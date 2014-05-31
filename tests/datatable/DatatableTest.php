<?php

class DatatableTest extends DatatablesTestCase {

    public function setUp()
    {
        parent::setUp();
        
        //$this->migrateDatabase();
        $this->setupDatabase();

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
        $datatable->model(new UserModel());

        $this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $this->getProperty($datatable, "model"));
    }

    /**
     * @depends testClassIsCreatedByIoC
     * @expectedException ErrorException
     */
    public function testSettingStringToModelThrowsException($datatable)
    {
        $datatable->model("strings!");
    }
    
    /**
     * @depends testClassIsCreatedByIoC
     * @expectedException ErrorException
     */
    public function testSettingBadInstanceToModelThrowsException($datatable)
    {
        $datatable->model(new stdClass());
    }
    
    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testQueryGetAndSet($datatable)
    {
        $datatable->query(DB::table('content'));
        
        $this->assertInstanceOf("Illuminate\Database\Query\Builder", $this->getProperty($datatable, "model"));
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

        $datatable->model($model->where("id", "=", 1));
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
        		new Daveawb\Datatables\Columns\Input($this->app['request']),
        		$this->app['validator']
			),
			new Illuminate\Http\JsonResponse
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
        		new Daveawb\Datatables\Columns\Input($this->app['request']),
        		$this->app['validator']
			),
			new Illuminate\Http\JsonResponse
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
        		new Daveawb\Datatables\Columns\Input($this->app['request']),
        		$this->app['validator']
			),
			new Illuminate\Http\JsonResponse
		);
		
		$datatable->model(new UserModel());
		
		$datatable->columns(array(
			"id", "first_name"
		));
		
		$result = $datatable->result();
		
		$this->assertInstanceOf("Illuminate\Http\JsonResponse", $result);
	}
}
