<?php

class ColumnFactoryTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);    
    }
    
    public function testCreateBuildsNewColumnIndexedByName()
    {
        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);
        
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
        $this->assertEquals($column->fields[0], 'id');
        $this->assertEquals($column->mDataProp, 0);
        $this->assertFalse($column->bSearchable);
        $this->assertFalse($column->bSortable);
        $this->assertFalse($column->bRegex);
        $this->assertEquals($column->sSearch, "");
        $this->assertTrue($column->sort);
        $this->assertEquals($column->sort_dir, "asc");
    }
	
	public function testColumnHasSearchSetFromGlobal()
	{
		$testData = array_merge($this->testData, array(
			"sSearch" => "test",
			"bSearchable_0" => true
		));
		
		$this->app['request']->replace($testData);    
		
		$input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);
        
        $factory = new Daveawb\Datatables\Columns\Factory($input, $this->app['validator']);
        
        $factory->create("id", 0);
		
		$this->assertEquals("test", $factory->getColumn(0)->sSearch);
	}
	
	public function testColumnHasSearchSetLocallyAndIsNotOverridenByGlobal()
	{
		$testData = array_merge($this->testData, array(
			"sSearch" => "test",
			"sSearch_0" => "col0",
			"bSearchable_0" => true
		));
		
		$this->app['request']->replace($testData);    
		
		$input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);
        
        $factory = new Daveawb\Datatables\Columns\Factory($input, $this->app['validator']);
        
        $factory->create("id", 0);
		
		$this->assertEquals("col0", $factory->getColumn(0)->sSearch);
	}
}
