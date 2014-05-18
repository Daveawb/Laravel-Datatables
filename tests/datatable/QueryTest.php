<?php

class QueryTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
    }
    
    public function testConstructAcceptsModel()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $query = new Daveawb\Datatables\Query(new UserModel(), $input, array());
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsFluentBuilder()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $query = new Daveawb\Datatables\Query(DB::table('contents'), $input, array());
        
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsEloquentBuilder()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $model = new UserModel();
        
        $model = $model->where("id", "=", 1);
        
        $query = new Daveawb\Datatables\Query($model, $input, array());
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Builder", $this->getProperty($query, "query"));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testConstructThrowsExceptionIfFirstArgIsIncorrect()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $query = new Daveawb\Datatables\Query(array(), $input, array());
    }
   
}
