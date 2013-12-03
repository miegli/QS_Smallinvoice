<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/18/13
 * Time: 5:47 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Block_Adminhtml_Smallinvoice extends Mage_Adminhtml_Block_Widget_Grid_Container{
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_smallinvoice';
        $this->_blockGroup = 'qs_smallinvoice';
        $this->_headerText = Mage::helper('qs_smallinvoice')->__('Queries log');
        parent::__construct();
        $this->removeButton('add');
    }
}
