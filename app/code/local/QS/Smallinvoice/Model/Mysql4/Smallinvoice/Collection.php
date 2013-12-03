<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/17/13
 * Time: 3:11 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Mysql4_Smallinvoice_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        $this->_init('qs_smallinvoice/smallinvoice');
    }
}
 
