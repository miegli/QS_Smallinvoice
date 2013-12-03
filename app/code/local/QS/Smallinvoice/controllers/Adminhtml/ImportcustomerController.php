<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/11/13
 * Time: 10:35 AM
 * To change this template use File | Settings | File Templates.
 */
 class QS_Smallinvoice_Adminhtml_ImportcustomerController extends Mage_Adminhtml_Controller_Action {

     public function importAction() {
         Mage::register('customer_save_observer_executed',true);

         set_time_limit(0);
         ignore_user_abort(true);
         
         $customerObserver = Mage::getModel('QS_Smallinvoice_Model_Customer_Observer');

         $collection = Mage::getModel('customer/customer')
              ->getCollection()
              ->addAttributeToSelect('*');

         foreach($collection as $customer) {
             $customer = $customer->load($customer->getId());
             $SmallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();

             if(empty($SmallinvoiceCustomerId)or($SmallinvoiceCustomerId=='0')){                
                 $customerObserver->addSmallinvoiceCustomer($customer);
                 $customer->setSuffix(''); //magic
                 $customer->save();
             }
         }
         
         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Items was successfully imported'));
         $this->_redirect('*/system_config/', array('_current' => true));
     }

     public function clearAction() {
        Mage::register('customer_save_observer_executed',true);

        set_time_limit(0);
        ignore_user_abort(true);

        $collection = Mage::getModel('customer/customer')
                 ->getCollection()
                ->addAttributeToSelect('*');

         foreach($collection as $customer) {
            $customer->setSmallinvoiceCustomerid('0');
            $customer->setSmallinvoiceMainaddrid('0');
            $customer->save();
         }

         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('All smallinvoice id cleared'));

         $this->_redirect('*/system_config/', array('_current' => true));
     }
 }