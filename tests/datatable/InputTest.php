<?php 

class InputTest extends DatatablesTestCase {
	
	public $allowed = array (
        'bRegex',
        'iColumns',
        'iDisplayLength',
        'iDisplayStart',
        'iSortingCols',
        'sEcho',
        'sSearch'
    );
	
	public $iterative = array (
        'bSearchable'   => 'iColumns',
        'bSortable'     => 'iColumns',
        'mDataProp'     => 'iColumns',
        'bRegex'        => 'iColumns',
        'sSearch'       => 'iColumns',
        'iSortCol'      => 'iSortingCols',
        'sSortDir'      => 'iSortingCols'
    );
	
	/**
	 * Unit Tests
	 */
	public function testAllowedPropertyContainsCorrectValues()
	{
		$property = parent::getProperty("Daveawb\Datatables\Support\Input", "allowed");
		
		$this->assertEquals($this->allowed, $property);
	}
	
	public function testIterativePropertyContainsCorrectValues()
	{
		$property = parent::getProperty("Daveawb\Datatables\Support\Input", "iterative");
		
		$this->assertEquals($this->iterative, $property);
	}
	
	public function testCreatedAttributesAreCorrect()
	{
		$this->app['request']->replace($this->testData);
		
		$input = new Daveawb\Datatables\Support\Input($this->app['request']);
		
		$output = $input->build()->get();
		
		foreach($this->allowed as $property)
		{
			$this->assertEquals($this->testData[$property], $output[$property]);
		}
		
		foreach($this->iterative as $property => $iterator)
		{
			for ($i = 0; $i < $this->testData[$iterator]; $i++) 
			{
				$this->assertEquals($this->testData[$property.'_'.$i], $output[$property.'_'.$i]);
			}
		}
	}
	
	public function testMiscInputIsNotPulledThrough()
	{
		$this->app['request']->replace(array_merge($this->testData, array('foo' => 'bar')));
		
		$input = new Daveawb\Datatables\Support\Input($this->app['request']);
		
		$output = $input->build()->get();
		
		$this->assertTrue($this->app['request']->has('foo'));
		$this->assertFalse(isset($output['foo']));
	}
	
	public function testIntegersAreTypeCastToInt()
	{
		$this->app['request']->replace(array_merge($this->testData, array('iColumns' => '2')));
		
		$input = new Daveawb\Datatables\Support\Input($this->app['request']);
		
		$output = $input->build()->get();
		
		$this->assertInternalType("int", $output['iColumns']);
	}
	
	public function testEchoIsTypeCastToInt()
	{
		$this->app['request']->replace(array_merge($this->testData, array('sEcho' => '200')));
		
		$input = new Daveawb\Datatables\Support\Input($this->app['request']);
		
		$output = $input->build()->get();
		
		$this->assertInternalType("int", $output['sEcho']);
		$this->assertEquals(200, $output['sEcho']);
	}
	
	/**
	 * @expectedException Daveawb\Datatables\InputMissingException
	 */
	public function testMissingInputThrowsInputMissingException() 
	{
		$data = $this->testData;
		unset($data['sEcho']);
		
		$this->app['request']->replace($data);
		
		$input = new Daveawb\Datatables\Support\Input($this->app['request']);
		
		$output = $input->build()->get();
	}
}