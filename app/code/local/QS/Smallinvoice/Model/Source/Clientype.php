<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/1/13
 * Time: 12:58 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Source_Clientype
{
	public static function toOptionArray()
	{
		$list = array(
					"2" => Mage::Helper('qs_smallinvoice')->__('Private client'),
					"1" => Mage::Helper('qs_smallinvoice')->__('Company client'),
					);

		return ($list);
	}
}

