<?php

class DatatableTest extends DatatablesTestCase {
	
	public function setUp()
	{
		parent::setUp();
		
		$this->runMigrations();
		
		$this->app['request']->replace($this->testData);
			
		$mock = Mockery::mock("Daveawb\Datatables\Support\Input");
		
		$mock->shouldReceive('build->get')->once()->andReturn(array('foo' => 'bar'));
		
		$this->app->instance("Daveawb\Datatables\Support\Input", $mock);
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
		$this->assertEquals(array('foo' => 'bar'), $datatable->input);
	}
	
	/**
	 * @depends testClassIsCreatedByIoC
	 */
	public function testModelGetAndSet($datatable)
	{
		$datatable->model(new UserModel());
		
		$this->assertInstanceOf("Illuminate\Database\Eloquent\Model", $datatable->model());
	}
	
	/**
	 * @depends testClassIsCreatedByIoC
	 * @expectedException ErrorException
	 */
	public function testSettingWrongTypeToModelThrowsException($datatable)
	{
		$datatable->model("strings!");
	}
	
	/**
	 * @depends testClassIsCreatedByIoC
	 */
	public function testRowAttributeGetAndSet($datatable)
	{
		$datatable->rowAttribute("name", "value");
		
		$this->assertEquals("value", $datatable->rowAttribute("name"));
	}
	
	/**
	 * @depends testClassIsCreatedByIoC
	 */
	public function testRowAttributeGettingUnsetKeyReturnsNull($datatable)
	{
		$this->assertNull($datatable->rowAttribute("not_set"));
	}
	
	/**
	 * @depends testClassIsCreatedByIoC
	 */
	public function testSettingBuilderObject($datatable)
	{
		$model = new UserModel();
		
		$datatable->builder($model->where("id", "=", 1));
	}
}
