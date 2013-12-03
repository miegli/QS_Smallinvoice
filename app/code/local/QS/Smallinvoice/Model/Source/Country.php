<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/9/13
 * Time: 2:37 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Source_Country
{
    public function toOptionArray()
    {
        $collection = Mage::getModel('directory/country')->getCollection();
        $list = array();

        foreach ($collection as $country)    {
            $cid = $country->getId();
            $cname = $country->getName();
            $list[$cid] = $cname;
        }

        return $list;
    }
    
}
 
