<?php

class QueryTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
		
		$this->colFactory = Mockery::mock("Daveawb\Datatables\Columns\Factory");
    }
    
    public function testConstructAcceptsModel()
    {        
        $query = new Daveawb\Datatables\Query(new UserModel(), $this->colFactory);
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsFluentBuilder()
    {
        $query = new Daveawb\Datatables\Query(DB::table('contents'), $this->colFactory);
        
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsEloquentBuilder()
    {
        $model = new UserModel();
        
        $model = $model->where("id", "=", 1);
        
        $query = new Daveawb\Datatables\Query($model, $this->colFactory);
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Builder", $this->getProperty($query, "query"));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testConstructThrowsExceptionIfFirstArgIsIncorrect()
    {
        $query = new Daveawb\Datatables\Query(array(), $this->colFactory);
    }
   
}
