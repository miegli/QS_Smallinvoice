<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/4/13
 * Time: 2:41 PM
 * To change this template use File | Settings | File Templates.
 */
 
class QS_Smallinvoice_Model_Receipts_Observer extends Mage_Core_Model_Abstract {

        protected $_catalogConfig;

        public function __construct()
        {
            parent::__construct();
            $this->_catalogConfig = Mage::helper('qs_smallinvoice')->getConfigList();
        }

        protected function _getDataReceipts($invoice)
        {

            $customer = Mage::getModel('customer/customer')->reset()->load($invoice->getCustomerId());

            $smallInvoiceCustomerId = $customer->getSmallinvoiceCustomerid();
            $smallInvoiceMainaddrId = $customer->getSmallinvoiceMainaddrid();

            $items = $invoice->getAllItems();

            $positions[] = array();
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
                $positions[$i]['discount'] = '';
                $i++;
              }
            }

             $shipping  = array(
                'type'=>1,
                'name'=>'Shipping & Handling',
                'description'=>'',
                'cost'=>$invoice->getData('shipping_amount'),
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
                    'cost'=>$invoice->getData('tax_amount'),
                    'unit'=>$this->_catalogConfig['productunit'],
                    'amount'=>1,
                    'vat'=>0,
                    'discount'=>''
            );

            $positions[] = $tax;

            if(abs($invoice->getData('discount_amount'))>0) {
                $discount = array(
                    'type'=>1,
                    'name'=>'Discount',
                    'description'=>$invoice->getData('discount_description'),
                    'cost'=>(0-$invoice->getData('discount_amount')),//must be negative
                    'unit'=>$this->_catalogConfig['productunit'],
                    'amount'=>1,
                    'vat'=>0,
                    'discount'=>''
                );

                $positions[] = $discount;
            }
            
            $storeId = $invoice->getStoreId();
            $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
            $lang = explode('_',$localeCode);

            $dataReceipts = array(
              "client_id" => $smallInvoiceCustomerId,
              "client_address_id" => $smallInvoiceMainaddrId,
              "currency" => $invoice->getData('order_currency_code'),
              "title" => $this->_catalogConfig['titlereceipt'],
              "date" => date('Y-m-d'),
              "introduction"=>"",
              "conditions"=>"",
              "language"=>$lang[0],
              "vat_included"=>0,//tax_amount
              "positions"=> $positions
            );

            return $dataReceipts;
        }

        public function invoiceSaveAfter(Varien_Event_Observer $observer)
        {
            $invoice = $observer->getEvent()->getInvoice();
            $dataReceipts = json_encode($this->_getDataReceipts($invoice));
            $customerid = $invoice->getCustomerId();

            Mage::helper('qs_smallinvoice')->getClient('receipt','add', null, $dataReceipts, null, null, $customerid);
        }

}