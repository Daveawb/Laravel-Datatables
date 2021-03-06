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
        $datatable = $this->getDatatable();
		
        $datatable->columns(array(
            "id"
        ));
    }

    /**
     * @expectedException Daveawb\Datatables\ValidationException
     */
    public function testSettingMoreColumnsThanExceptedThrowsException()
    {
        $datatable = $this->getDatatable();
		
        $datatable->columns(array(
            "id",
            "title",
            "content"
        ));
    }
	
	public function testResultReturnsAJsonResponseObject()
	{
		$datatable = $this->getDatatable();
		
		$datatable->query(new UserModel());
		
		$datatable->columns(array(
			"id", "first_name"
		));
		
		$result = $datatable->result();

		$this->assertInstanceOf("Illuminate\Http\JsonResponse", $result);
	}
	
	public function testSettingMultipleFieldsPerColumn()
	{
		$datatable = $this->getDatatable();
		
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
	
	private function getDatatable()
	{
		return new Daveawb\Datatables\Datatable(
        	new Daveawb\Datatables\Columns\Factory(
        		new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
        		$this->app['validator']
			),
			new Daveawb\Datatables\Drivers\Laravel,
			new Illuminate\Http\JsonResponse,
            $this->app['config']
		);
	}
}
