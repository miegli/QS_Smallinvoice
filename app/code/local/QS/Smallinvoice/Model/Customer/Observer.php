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

class QS_Smallinvoice_Model_Customer_Observer extends Mage_Core_Model_Abstract
{
    protected $_catalogConfig;

    public function __construct()
    {
        parent::__construct();

        $this->_catalogConfig = Mage::helper('qs_smallinvoice')->getConfigList();

    }

    public function editSmallinvoiceCustomer($customer)
    {

        $dataClient = json_encode($this->getCustomerInfo($customer));

        $websiteId = $customer->getWebsiteId();
        $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();

        $response = Mage::helper('qs_smallinvoice')->getClient('client','edit',$smallinvoiceCustomerId, $dataClient, $websiteId);

        if(!$response){
             return 0;
        }else{
            if ($response->isSuccessful() && $response->getStatus() == 200) {
                        try {
                             $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();

                             $customerMainAddressId = $this->getCustomerMainAddressId($smallinvoiceCustomerId);
                             if(!empty($customerMainAddressId)) {
                                 $customer->setSmallinvoiceMainaddrid($customerMainAddressId);
                             }else{
                                 Mage::log('Can not save main addresss id for customer: '.$customerMainAddressId,null,'SmallInvoiceErrors.log');
                             }
                             return $response->getBody();
                        } catch (Exception $e) {
                             Mage::logException($e);
                        }
            }
        }
    }

    public function customerSaveAddress(Varien_Event_Observer $observer)
    {
        if(Mage::registry('customer_save_observer_executed')){
            return $this; //this method has already been executed once in this request
        }
        
        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();
        $websiteId = $customer->getWebsiteId();

        $dataClient = json_encode($this->getCustomerInfo($customer));

        $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();

        $response = Mage::helper('qs_smallinvoice')->getClient('client','edit',$smallinvoiceCustomerId, $dataClient, $websiteId);

        if(!$response){
            return 0;
        }else{
            if ($response->isSuccessful() && $response->getStatus() == 200) {
                try{
                    $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();
                    $customerMainAddressId = $this->getCustomerMainAddressId($smallinvoiceCustomerId);
                        if(!empty($customerMainAddressId)) {
                            $customer->setSmallinvoiceMainaddrid($customerMainAddressId);
                        }else{
                            Mage::log('Can not save main addresss id for customer: '.$customerMainAddressId,null,'SmallInvoiceErrors.log');
                        }
                    return $response->getBody();
                }catch (Exception $e){
                    Mage::logException($e);
                }
            }
        }

    }

    public function getCustomerInfo($customer)
    {

        $customerType = $this->_catalogConfig['clientype'];//client type

        $customerGender = $customer->getGender();
        $customerGender = empty($customerGender) ? $this->_catalogConfig['gender'] : $customer->getGender();//Gender
        if($customerGender==123) {
            $customerGender =1;
        }
        if($customerGender==124) {
            $customerGender =2;
        }

        $storeId = $customer->getStoreId();
        $localeCode = Mage::getStoreConfig('general/locale/code', $storeId);
        $atrr = explode('_', $localeCode);

        $customerLanguage = $atrr[0];

        $customerName = $customer->getName();
        $customerAddition = $customer->getMiddlename();
        $customerAddition = empty($customerAddition) ? '' : $customerAddition;
        $customerEmail = $customer->getEmail();

        $iDefaultStoreId = Mage::app()
            ->getWebsite()
            ->getDefaultGroup()
            ->getDefaultStoreId();

        $custmerStoreId = Mage::app()->getStore()->getCode();
        $customerWebsite = empty($custmerStoreId) ? $iDefaultStoreId : $custmerStoreId;

        $customerPhone ='';
        $customerFax = '';

        $customerVat_id = $customer->getTaxvat();
        $customerVat_id = empty($customerVat_id) ? '' : $customerVat_id;//tax vat
        $customerNotes = '';

        $addressesCustomer = $customer->getAddresses();

        $addresses[] = Array ();
        $i=0;
        $flag=0;
        if(count($addressesCustomer)>0) {
            $defaultBilling = $customer->getDefaultBilling();
            foreach ($addressesCustomer as $adr) {
                  if($adr->getData('entity_id') == $defaultBilling){
                    $addresses[$i]['primary'] = 1;
                    $flag=1;
                    $customerPhone = $adr->getTelephone();
                    $customerPhone = empty($customerPhone) ? ' ' : $customerPhone;
                    $customerFax = $adr->getFax();
                    if(!$customerFax)
                        $customerFax='';

                }else{
                    $addresses[$i]['primary'] = 0;
                }

                $streets = $adr->getStreet();
                $addresses[$i]['street'] = $streets[0];
                $addresses[$i]['streetno']= ' ';

                if(count($streets)>1) {
                    $addresses[$i]['street2'] = $streets[1];
                }else{
                    $addresses[$i]['street2'] = ' ';
                }

                $customerCity = $adr->getCity();
                $addresses[$i]['city']= empty($customerCity) ? ' ' : $customerCity;
                $customerCode = $adr->getPostcode();
                $addresses[$i]['code'] = empty($customerCode) ? $this->_catalogConfig['code'] : $customerCode;
                $customerCountry = $adr->getCountry();
                $addresses[$i]['country'] = empty($customerCountry) ? $this->_catalogConfig['country'] : $customerCountry;
                $customerPhone = $adr->getTelephone();
                $addresses[$i]['customerPhone'] = empty($customerPhone) ? ' ' : $customerPhone;
                $customerFax = $adr->getFax();
                if(!$customerFax)
                        $customerFax='';
                $i++;

                }
        }else {//defaults
            $defaultCode = $this->_catalogConfig['code'];

            $addresses[0]['primary'] = 1;
            $addresses[0]['street']=' ';
            $addresses[0]['streetno']=' ';
            $addresses[0]['street2']=' ';
            $addresses[0]['city']=' ';
            $addresses[0]['code']= empty($defaultCode) ? '00000' : $defaultCode;
            $addresses[0]['country']= $this->_catalogConfig['country'];
            $addresses[0]['customerPhone']='';
            $addresses[0]['customerFax']='';
        }

        if($flag==0) {
            $addresses[0]['primary'] = 1;//At least one address should be the main
            $customerPhone = $addresses[0]['customerPhone'];
           // $customerFax = $addresses[0]['customerFax'];
            if(!$customerFax)
                        $customerFax='';
        }

        $dataClient = array(
            "type"=>$customerType,
            "gender"=>$customerGender,
            "name"=>$customerName,
            "addition"=>$customerAddition,
            "vat_id"=>$customerVat_id,
            "language"=>$customerLanguage,
            "phone"=>$customerPhone,
            "fax"=>$customerFax,
            "email"=>$customerEmail,
            "website"=>$customerWebsite,
            "notes"=>$customerNotes,
            "addresses"=>$addresses
        );

        return $dataClient;
    }

    public function addSmallinvoiceCustomer($customer)
    {
        $dataClient = json_encode($this->getCustomerInfo($customer));

        $websiteId = $customer->getWebsiteId();

        if(empty($websiteId)) $websiteId = Mage::app()->getStore()->getWebsiteId();
        
        $response = Mage::helper('qs_smallinvoice')->getClient('client','add', null, $dataClient, $websiteId);

        if(!$response){
             return 0;
        }else{
            if ($response->isSuccessful() && $response->getStatus() == 200) {

                    try {
                        $responseBody = json_decode($response->getBody(), true);
                        $smallinvoiceCustomerid = $responseBody['id'];
                        $customer->setSmallinvoiceCustomerid($smallinvoiceCustomerid);
                        $customerMainAddressId = $this->getCustomerMainAddressId($smallinvoiceCustomerid);
                        if(!empty($customerMainAddressId)) {
                            $customer->setSmallinvoiceMainaddrid($customerMainAddressId);
                        }else{
                            Mage::log('Can not save main addresss id for customer: '.$customerMainAddressId,null,'SmallInvoiceErrors.log');
                        }
                        return $response->getBody();
                    }catch (Exception $e){
                        Mage::logException($e);
                    }
            }
        }
    }

    public function getCustomerMainAddressId($customerId)
    {
        $response = Mage::helper('qs_smallinvoice')->getClient('client','get', $customerId);
        if(!$response){
             return 0;
        }else{
            $responseArray = json_decode($response->getBody(),true);
            if ($response->isSuccessful() && $response->getStatus() == 200) {
                 return $responseArray['item']['main_address_id'];
            }
        }
    }

    public function getCustomerMainAddressIdCron($customerId)
    {
        $response = Mage::helper('qs_smallinvoice')->getApiResponce('client','get', $customerId);
        if(!$response){
             return 0;
        }else{
            $responseArray = json_decode($response->getBody(),true);
            if ($response->isSuccessful() && $response->getStatus() == 200) {
                 return $responseArray['item']['main_address_id'];
            }
        }
    }

    public function customerDelete(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();
        
        if($smallinvoiceCustomerId) {
           Mage::helper('qs_smallinvoice')->getClient('client','delete',$smallinvoiceCustomerId);
        }else{
           Mage::log('Customer smalinvoiceid not defined',null,'SmallInvoiceErrors.log');
        }
    }

    public function customerSaveBefore(Varien_Event_Observer $observer)
    {
        if(Mage::registry('customer_save_observer_executed')){
            return $this; //this method has already been executed once in this request
        }

        $customer = $observer->getEvent()->getCustomer();
        $smallinvoiceCustomerId = $customer->getSmallinvoiceCustomerid();

            if(empty($smallinvoiceCustomerId)or($smallinvoiceCustomerId==0)) {
                $this->addSmallinvoiceCustomer($customer);
            }else{
                $this->editSmallinvoiceCustomer($customer);
            }


        Mage::register('customer_save_observer_executed',true);
    }

}