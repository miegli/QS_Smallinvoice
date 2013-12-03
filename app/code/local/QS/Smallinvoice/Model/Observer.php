<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/18/13
 * Time: 2:00 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Observer {

    public function cronDeal() {
       $model = Mage::getModel('QS_Smallinvoice_Model_Smallinvoice');
       $collection = $model->getCollection()->setOrder('smallinvoice_id', 'ASC');
       $count =$collection->count();

       if($count>0){
         foreach($collection as $item) {
           $response = Mage::helper('qs_smallinvoice')->cronClient($item->getApiObject(), $item->getObjectAction(),
                                                       $item->getObjectId(), $item->getDataClient(), $item->getWebsiteid(),
                                                       $item->getProductSku(), $item->getCustomerid());
           if($response){
              $model->setId($item->getSmallinvoiceId())->delete();
           }
         }
       }

    }
}
