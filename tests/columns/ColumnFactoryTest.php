<?php

class ColumnFactoryTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);    
    }
    
    public function testCreateReturnsNewColumn()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $factory = new Daveawb\Datatables\Columns\Factory();
        
        $factory->input($input->build());
        
        $column = $factory->create("id", 0);
        
        $this->assertInstanceOf("Daveawb\Datatables\Columns\Column", $column);
        
        return $column;
    } 
    
    /**
     * @depends testCreateReturnsNewColumn
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
