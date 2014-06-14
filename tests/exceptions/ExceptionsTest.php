<?php

class ExceptionsTest extends DatatablesTestCase {
	
	public function testValidationExceptionAcceptsValidator()
	{
		$validator = $this->app['validator']->make(array(
			"testRequired" => "required",
			"testInteger" => 1
		), array(
			"testRequired" => "required",
			"testInteger" => "required|integer"
		), array(
			"testRequired" => "Field required",
			"testInteger" => "Field is integer"
		));
		
		$exception = new Daveawb\Datatables\ValidationException($validator);
	}
	
	public function testValidationExceptionRetrievesValidator()
	{
		$validator = $this->app['validator']->make(array(
			"testRequired" => "required",
			"testInteger" => 1
		), array(
			"testRequired" => "required",
			"testInteger" => "required|integer"
		), array(
			"testRequired" => "Field required",
			"testInteger" => "Field is integer"
		));
		
		$exception = new Daveawb\Datatables\ValidationException($validator);
		
		$this->assertInstanceOf("Illuminate\Validation\Validator", $exception->getValidator());
	}
	
	public function testValidationExceptionRetrievesValidatorMessages()
	{
		$validator = $this->app['validator']->make(array(
			"testRequired" => "required",
			"testInteger" => "not int"
		), array(
			"testRequired" => "required",
			"testInteger" => "required|integer"
		), array(
			"testRequired" => "Field required",
			"testInteger" => "Field is integer"
		));
		
		$exception = new Daveawb\Datatables\ValidationException($validator);
		
		$this->assertEquals(array(
			"testInteger" => array(
				"validation.integer"
			)
		), $exception->getMessages());
	}
}
