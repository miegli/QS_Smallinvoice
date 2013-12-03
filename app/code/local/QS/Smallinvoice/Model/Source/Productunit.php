<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gikach
 * Date: 4/1/13
 * Time: 12:55 PM
 * To change this template use File | Settings | File Templates.
 */
class QS_Smallinvoice_Model_Source_Productunit
{
	public static function toOptionArray()
	{
		$list = array(
					"1" => Mage::Helper('qs_smallinvoice')->__('Hour'),
					"2" => Mage::Helper('qs_smallinvoice')->__('Day'),
					"3" => Mage::Helper('qs_smallinvoice')->__('Month'),
					"4" => Mage::Helper('qs_smallinvoice')->__('Quarter'),
					"5" => Mage::Helper('qs_smallinvoice')->__('Semester'),
					"6" => Mage::Helper('qs_smallinvoice')->__('Year'),
					"7" => Mage::Helper('qs_smallinvoice')->__('Piece'),
					"8" => Mage::Helper('qs_smallinvoice')->__('kg'),
					"9" => Mage::Helper('qs_smallinvoice')->__('Liters'),
					"10" => Mage::Helper('qs_smallinvoice')->__('m2'),
					"11" => Mage::Helper('qs_smallinvoice')->__('m3'),
					"12" => Mage::Helper('qs_smallinvoice')->__('km'),
					"13" => Mage::Helper('qs_smallinvoice')->__('Flat rate'),
					"14" => Mage::Helper('qs_smallinvoice')->__('-'),
					);

		return ($list);
	}
}