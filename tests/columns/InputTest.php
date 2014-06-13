<?php
class InputTest extends DatatablesTestCase {

    public $globalFields = array(
        'bRegex',
        'iColumns',
        'iDisplayLength',
        'iDisplayStart',
        'iSortingCols',
        'sEcho',
        'sSearch'
    );

    protected $columnFields = array(
        'bSearchable',
        'bSortable',
        'mDataProp',
        'bRegex',
        'sSearch'
    );

    protected $sortingFields = array(
        'iSortCol',
        'sSortDir'
    );

    /**
     * Unit Tests
     */
    public function testPropertiesContainCorrectDefaults()
    {
        $globals = $this->getDefaultProperty("Daveawb\Datatables\Columns\Input\OneNineInput", "globalFields");
        $columns = $this->getDefaultProperty("Daveawb\Datatables\Columns\Input\OneNineInput", "columnFields");
		$sorting = $this->getDefaultProperty("Daveawb\Datatables\Columns\Input\OneNineInput", "sortingFields");

        $this->assertEquals($this->globalFields, $globals);
		$this->assertEquals($this->columnFields, $columns);
		$this->assertEquals($this->sortingFields, $sorting);
    }

    public function testCreatedAttributesAreCorrect()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->get();

        foreach ($this->globalFields as $property)
        {
            $this->assertEquals($this->testData[$property], $output['global'][$property]);
        }

        foreach ($this->columnFields as $property)
        {
            for ($i = 0; $i  < $this->testData['iColumns']; $i++)
            {
                $this->assertEquals($this->testData[$property . '_' . $i], $output[$i][$property]);
            }
        }
    }

    public function testMiscInputIsNotPulledThrough()
    {
        $this->app['request']->replace(array_merge($this->testData, array('foo' => 'bar')));

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->get();

        $this->assertTrue($this->app['request']->has('foo'));
        $this->assertFalse(isset($output['foo']));
    }

    public function testIntegersAreTypeCastToInt()
    {
        $this->app['request']->replace(array_merge($this->testData, array('iColumns' => '2')));

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->get();

        $this->assertInternalType("int", $output['global']['iColumns']);
    }

    public function testEchoIsTypeCastToInt()
    {
        $this->app['request']->replace(array_merge($this->testData, array('sEcho' => '200')));

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->get();

        $this->assertInternalType("int", $output['global']['sEcho']);
        $this->assertEquals(200, $output['global']['sEcho']);
    }

    public function testGatherReturnsCorrectData()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->getColumn(0);

        $this->assertFalse($output['bSearchable']);
        $this->assertFalse($output['bSortable']);
        $this->assertEquals($output['mDataProp'], 0);
        $this->assertFalse($output['bRegex']);
        $this->assertEquals($output['sSearch'], "");
    }

    public function testGatherGlobalsReturnsCorrectData()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->getGlobals();

        $this->assertEquals($output['sEcho'], 1);
        $this->assertEquals($output['iDisplayLength'], 10);
        $this->assertEquals($output['iDisplayStart'], 0);
        $this->assertEquals($output['iColumns'], 2);
        $this->assertEquals($output['sSearch'], "");
        $this->assertFalse($output['bRegex']);
    }

    public function testGatherSortablesReturnsCorrectData()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->getColumn(0);

        $this->assertEquals(true, $output['sort']);
        $this->assertEquals('asc', $output['sort_dir']);
    }

    /**
     * @expectedException Daveawb\Datatables\InputMissingException
     */
    public function testMissingInputThrowsInputMissingException()
    {
        $data = $this->testData;
        unset($data['sEcho']);

        $this->app['request']->replace($data);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);

        $output = $input->get();
    }

    public function testSettingMiscAttributeAppliesToTheGlobalValues()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);
        
        $input->newProperty = "testing";
        
        $attributes =  $this->getProperty($input, "attributes");
        
        $this->assertEquals($input->newProperty, "testing");
        $this->assertTrue(array_key_exists("newProperty", $attributes['global']));
        $this->assertEquals($attributes['global']['newProperty'], "testing");
    }
    
    public function testGettingNonExistantPropertyReturnsNull()
    {
        $this->app['request']->replace($this->testData);

        $input = new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']);
        
        $this->assertNull($input->nonExistantProperty);
    }
}
