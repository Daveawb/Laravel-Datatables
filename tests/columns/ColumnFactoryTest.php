<?php

class ColumnFactoryTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);    
    }
    
    public function testCreateBuildsNewColumnIndexedByName()
    {
        $input = new Daveawb\Datatables\Columns\Input($this->app['request']);
        
        $factory = new Daveawb\Datatables\Columns\Factory($input, $this->app['validator']);
        
        $factory->create("id", 0);
        
        $this->assertInstanceOf("Daveawb\Datatables\Columns\Column", $this->getProperty($factory, "columns")[0]);
        
        return $factory;
    } 
    
    /**
     * @depends testCreateBuildsNewColumnIndexedByName
     */
    public function testGetColumnFromFactoryReturnsWithDataSet($factory)
	{
		$column = $factory->getColumn(0);
		
		$this->assertInstanceOf("Daveawb\Datatables\Columns\Column", $column);
		
		return $column;
	}
	
	/**
	 * @depends testGetColumnFromFactoryReturnsWithDataSet
	 */
    public function testColumnHasDataSetByFactory(Daveawb\Datatables\Columns\Column $column)
    {
        $this->assertEquals($column->name, 'id');
        $this->assertEquals($column->mDataProp, 0);
        $this->assertFalse($column->bSearchable);
        $this->assertFalse($column->bSortable);
        $this->assertFalse($column->bRegex);
        $this->assertEquals($column->sSearch, "");
        $this->assertFalse($column->sortable);
        $this->assertEquals($column->sortDirection, "asc");
    }
}
