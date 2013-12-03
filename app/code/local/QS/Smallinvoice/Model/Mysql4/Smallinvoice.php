<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/17/13
 * Time: 12:04 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Mysql4_Smallinvoice extends Mage_Core_Model_Mysql4_Abstract {

    public function _construct() {
        $this->_init('qs_smallinvoice/smallinvoice', 'smallinvoice_id');
    }
}