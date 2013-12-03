<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/3/13
 * Time: 2:28 PM
 * To change this template use File | Settings | File Templates.
 */

class QS_Smallinvoice_Model_Catalog_Observer extends Mage_Core_Model_Abstract
{
    protected $_catalogConfig;

    public function __construct()
    {
        parent::__construct();

        $this->_catalogConfig = Mage::helper('qs_smallinvoice')->getConfigList();

    }
    protected function _getCategoriesString($product)
    {
        $cats = $product->getCategoryIds();
        $str = '';
        $count = count($cats);
        foreach ($cats as $key => $category_id):
            $_cat = Mage::getModel('catalog/category')->load($category_id);
            if (($key + 1) == $count) {
                $str .= $_cat->getName();
            } else {
                $str .= $_cat->getName() . ', ';
            }

        endforeach;

        return $str;
    }

    public function productSaveBefore(Varien_Event_Observer $observer)
    {
         if(Mage::registry('catalog_product_save_before')){
             return $this; //this method has already been executed once in this request
         }

        $product = $observer->getProduct();

        $smallInvoiceId = $product->getIsnew();

        if ((empty($smallInvoiceId))or($smallInvoiceId == 0)) {
            $this->addNewProduct($product);
        } else {
            $this->_editProduct($product, $smallInvoiceId);
        }

    }

    protected function _getProductArray($product)
    {
        if($product->getId()) {
            $iDefaultStoreId = Mage::app()
            ->getWebsite()
            ->getDefaultGroup()
            ->getDefaultStoreId();

            $product = Mage::getModel('catalog/product')
            ->setStoreId($iDefaultStoreId)
            ->load($product->getId());
        }

        try {
            $productType = intval($this->_catalogConfig['producttype']);
            $productNumber = intval($product->getId());
            $productUnit = intval($this->_catalogConfig['productunit']);
            $productName = $product->getName();
            $productDescription = $product->getDescription();
            $productCategory = $this->_getCategoriesString($product);
            $productVat = $product->getData('tax_percent');
            if ($productVat == '') {
                $productVat = 0;
            }
            $productCostPerUnit = $product->getFinalPrice();
            if (Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true) == $product->getFinalPrice()) {
                $productCostVatIncluded = 0;
            } else {
                $productCostVatIncluded = 1;
            }

            $addProductArray = array(
                'type' => $productType,
                'number' => $productNumber,
                'unit' => $productUnit,
                'name' => $productName,
                'description' => $productDescription,
                'category' => $productCategory,
                'vat' => $productVat,
                'cost_per_unit' => $productCostPerUnit,
                'cost_vat_included' => $productCostVatIncluded
            );

            $jsonAddProductArray = json_encode($addProductArray);
           
            return $jsonAddProductArray;

        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'SmallInvoiceErrors.log');
        }
    }

    protected function _updateProduct($product, $api_object, $object_action, $object_id = null)
    {
        try {
            $jsonAddProductArray = $this->_getProductArray($product);

            $productSku = $product->getSku();

            $response = Mage::helper('qs_smallinvoice')->getClient($api_object, $object_action, $object_id, $jsonAddProductArray, null, $productSku);

             if(!$response){
                return 0;
             }else{
                if ($response->isSuccessful() && $response->getStatus() == 200) {
                    $responseBody = json_decode($response->getBody(), true);
                    if (isset($responseBody['id'])) {
                        $product->setIsnew($responseBody['id']);
                    }
                }else{
                Mage::log($response->getBody(),null,'SmallInvoiceErrorsProductsAddError.log');
                }
             }

        } catch (Exception $e) {
             Mage::log($e->getMessage(),null,'SmallInvoiceErrors.log');
        }

    }


    public function addNewProduct($product)
    {
        $this->_updateProduct($product, 'catalog', 'add');
    }

    protected function _editProduct($product, $smallInvoiceId)
    {
        $this->_updateProduct($product, 'catalog', 'edit', $smallInvoiceId);
    }

    public function deleteProduct(Varien_Event_Observer $observer)
    {
        try {
            $product = $observer->getEvent()->getProduct();
            if ($product && $product->getId() && $product->getIsnew()) {
                $productId = $product->getIsnew();
                Mage::helper('qs_smallinvoice')->getClient('catalog', 'delete', $productId);
            }
        }catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            mage::log($e->getMessage());
        }
    }
}
 
