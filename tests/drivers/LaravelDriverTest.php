<?php

class LaravelDriverTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
		
		$this->colFactory = Mockery::mock("Daveawb\Datatables\Columns\Factory");
    }
    
    public function testSetupAcceptsModel()
    {        
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->setup(new UserModel(), $this->colFactory);
		
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsFluentBuilder()
    {
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->setup(DB::table('users'), $this->colFactory);
        
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($query, "query"));
    }
    
    public function testConstructAcceptsEloquentBuilder()
    {
        $model = new UserModel();
        
        $model = $model->where("id", "=", 1);
        
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->setup($model, $this->colFactory);
        
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Builder", $this->getProperty($query, "query"));
    }
    
    /**
     * @expectedException ErrorException
     */
    public function testConstructThrowsExceptionIfFirstArgIsIncorrect()
    {
        $query = new Daveawb\Datatables\Drivers\Laravel();
        
		$query->setup(array(), $this->colFactory);
    }
   
}
