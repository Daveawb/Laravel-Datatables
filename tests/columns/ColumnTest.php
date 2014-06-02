<?php

class ColumnTest extends DatatablesTestCase {
	
	public function testConstructorTakesFieldsAndSettings()
	{
		$column = new Daveawb\Datatables\Columns\Column($fields = array(
				"id"
			),
			$settings = array(
				"mDataProp" => 0,
				"bSearchable" => false,
				"bSortable" => true,
				"bRegex" => false,
				"sSearch" => ""
			)
		);
		
		$this->assertEquals($fields, $this->getProperty($column, "fields"));
		$this->assertEquals($settings, $this->getProperty($column, "attributes"));
		
		return $column;
	}
	
	/**
	 * @depends testConstructorTakesFieldsAndSettings
	 */
	public function testGetAndSetAttributes($column)
	{
		$this->assertEquals(0, $column->mDataProp);
		
		$column->mDataProp = 1;
		
		$this->assertEquals(1, $column->mDataProp);
	}
}
