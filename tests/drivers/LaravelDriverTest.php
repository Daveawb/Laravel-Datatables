<?php

class LaravelDriverTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
		
		$this->colFactory = Mockery::mock("Daveawb\Datatables\Columns\Factory");
    }
    
    public function testQueryAcceptsModel()
    {        
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->query(new UserModel());
		
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($query, "query"));
    }
    
    public function testQueryAcceptsFluentBuilder()
    {
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->query(DB::table('users'));
        
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($query, "query"));
    }
    
    public function testQueryAcceptsEloquentBuilder()
    {
        $model = new UserModel();
        
        $model = $model->where("id", "=", 1);
        
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->query($model);
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Builder", $this->getProperty($query, "query"));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testQueryThrowsExceptionIfFirstArgIsIncorrect()
    {
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->query(array());
    }
   
}
