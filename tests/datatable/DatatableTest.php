<?php

class DatatableTest extends DatatablesTestCase {

    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
    }

    public function testClassIsCreatedByIoC()
    {
        $datatable = $this->app->make("Daveawb\Datatables\Datatable");

        $this->assertInstanceOf("Daveawb\Datatables\Datatable", $datatable);

        return $datatable;
    }

    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testModelGetAndSet($datatable)
    {
        $datatable->query(new UserModel());
		
		$driver = $this->getProperty($datatable, "driver");
		
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($driver, "query"));
    }
    
    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testQueryGetAndSet($datatable)
    {
        $datatable->query(DB::table('content'));
        
		$driver = $this->getProperty($datatable, "driver");
		
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($driver, "query"));
    }

    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testRowAttributeGetAndSet($datatable)
    {
        $datatable->attribute("name", "value");

        $this->assertEquals("value", $datatable->attribute("name"));
    }

    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testRowAttributeGettingUnsetKeyReturnsNull($datatable)
    {
        $this->assertNull($datatable->attribute("not_set"));
    }

    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testSettingBuilderObject($datatable)
    {
        $model = new UserModel();

        $datatable->query($model->where("id", "=", 1));
		
		$driver = $this->getProperty($datatable, "driver");
		
        $this->assertInstanceOf("Illuminate\Database\Eloquent\Builder", $this->getProperty($driver, "query"));
    }

    public function testSettingColumnDataCallsCreateOnFactory()
    {
		$mock = Mockery::mock("Daveawb\Datatables\Columns\Factory");
		
		$mock->shouldReceive("validate")->once()->andReturn($this->app['validator']);
		$mock->shouldReceive("create")->twice();
		
		$this->app->instance("Daveawb\Datatables\Columns\Factory", $mock);

        $datatable = $this->app->make("Daveawb\Datatables\Datatable");

        $datatable->columns($columns = array(
            "id",
            "title"
        ));
    }

    /**
     * @expectedException Daveawb\Datatables\ValidationException
     */
    public function testSettingLessColumnsThanExceptedThrowsException()
    {
        $datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
			$this->app['config']
		);
		
        $datatable->columns(array(
            "id"
        ));
    }

    /**
     * @expectedException Daveawb\Datatables\ValidationException
     */
    public function testSettingMoreColumnsThanExceptedThrowsException()
    {
        $datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
        $datatable->columns(array(
            "id",
            "title",
            "content"
        ));
    }
	
	public function testResultReturnsAJsonResponseObject()
	{
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			"id", "first_name"
		));
		
		$result = $datatable->result();
		
		$this->assertInstanceOf("Illuminate\Http\JsonResponse", $result);
	}
	
	public function testSettingMultipleFieldsPerColumn()
	{
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			$col1 = "id",
			$col2 = array("first_name", "last_name")
		));
		
		$factory = $this->getProperty($datatable, "factory");
		
		$column = $factory->getColumn(0);
		
		$this->assertEquals(array($col1), $this->getProperty($column, "fields"));
		
		$column = $factory->getColumn(1);
		
		$this->assertEquals($col2, $this->getProperty($column, "fields"));
	}
	
	/**
	 * Module tests
	 */
	public function testResultsAreSortedAscending()
	{
		$testData = array_merge($this->testData, array(
			"bSortable_0" => true,
			"bSortable_1" => true,
			"iSortCol_0" => 0,
			"sSortDir_0" => "asc"
		));
		
		$this->app['request']->replace($testData);
		
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			"first_name",
			"last_name"
		));
		
		$result = $datatable->result();
		
		$data = json_decode($result->getContent());

		$this->assertEquals("Barry", $data->aaData[0][0]);
	}
	
	public function testResultsAreSortedDescending()
	{
		$testData = array_merge($this->testData, array(
			"bSortable_0" => true,
			"bSortable_1" => true,
			"iSortCol_0" => 0,
			"sSortDir_0" => "desc"
		));
		
		$this->app['request']->replace($testData);
		
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			"first_name",
			"last_name"
		));
		
		$result = $datatable->result();
		
		$data = json_decode($result->getContent());
		
		$this->assertEquals("Englebert", $data->aaData[0][0]);
	}
	
	public function testMultipleFieldsAreSortedAscending()
	{
		$testData = array_merge($this->testData, array(
			"bSortable_0" => true,
			"bSortable_1" => false,
			"iSortCol_0" => 0,
			"sSortDir_0" => "asc"
		));
		
		$expected = array(
			"Barry Evans",
			"Barry Manilow",
			"Barry Scott",
			"Barry White",
			"Barry Williams"
		);
		
		$this->app['request']->replace($testData);
		
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(with(new UserModel())->where('first_name', '=', 'Barry'));
		
		$datatable->columns(array(
			array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
			array("id")
		));
		
		$result = $datatable->result();
		
		$data = json_decode($result->getContent());
		
		for ($i = 0; $i < count($data->aaData); $i++)
		{
			$this->assertEquals($expected[$i], $data->aaData[$i][0]);
		}
	}
	
	public function testMultipleFieldsAreSortedDescending()
	{
		$testData = array_merge($this->testData, array(
			"bSortable_0" => true,
			"bSortable_1" => false,
			"iSortCol_0" => 0,
			"sSortDir_0" => "desc"
		));
		
		$expected = array(
			"Barry Evans",
			"Barry Manilow",
			"Barry Scott",
			"Barry White",
			"Barry Williams"
		);
		
		$this->app['request']->replace($testData);
		
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(with(new UserModel())->where('first_name', '=', 'Barry'));
		
		$datatable->columns(array(
			array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
			array("id")
		));
		
		$result = $datatable->result();
		
		$data = json_decode($result->getContent());
		
		for ($i = 0; $i < count($data->aaData); $i++)
		{
			$this->assertEquals($expected[4 - $i], $data->aaData[$i][0]);
		}
	}

	public function testSearchReturnsOnlyResultsWithSearchString()
	{
		$testData = array_merge($this->testData, array(
			"bSearchable_0" => true,
			"bSearchable_1" => false,
			"sSearch" => "Barry"
		));
		
		$this->app['request']->replace($testData);
		
		$datatable = new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			"first_name",
			"last_name"
		));
		
		$result = $datatable->result();
		
		$data = json_decode($result->getContent());
		
		$this->assertCount(5, $data->aaData);
	}
    
    public function testCombineInterpreterReturnsCorrectData()
    {
        $datatable = new Daveawb\Datatables\Datatable(
            new Daveawb\Datatables\Columns\Factory(
                new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
                $this->app['validator']
            ),
            new Daveawb\Datatables\Drivers\Laravel,
            new Illuminate\Http\JsonResponse,
            $this->app['config']
        );
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Barry Evans");
    }
    
    public function testAppendInterpreterReturnsCorrectData()
    {
        $datatable = new Daveawb\Datatables\Datatable(
            new Daveawb\Datatables\Columns\Factory(
                new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
                $this->app['validator']
            ),
            new Daveawb\Datatables\Drivers\Laravel,
            new Illuminate\Http\JsonResponse,
            $this->app['config']
        );
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("append" => "%")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Barry%");
    }
    
    public function testMultipleInterpretersReturnCorrectData()
    {
        $datatable = new Daveawb\Datatables\Datatable(
            new Daveawb\Datatables\Columns\Factory(
                new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
                $this->app['validator']
            ),
            new Daveawb\Datatables\Drivers\Laravel,
            new Illuminate\Http\JsonResponse,
            $this->app['config']
        );
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("append" => "%", "prepend" => "Mr, ", "combine" => "first_name,last_name, ")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Mr Barry% Evans");
    }
}
