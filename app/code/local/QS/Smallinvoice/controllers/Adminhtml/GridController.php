<?php

class QS_Smallinvoice_Adminhtml_GridController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
       {
           $this->loadLayout();
           $this->_setActiveMenu('qs_smallinvoice')->_title($this->__('Smallinvoice'));
           $this->_addContent($this->getLayout()->createBlock('qs_smallinvoice/adminhtml_smallinvoice', 'qs_smallinvoice.smallinvoice'));
           $this->renderLayout();
       }

    public function massDeleteAction(){
        $fileIds = $this->getRequest()->getParam('smallinvoice_id');
        if(!is_array($fileIds)){
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select files.'));
        } else {
            try {
                $model = Mage::getModel('QS_Smallinvoice_Model_Smallinvoice');
                foreach ($fileIds as $id) {
                    $model->setId($id)->delete();

                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('qs_smallinvoice')->__(
                        'Total of %d record(s) were deleted.', count($fileIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
 
}