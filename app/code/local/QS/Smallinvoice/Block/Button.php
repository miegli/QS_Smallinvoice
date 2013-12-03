<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/10/13
 * Time: 5:12 PM
 * To change this template use File | Settings | File Templates.
 */
 class QS_Smallinvoice_Block_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('*/importcustomer/import');
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Import')
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();
        return $html;
    }
}
