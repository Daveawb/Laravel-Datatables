<?php

class DatatablesTestCase extends Orchestra\Testbench\TestCase {
	
	protected $testData = array(
        "sEcho" => 1,
        "iDisplayLength" => 10,
        "iDisplayStart" => 0,
        "iColumns" => 2,
        "sSearch" => "",
        "bRegex" => false,
        "bSearchable_0" => false,
        "bSearchable_1" => false,
        "sSearch_0" => "",
        "sSearch_1" => "",
        "bRegex_0" => false,
        "bRegex_1" => false,
        "bSortable_0" => false,
        "bSortable_1" => false,
        "iSortingCols" => 1,
        "iSortCol_0" => 0,
        "sSortDir_0" => "asc",
        "mDataProp_0" => 0,
        "mDataProp_1" => 1
    );
	
	public function setUp()
    {
        parent::setUp();
    }
	
	protected function getPackageProviders()
    {
        return array('Daveawb\Datatables\DatatablesServiceProvider');
    }
	
	protected function getPackageAliases()
    {
        return array(
            'Datatable' => 'Daveawb\Datatables\Facades\Datatable'
        );
    }
	
	/**
	 * Setup testing environment for database using sqlite
	 * @param {Object} Illuminate\Foundation\Application
	 */
	protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'testing');
		
		$app['config']->set('database.connections.setup', array(
            'driver'   => 'sqlite',
            'database' => __DIR__ . '/stubdb.sqlite',
            'prefix'   => '',
        ));
		
        $app['config']->set('database.connections.testing', array(
            'driver'   => 'sqlite',
            'database' => __DIR__ . '/testdb.sqlite',
            'prefix'   => '',
        ));
    }
	
	public function setupDatabase()
	{
		exec('rm ' . __DIR__ . '/testdb.sqlite');
	    exec('cp ' . __DIR__ . '/stubdb.sqlite ' . __DIR__ . '/testdb.sqlite');
	}
	
	public function runMigrations()
	{
		// Allow us to call artisan commands
		$artisan = $this->app->make('artisan');
		
		$artisan->call('migrate', array(
            '--database' => 'setup',
            '--path'     => __DIR__ . '/migrations',
        ));
		
		$this->setupDatabase();
	}
	
	public function getDefaultProperty($class, $property)
	{
		$class = $this->getReflection($class);
		$properties = $class->getDefaultProperties();
		return $properties[$property];
	}    
    
    public function getMethod($class, $method)
    {
        $reflection = $this->getReflection($class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }

    public function getProperty($class, $property)
    {
        $reflection = $this->getReflection($class);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($class);
    }

    public function setProperty($class, $property, $value)
    {
        $reflection = $this->getReflection($class);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->setValue($class, $value);
    }

    private function getReflection($class)
    {
        return new ReflectionClass($class);
    }
}