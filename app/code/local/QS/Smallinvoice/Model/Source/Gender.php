<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/1/13
 * Time: 12:55 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Source_Gender
{
	public static function toOptionArray()
	{
		$list = array(
					"1" => Mage::Helper('qs_smallinvoice')->__('Male'),
					"2" => Mage::Helper('qs_smallinvoice')->__('Female'),
					);

		return ($list);
	}
}
