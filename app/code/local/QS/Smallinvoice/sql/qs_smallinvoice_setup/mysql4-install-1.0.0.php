<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS smallinvoice;

CREATE TABLE smallinvoice (
  smallinvoice_id int(11) unsigned NOT NULL auto_increment,
  api_object varchar(255) NOT NULL default '',
  object_action varchar(255) NOT NULL default '',
  object_id varchar(255) NULL default NULL,
  data_client MEDIUMTEXT NULL default '',
  created_time DATETIME NULL,
  message TEXT NULL,
  websiteid int(2) unsigned NULL,
  product_sku varchar(255) NULL default NULL,
  customerid int(11) unsigned NULL,
  PRIMARY KEY (smallinvoice_id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$entityTypeId     = $setup->getEntityTypeId('customer');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('customer', 'smallinvoice_customerid', array(
    'input'         => 'text',
    'type'          => 'int',
    'label'         => 'Customer id for smallinvoice',
    'default'       => 0,
    'visible'       => 0,
    'required'      => 0,
    'user_defined'  => 1,
));

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'smallinvoice_customerid',
 '999'  //sort_order
);

$oAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'smallinvoice_customerid');
$oAttribute->setData('used_in_forms', array('adminhtml_customer'));
$oAttribute->save();

///
$setup->addAttribute('customer', 'smallinvoice_mainaddrid', array(
    'input'         => 'text',
    'type'          => 'int',
    'label'         => 'Main address id for smallinvoice',
    'visible'       => 0,
    'required'      => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'smallinvoice_mainaddrid',
 '999'  //sort_order
);

$bAttribute = Mage::getSingleton('eav/config')->getAttribute('customer', 'smallinvoice_mainaddrid');
$bAttribute->setData('used_in_forms', array('adminhtml_customer'));
$bAttribute->save();

///product attribute

$entityTypeId     = $setup->getEntityTypeId('catalog_product');
$attributeSetId   = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute('catalog_product', 'isnew', array(
    'input'         => 'text',
    'type'          => 'int',
    'label'         => 'Product id for smallinvoice',
    'default'       => 0,
    'visible'       => 0,
    'required'      => 0,
    'user_defined' => 1,
));

$setup->addAttributeToGroup(
 $entityTypeId,
 $attributeSetId,
 $attributeGroupId,
 'isnew',
 '999'  //sort_order
);

$bAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'isnew');

$bAttribute->save();

$setup->endSetup();