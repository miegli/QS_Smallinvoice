<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/11/13
 * Time: 4:33 PM
 * To change this template use File | Settings | File Templates.
 */
 class QS_Smallinvoice_Adminhtml_ImportproductsController extends Mage_Adminhtml_Controller_Action {

      public function importAction() {

         Mage::register('catalog_product_save_before',true);
          
         $productObserver = Mage::getModel('QS_Smallinvoice_Model_Catalog_Observer');

         set_time_limit(0);
         ignore_user_abort(true);

         $products = Mage::getModel('catalog/product')
                 ->getCollection()
                 ->addAttributeToSelect('*');

         foreach($products as $product) {
             $product = $product->load($product->getId());

             $smallinvoiceProductId = $product->getIsnew();

             if(empty($smallinvoiceProductId)or($smallinvoiceProductId==0)){
                 $productObserver->addNewProduct($product);
                 $product->save();
             }
         }

         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Items was successfully imported'));

         $this->_redirect('*/system_config/', array('_current' => true));

      }

      public function clearAction() {
         Mage::register('catalog_product_save_before',true);

         set_time_limit(0);
         ignore_user_abort(true);

         $products = Mage::getModel('catalog/product')
                 ->getCollection()
                 ->addAttributeToSelect('*');

         foreach($products as $product) {
              $product = $product->load($product->getId());
              $product->setIsnew('0');
              $product->save();
         }

         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('All smallinvoice id cleared'));

         $this->_redirect('*/system_config/', array('_current' => true));
      }

 }
