<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/2/13
 * Time: 4:27 PM
 * To change this template use File | Settings | File Templates.
 */
 class QS_Smallinvoice_Model_Invoice_Observer extends Mage_Core_Model_Abstract
{
      protected $_catalogConfig;

      public function __construct()
      {
            parent::__construct();

            $this->_catalogConfig = Mage::helper('qs_smallinvoice')->getConfigList();

      }

      protected function _getInvoiceData($order)
      {
          $customer = Mage::getModel('customer/customer')->reset()->load($order->getCustomerId());
          $smallInvoiceCustomerId = $customer->getSmallinvoiceCustomerid();
          $smallInvoiceMainaddrId = $customer->getSmallinvoiceMainaddrid();

          $items = $order->getAllItems();
          $positions[] = array( );
          $i=0;

          foreach ($items as $item)  {
              if($item->getData('parent_item_id')) {
                  //NOTHING TO DO
              }else{
                $positions[$i]['type'] = $this->_catalogConfig['producttype'];
                $positions[$i]['name'] = $item->getData('name');
                $positions[$i]['description'] = $item->getData('description');
                $positions[$i]['cost'] = $item->getData('price');
                $positions[$i]['unit'] = $this->_catalogConfig['productunit'];// 1 - Hour from configuration, 7-Piece
                $positions[$i]['amount'] = $item->getData('qty_ordered');
                $positions[$i]['vat'] = 0;
                //$positions[$i]['discount'] = $item->getData('discount_amount');
                $positions[$i]['discount'] = '';
                $i++;
              }
          }
          
            $shipping  = array(
                'type'=>1,
                'name'=>'Shipping & Handling',
                'description'=>'',
                'cost'=>$order->getData('shipping_amount'),
                'unit'=>$this->_catalogConfig['productunit'],
                'amount'=>1,
                'vat'=>0,
                'discount'=>''
            );

            $positions[] = $shipping;

            $tax = array(
                    'type'=>1,
                    'name'=>'Tax',
                    'description'=>'',
                    'cost'=>$order->getData('tax_amount'),
                    'unit'=>$this->_catalogConfig['productunit'],
                    'amount'=>1,
                    'vat'=>0,
                    'discount'=>''
            );
           
            $positions[] = $tax;

           if(abs($order->getData('discount_amount'))>0) {
                $discount = array(
                    'type'=>1,
                    'name'=>'Discount',
                    'description'=>'Coupon code: ('.$order->getData('discount_description').')',
                    'cost'=>$order->getData('discount_amount'),
                    'unit'=>$this->_catalogConfig['productunit'],
                    'amount'=>1,
                    'vat'=>0,
                    'discount'=>''
                );

                $positions[] = $discount;
            }
          
            $storeId = $order->getStoreId();
            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $lang = explode('_',$localeCode);

            $due = $this->_catalogConfig['due'];
            $dataInvoice = array(
              "client_id" => $smallInvoiceCustomerId,
              "client_name" => $customer->getName(),
              "client_address_id" => $smallInvoiceMainaddrId,
              "currency" => $order->getData('order_currency_code'),
              "title" => $this->_catalogConfig['title'],
              "date" => date('Y-m-d'),
              "due" => date('Y-m-d', strtotime('+'.$due.'days', strtotime(date('Y-m-d')))),
              "account_id" =>0,
              "language"=>$lang[0],
              "vat_included"=>0,
              "positions"=> $positions
          );

          return $dataInvoice;
      }

      public function SalesOrderSaveAfter(Varien_Event_Observer $observer)
      {
         if(Mage::registry('sales_order_observer_executed')){
            return $this; //this method has already been executed once in this request
         }

         $order = $observer->getEvent()->getOrder();
         $order = Mage::getModel('sales/order')->load($order->getId());
         
         $customerid = $order->getCustomerId();

         $dataInvoice = json_encode($this->_getInvoiceData($order));

         Mage::helper('qs_smallinvoice')->getClient('invoice','add', null, $dataInvoice, null, null, $customerid);

       

         Mage::register('sales_order_observer_executed',true); 

      }
}