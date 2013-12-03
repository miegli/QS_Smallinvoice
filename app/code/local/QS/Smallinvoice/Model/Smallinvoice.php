<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/17/13
 * Time: 3:37 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Smallinvoice extends Mage_Core_Model_Abstract {
    public function _construct()
    {
        parent::_construct();
        $this->_init('qs_smallinvoice/smallinvoice');
    }
}
 
