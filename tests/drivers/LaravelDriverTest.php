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
	
	public function testBuildQueryWheresAddsWhereClause()
	{
		$driver = new Daveawb\Datatables\Drivers\Laravel();
        
		$model = new UserModel();
		
		$column = new Daveawb\Datatables\Columns\Column(array("id"), array("bRegex" => false, "sSearch" => "Barry", "bSearchable" => true, "bSortable" => false));
		
		$builder = $this->getMethod($driver, "buildWhereClause");

		$query = $builder->invoke($driver, $model, $column);
		
		$wheres = $query->getQuery()->wheres[0];
		
		$this->assertEquals("id", $wheres["column"]);
		$this->assertEquals("%Barry%", $wheres["value"]);
		$this->assertEquals("and", $wheres["boolean"]);
	}
	
	public function testBuildFullQueryAddsWhereClauses()
	{
		$driver = new Daveawb\Datatables\Drivers\Laravel();
		
		$this->colFactory->shouldReceive("getColumns")->once()->andReturn(array(
			 new Daveawb\Datatables\Columns\Column(array("id"), array("bRegex" => false, "sSearch" => "Barry", "bSearchable" => true, "bSortable" => false)),
			 new Daveawb\Datatables\Columns\Column(array("first_name"), array("bRegex" => false, "sSearch" => "Barry", "bSearchable" => true, "bSortable" => false))
		));
		
		$stdClass = new stdClass();
		$stdClass->iDisplayStart = 0;
		$stdClass->iDisplayLength = 10;
		
		$this->colFactory->input = $stdClass;
		
		$driver->query(new UserModel());
		$driver->factory($this->colFactory);
		
		$build = $this->getMethod($driver, "build");
		
		$query = $build->invoke($driver);
		
		$wheres = $query->getQuery()->wheres;
		
		$this->assertCount(2, $wheres);
		$this->assertEquals("id", $wheres[0]["column"]);
		$this->assertEquals("%Barry%", $wheres[0]["value"]);
		$this->assertEquals("and", $wheres[0]["boolean"]);
		
		
		$this->assertEquals("first_name", $wheres[1]["column"]);
		$this->assertEquals("%Barry%", $wheres[1]["value"]);
		$this->assertEquals("or", $wheres[1]["boolean"]);
	}
   	
	public function testBuildFullQueryAddsSkipAndTake()
	{
		$driver = new Daveawb\Datatables\Drivers\Laravel();
		
		$this->colFactory->shouldReceive("getColumns")->once()->andReturn(array(
			 new Daveawb\Datatables\Columns\Column(array("id"), array("bRegex" => false, "sSearch" => "Barry", "bSearchable" => true, "bSortable" => false)),
			 new Daveawb\Datatables\Columns\Column(array("first_name"), array("bRegex" => false, "sSearch" => "Barry", "bSearchable" => true, "bSortable" => false))
		));
		
		$stdClass = new stdClass();
		$stdClass->iDisplayStart = 0;
		$stdClass->iDisplayLength = 10;
		
		$this->colFactory->input = $stdClass;
		
		$driver->query(new UserModel());
		$driver->factory($this->colFactory);
		
		$build = $this->getMethod($driver, "build");
		
		$query = $build->invoke($driver);
		
		$offset = $query->getQuery()->offset;
		$limit = $query->getQuery()->limit;
		
		$this->assertEquals(0, $offset);
		$this->assertEquals(10, $limit);
	}
}
