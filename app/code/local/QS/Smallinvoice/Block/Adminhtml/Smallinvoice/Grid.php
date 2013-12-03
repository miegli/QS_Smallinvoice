<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/18/13
 * Time: 6:04 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Block_Adminhtml_Smallinvoice_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('smallinvoice_id');
        // This is the primary key of the database
        $this->setDefaultSort('smallinvoice_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('QS_Smallinvoice_Model_Smallinvoice')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('smallinvoice_id', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'smallinvoice_id',
        ));

        $this->addColumn('api_object', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('api_object'),
            'align'     =>'left',
            'width'     => '80px',
            'index'     => 'api_object',
        ));

        $this->addColumn('object_action', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('object_action'),
            'align'     =>'left',
            'width'     => '80px',
            'index'     => 'object_action',
        ));

        $this->addColumn('object_id', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('object_id'),
            'align'     =>'left',
            'width'     => '80px',
            'index'     => 'object_id',
        ));

        $this->addColumn('data_client', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('data_client'),
            'align'     =>'left',
            'index'     => 'data_client',
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('Creation Time'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'created_time',
        ));

        $this->addColumn('message', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('Message'),
            'align'     => 'left',
            
            'index'     => 'message',
        ));

        $this->addColumn('websiteid', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('websiteid'),
            'align'     => 'left',
            'width'     => '10px',
            'index'     => 'websiteid',
        ));

        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('product sku'),
            'align'     => 'left',
            'width'     => '20px',
            'index'     => 'product_sku',
        ));

        $this->addColumn('customerid', array(
            'header'    => Mage::helper('qs_smallinvoice')->__('customer id'),
            'align'     => 'left',
            'width'     => '20px',
            'index'     => 'customerid',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction(){
        $this->setMassactionIdField('smallinvoice_id');
        $this->getMassactionBlock()->setFormFieldName('smallinvoice_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> $this->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => $this->__('Are you sure?')
        ));
        return $this;
    }


}
