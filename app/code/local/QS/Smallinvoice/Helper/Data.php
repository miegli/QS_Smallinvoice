<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    QS
 * @package     QS_Smallinvoice
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @generator   http://www.mgt-commerce.com/kickstarter/ Mgt Kickstarter
 */

class QS_Smallinvoice_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigList() {
        $configList = array(
            'token' =>Mage::getStoreConfig('qs/smallinvoice/token'),
            'country'=>Mage::getStoreConfig('qs/smallinvoice_customer/country'),
            'code'=>Mage::getStoreConfig('qs/smallinvoice_customer/code'),
            'language'=>Mage::getStoreConfig('qs/smallinvoice_customer/language'),
            'gender'=>Mage::getStoreConfig('qs/smallinvoice_customer/gender'),
            'clientype'=>Mage::getStoreConfig('qs/smallinvoice_customer/clientype'),
            'productunit'=>Mage::getStoreConfig('qs/smallinvoice_products/productunit'),
			'producttype'=>Mage::getStoreConfig('qs/smallinvoice_products/producttype'),
            'due'=>Mage::getStoreConfig('qs/smallinvoice_invoice/due'),
            'title'=>Mage::getStoreConfig('qs/smallinvoice_invoice/title'),
            'titlereceipt'=>Mage::getStoreConfig('qs/smallinvoice_invoice/titlereceipt'),
            'apiurl' =>Mage::getStoreConfig('qs/smallinvoice/api')
        );
        
        return $configList;
    }

    public function getClient($api_object, $object_action, $object_id=null, $dataClient=null, $websiteId=null, $productsku=null, $customerid=null) {

        $config = $this->getConfigList();
        $https = $config['apiurl'];

        $configList = $this->getConfigList();

        if($object_id) {
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.'id/'.$object_id.'/'.'token/'.$configList['token'].'/';
        }else{
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.$object_id.'token/'.$configList['token'].'/';
        }

        $model = Mage::getModel('QS_Smallinvoice_Model_Smallinvoice');
        $collection = $model->getCollection()->count();
        
        try {

            $client = new Zend_Http_Client($apiUrll, array(
                'adapter'      => 'Zend_Http_Client_Adapter_Socket',
                'ssltransport' => 'tls',
                'maxredirects' => 0,
                'timeout'      => 30));
            
            if($collection>0) {
                $data = array('api_object'=>$api_object,'object_action'=>$object_action, 'object_id'=>$object_id,
                'data_client'=>$dataClient, 'created_time'=>date('Y-m-d H:i:s'), 'websiteid'=>$websiteId, 'product_sku'=>$productsku,
                'customerid'=>$customerid);

                $model->setData($data);
                $model->save();

                return null;
            }else{
                if($dataClient) {
                    $client->setMethod(Zend_Http_Client::POST);
                    $client->setParameterPost('data', $dataClient);
                    $response = $client->request('POST');
                }else{
                    $client->setMethod();
                    $response = $client->request();
                }
            }
            
            $responceBody = json_decode($response->getBody(),true);

            if($responceBody['error']==true) {
                  $data = array('api_object'=>$api_object,'object_action'=>$object_action, 'object_id'=>$object_id,
                'data_client'=>$dataClient, 'created_time'=>date('Y-m-d H:i:s'),'message'=>$responceBody['errormessage'], 'websiteid'=>$websiteId,
                'product_sku'=>$productsku, 'customerid'=>$customerid);
                $model->setData($data);
                $model->save();
            }

                return $response;
            
        }catch(Exception $e){
                Mage::logException($e);
                Mage::log($e->getMessage(),null,'SmallInvoiceErrors.log');

                $data = array('api_object'=>$api_object,'object_action'=>$object_action, 'object_id'=>$object_id,
                'data_client'=>$dataClient, 'created_time'=>date('Y-m-d H:i:s'),'message'=>$e->getMessage(), 'websiteid'=>$websiteId,
                'product_sku'=>$productsku, 'customerid'=>$customerid);
                $model->setData($data);
                $model->save();
        }

        return null;
    }

    public function cronClient($api_object, $object_action, $object_id=null, $dataClient=null, $websiteId=null, $productsku=null, $customerid=null) {

        $config = $this->getConfigList();
        $https = $config['apiurl'];

        $configList = $this->getConfigList();

         if(($api_object=='client')and($object_action == 'add')) {
             $dataClient2 = json_decode($dataClient,true);
                             $customerEmail = $dataClient2['email'];
                             $customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($customerEmail);
                             $smallinvoiceCustomerid = $customer->getSmallinvoiceCustomerid();

             if((($smallinvoiceCustomerid=='0')or(empty($smallinvoiceCustomerid)))) {
                 //NOTHING TO DO
             }else{
                 $object_action = 'edit';
                 $object_id = $smallinvoiceCustomerid;
             }
         }

         if((($api_object=='catalog')and($object_action=='add'))) {
              $product = Mage::getModel('catalog/product');
              $product->load($product->getIdBySku($productsku));

              $productSmallinvoiceId = $product->getIsnew();

              if(($productSmallinvoiceId=='0')or(empty($productSmallinvoiceId))){
                //NOTHING TO DO
              }else{
                  $object_action = 'edit';
                  $object_id = $productSmallinvoiceId;
              }
         }

         if((($api_object=='invoice')and($object_action=='add'))or(($api_object=='receipt')and($object_action=='add'))) {
              $dataClientInvoice = json_decode($dataClient,true);

              $client_id = $dataClientInvoice['client_id'];
              $client_address_id = $dataClientInvoice['client_address_id'];

              if((empty($client_id))or(empty($client_address_id))){
                  if($customerid) {
                      $customer = Mage::getModel('customer/customer')->load($customerid);
                      $dataClientInvoice['client_id'] =  $customer->getSmallinvoiceCustomerid();
                      $dataClientInvoice['client_address_id'] = $customer->getSmallinvoiceMainaddrid();
                  }

              }
         }

        if($object_id) {
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.'id/'.$object_id.'/'.'token/'.$configList['token'].'/';
        }else{
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.$object_id.'token/'.$configList['token'].'/';
        }

        try {
            $client = new Zend_Http_Client($apiUrll, array(
                    'adapter'      => 'Zend_Http_Client_Adapter_Socket',
                    'ssltransport' => 'tls',
                    'maxredirects' => 0,
                    'timeout'      => 30));

            if(((($api_object=='invoice')and($object_action=='add'))or(($api_object=='receipt')and($object_action=='add')))and(count($dataClientInvoice)>0)){
                    $dataClientInvoice = json_encode($dataClientInvoice);
                    $client->setMethod(Zend_Http_Client::POST);
                    $client->setParameterPost('data', $dataClientInvoice);
                    $response = $client->request('POST');
            }elseif($dataClient) {
                    $client->setMethod(Zend_Http_Client::POST);
                    $client->setParameterPost('data', $dataClient);
                    $response = $client->request('POST');
            }else{
                    $client->setMethod();
                    $response = $client->request();
            }

            $responseBody = json_decode($response->getBody(), true);

            if(($api_object=='client')and($object_action=='add')and($responseBody['id'])) {

                $smallinvoiceCustomerid = $responseBody['id'];
                $observerCustomer = Mage::getModel('QS_Smallinvoice_Model_Customer_Observer');
                $dataClient = json_decode($dataClient,true);
                $customerEmail = $dataClient['email'];
                $customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($customerEmail);

                        $customer->setSmallinvoiceCustomerid($smallinvoiceCustomerid);
                        $customerMainAddressId = $observerCustomer->getCustomerMainAddressIdCron($smallinvoiceCustomerid);
                        if(!empty($customerMainAddressId)) {
                            $customer->setSmallinvoiceMainaddrid($customerMainAddressId);
                        }else{
                            Mage::log('Can not save main addresss id for customer: '.$customer->getId(),null,'SmallInvoiceErrors.log');
                        }

                $customer->save();
            }

            if((($api_object=='catalog')and($object_action=='add')and($responseBody['id']))) {

                $productSmallinvoiceId = $responseBody['id'];
                $product = Mage::getModel('catalog/product');
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                $product->load($product->getIdBySku($productsku));
                $product->setIsnew($productSmallinvoiceId);
                $product->save();
            }

          return true;

        }catch(Exception $e){
                Mage::logException($e);
                Mage::log($e->getMessage(),null,'SmallInvoiceErrors.log');
        }

    }

    public function getApiResponce($api_object, $object_action, $object_id=null, $dataClient=null) {

        $config = $this->getConfigList();
        $https = $config['apiurl'];

        $configList = $this->getConfigList();

        if($object_id) {
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.'id/'.$object_id.'/'.'token/'.$configList['token'].'/';
        }else{
            $apiUrll = $https.'/'.$api_object.'/'.$object_action.'/'.$object_id.'token/'.$configList['token'].'/';
        }

        try {

            $client = new Zend_Http_Client($apiUrll, array(
                'adapter'      => 'Zend_Http_Client_Adapter_Socket',
                'ssltransport' => 'tls',
                'maxredirects' => 0,
                'timeout'      => 30));

                if($dataClient) {
                    $client->setMethod(Zend_Http_Client::POST);
                    $client->setParameterPost('data', $dataClient);
                    $response = $client->request('POST');
                }else{
                    $client->setMethod();
                    $response = $client->request();
                }

            return $response;

        }catch(Exception $e){
                Mage::logException($e);
                Mage::log($e->getMessage(),null,'SmallInvoiceErrors.log');
        }

        return null;
    }
}