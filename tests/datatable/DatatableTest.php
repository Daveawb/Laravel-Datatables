<?php

class DatatableTest extends DatatablesTestCase {

    public function setUp()
    {
        parent::setUp();
        
        //$this->migrateDatabase();
        $this->setupDatabase();

        $this->app['request']->replace($this->testData);

        $mock = Mockery::mock("Daveawb\Datatables\Input");

        $mock->shouldReceive('build')->once()->andReturn(array('foo' => 'bar'));
        $mock->shouldReceive('gatherGlobals')->once()->andReturn(array('foo' => 'bar'));
        $mock->shouldReceive('gatherSortables')->once()->andReturn(array('foo' => 'bar'));

        $this->app->instance("Daveawb\Datatables\Input", $mock);
    }

    public function testClassIsCreatedByIoC()
    {
        $datatable = $this->app->make("Daveawb\Datatables\DatatableInterface");

        $this->assertInstanceOf("Daveawb\Datatables\Datatable", $datatable);

        return $datatable;
    }

    /**
     * @depends testClassIsCreatedByIoC
     */
    public function testInputIsBuiltAndRetrieved($datatable)
    {
        $data = $this->getProperty($datatable, "input");
        
        $this->assertEquals(array('foo' => 'bar'), $data);
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

    public function testSettingColumnDataAsBasicArray()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);

        $datatable = new Daveawb\Datatables\Datatable($input, new Daveawb\Datatables\Columns\Factory());

        $datatable->columns($columns = array(
            "id",
            "title"
        ));
        
        $columns = $this->getProperty($datatable, "columns");
        
        $this->assertCount(2, $columns);
        $this->assertInstanceOf("Daveawb\Datatables\Columns\Column", $columns[0]);
    }

    /**
     * @expectedException Daveawb\Datatables\ColumnCountException
     */
    public function testSettingLessColumnsThanExceptedThrowsException()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);

        $datatable = new Daveawb\Datatables\Datatable($input, new Daveawb\Datatables\Columns\Factory());

        $datatable->columns(array(
            "id"
        ));
    }

    /**
     * @expectedException Daveawb\Datatables\ColumnCountException
     */
    public function testSettingMoreColumnsThanExceptedThrowsException()
    {
        $input = new Daveawb\Datatables\Input($this->app['request']);

        $datatable = new Daveawb\Datatables\Datatable($input, new Daveawb\Datatables\Columns\Factory());
        
        $datatable->columns(array(
            "id",
            "title",
            "content"
        ));
    }
    
    public function testResultBuildsQuery()
    {
        $testData = array(
            "sEcho" => 1,
            "iDisplayLength" => 10,
            "iDisplayStart" => 0,
            "iColumns" => 2,
            "sSearch" => "Barry",
            "bRegex" => false,
            "bSearchable_0" => false,
            "bSearchable_1" => false,
            "sSearch_0" => "",
            "sSearch_1" => "",
            "bRegex_0" => false,
            "bRegex_1" => false,
            "bSortable_0" => true,
            "bSortable_1" => true,
            "iSortingCols" => 1,
            "iSortCol_0" => 1,
            "sSortDir_0" => "desc",
            "mDataProp_0" => 0,
            "mDataProp_1" => 1
        );
        
        $this->app['request']->replace($testData);
        
        $input = new Daveawb\Datatables\Input($this->app['request']);
        
        $datatable = new Daveawb\Datatables\Datatable($input, new Daveawb\Datatables\Columns\Factory());
        
        $model = new UserModel();
		
        $datatable->model($model->select('username', 'first_name', 'last_name'));
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
    }

}
