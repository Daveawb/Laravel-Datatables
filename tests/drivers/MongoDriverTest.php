<?php

class MongoDriverTest extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->config = $this->app['config']->get("datatables::database.connections.mongo");
        
        $mock = Mockery::mock("Daveawb\Datatables\Drivers\Mongo[createConnection,getDsn]")->shouldAllowMockingProtectedMethods();
        
        $collectionClass = new stdClass();
        $collectionClass->test = true;
        
        $connection = new stdClass();
        $connection->database = new stdClass();
        $connection->database->testcollection = $collectionClass;
        
        $mock->shouldReceive("createConnection")->once()->andReturn($connection);
        $mock->shouldReceive("getDsn")->once()->andReturn(array());
                
        $mock->config($this->config);
        
        $this->driver = $mock;
    }
    
    public function testConfigIsSetCorrectlyAndCreatesConnection()
    {
        $this->assertEquals($this->config, $this->getProperty($this->driver, 'config'));
        $this->assertInstanceOf("stdClass", $this->getProperty($this->driver, 'db'));
    }
    
    public function testCollectionAppliesCollectionToDbObject()
    {
        $method = $this->getMethod($this->driver, "collection");
        
        $method->invoke($this->driver, "testcollection");
        
        $this->assertTrue($this->getProperty($this->driver, "db")->test);
    }
    
    public function testQueryAcceptsCollectionAndAppliesIt()
    {
        $this->driver->query("testcollection");
        
        $this->assertTrue($this->getProperty($this->driver, "db")->test);
    }
    
    public function testQueryAcceptsArrayWithClosureAsSecondArg()
    {
        $this->driver->query(array("testcollection", function()
        {
            return array(
                '$or' => array(
                    array("first_name" => "Simon")
                )
            );
        }));
        
        $this->assertEquals(array(array("first_name" => "Simon")), $this->getProperty($this->driver, "searchTerms")['$or']);
    }
    
    /**
     * @expectedException Daveawb\Datatables\DatatablesException
     */
    public function testQueryThrowsExceptionIfNoClosureIsPassed()
    {
        $this->driver->query(array("testcollection", "not callable"));
    }
    
    /**
     * @expectedException Daveawb\Datatables\DatatablesException
     */
    public function testQueryThrowsExceptionIfMoreThanTwoArrayIndexesArePassed()
    {
        $this->driver->query(array("testcollection", "not callable", function($db) {}));
    }
    
    /**
     * @expectedException Daveawb\Datatables\DatatablesException
     */
    public function testQueryThrowsExceptionIfLessThanTwoArrayIndexesArePassed()
    {
        $this->driver->query(array(function($db) {}));
    }
    
    public function testQueryGetsCorrectData()
    {
        $this->seedMongo();
        
        $this->app['config']->set("datatables::database.connections.mongo.database", "datatablestests");
        
        $this->app['request']->replace($this->testData);
        
        $driver = new Daveawb\Datatables\Drivers\Mongo();
        
        $driver->config($this->app['config']->get("datatables::database.connections.mongo"));
        
        $driver->factory(new Daveawb\Datatables\Columns\Factory(
            new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
            $this->app['validator']
        ));
        
        $driver->query("users");
        
        $result = $driver->get();
        
        $this->assertInstanceOf("MongoCursor", $result["aaData"]);
        $this->assertEquals(2, $result['iTotalRecords']);
        $this->assertEquals(2, $result['iTotalDisplayRecords']);
    }
    
    public function testDriverFiltersDataUsingSearch()
    {
        $this->seedMongo();
        
        $this->app['config']->set("datatables::database.connections.mongo.database", "datatablestests");
        
        $this->app['request']->replace(array_merge($this->testData, array("bSearchable_0" => true, "sSearch" => "Simon")));
        
        $driver = new Daveawb\Datatables\Drivers\Mongo();
        
        $driver->config($this->app['config']->get("datatables::database.connections.mongo"));
        
        $factory = new Daveawb\Datatables\Columns\Factory(
            new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
            $this->app['validator']
        );
        
        $factory->create("first_name", 0);
        $factory->create("last_name", 1);
        
        $driver->factory($factory);
        
        $driver->query("users");
        
        $result = $driver->get();
        
        $this->assertInstanceOf("MongoCursor", $result["aaData"]);
        $this->assertEquals(2, $result['iTotalRecords']);
        $this->assertEquals(1, $result['iTotalDisplayRecords']);
        
        foreach($result['aaData'] as $value)
        {
            $this->assertEquals("Simon", $value['first_name']);
            $this->assertEquals("Holloway", $value['last_name']);
        }
    }

    public function testDriverOrdersDataAsc()
    {
        $this->seedMongo();
        
        $this->app['config']->set("datatables::database.connections.mongo.database", "datatablestests");
        
        $this->app['request']->replace(array_merge($this->testData, array("bSortable_0" => true, "iSortCol_0" => 0, "sSortDir_0" => "asc")));
        
        $driver = new Daveawb\Datatables\Drivers\Mongo();
        
        $driver->config($this->app['config']->get("datatables::database.connections.mongo"));
        
        $factory = new Daveawb\Datatables\Columns\Factory(
            new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
            $this->app['validator']
        );
        
        $factory->create("first_name", 0);
        $factory->create("last_name", 1);
        
        $driver->factory($factory);
        
        $driver->query("users");
        
        $result = $driver->get();
        
        $this->assertInstanceOf("MongoCursor", $result["aaData"]);
        $this->assertEquals(2, $result['iTotalRecords']);
        $this->assertEquals(2, $result['iTotalDisplayRecords']);
        
        $value = $result['aaData']->getNext();
        
        $this->assertEquals("David", $value['first_name']);
        $this->assertEquals("Barker", $value['last_name']);
    }
    
    public function testDriverOrdersDataDesc()
    {
        $this->seedMongo();
        
        $this->app['config']->set("datatables::database.connections.mongo.database", "datatablestests");
        
        $this->app['request']->replace(array_merge($this->testData, array("bSortable_0" => true, "iSortCol_0" => 0, "sSortDir_0" => "desc")));
        
        $driver = new Daveawb\Datatables\Drivers\Mongo();
        
        $driver->config($this->app['config']->get("datatables::database.connections.mongo"));
        
        $factory = new Daveawb\Datatables\Columns\Factory(
            new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
            $this->app['validator']
        );
        
        $factory->create("first_name", 0);
        $factory->create("last_name", 1);
        
        $driver->factory($factory);
        
        $driver->query("users");
        
        $result = $driver->get();
        
        $this->assertInstanceOf("MongoCursor", $result["aaData"]);
        $this->assertEquals(2, $result['iTotalRecords']);
        $this->assertEquals(2, $result['iTotalDisplayRecords']);
        
        $value = $result['aaData']->getNext();
        
        $this->assertEquals("Simon", $value['first_name']);
        $this->assertEquals("Holloway", $value['last_name']);
    }

    public function testDriverUsesClosureDataInQuery()
    {
        $this->seedMongo();
        
        $this->app['config']->set("datatables::database.connections.mongo.database", "datatablestests");
        
        $this->app['request']->replace($this->testData);
        
        $driver = new Daveawb\Datatables\Drivers\Mongo();
        
        $driver->config($this->app['config']->get("datatables::database.connections.mongo"));
        
        $factory = new Daveawb\Datatables\Columns\Factory(
            new Daveawb\Datatables\Columns\Input\OneNineInput($this->app['request']),
            $this->app['validator']
        );
        
        $factory->create("first_name", 0);
        $factory->create("last_name", 1);
        
        $driver->factory($factory);
        
        $driver->query(array("users", function() {
            return array(
                "first_name" => "Simon"
            );
        }));
        
        $result = $driver->get();
        
        $this->assertInstanceOf("MongoCursor", $result["aaData"]);
        $this->assertEquals(1, $result['iTotalRecords']);
        $this->assertEquals(1, $result['iTotalDisplayRecords']);
        
        $value = $result['aaData']->getNext();
        
        $this->assertEquals("Simon", $value['first_name']);
        $this->assertEquals("Holloway", $value['last_name']);
    }
}
