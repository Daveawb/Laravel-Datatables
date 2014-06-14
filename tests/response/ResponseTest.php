<?php

class ResponseTest extends DatatablesTestCase {
    
    public function testResponseConstructs()
    {
        $factory = Mockery::mock("Daveawb\Datatables\Columns\Factory");
        //$factory->shouldReceive('getColumns')->once()->andReturn(array());
        
        $response = new Daveawb\Datatables\Response(
            $this->app['config'],
            Mockery::mock("Daveawb\Datatables\Driver"),
            $factory,
            array("foo" => "bar")
        );
        
        $this->assertInstanceOf("Illuminate\Config\Repository", $this->getProperty($response, "config"));
        $this->assertInstanceOf("Daveawb\Datatables\Driver", $this->getProperty($response, "driver"));
        $this->assertInstanceOf("Daveawb\Datatables\Columns\Factory", $this->getProperty($response, "factory"));
        
        $this->assertEquals(array("foo" => "bar"), $this->getProperty($response, "attributes"));
    }
    
    public function testReturnedFormatConformsWithDatatablesAPI()
    {
        $factory = Mockery::mock("Daveawb\Datatables\Columns\Factory");
        $driver = Mockery::mock("Daveawb\Datatables\Driver");
        $input = new stdClass();
        $input->sEcho = 100;
        
        $factory->input = $input;
        $driver->shouldReceive('getTotalRecords')->once()->andReturn(256);
        $driver->shouldReceive('getDisplayRecords')->once()->andReturn(128);
        
        $response = new Daveawb\Datatables\Response(
            $this->app['config'],
            $driver,
            $factory,
            array("foo" => "bar")
        );
        
        $method = $this->getMethod($response, "formattedResponse");
        $data = $method->invoke($response, array(array(0 => "David", 1 => "Barker")));
        
        $this->assertEquals(array(
            "aaData" => array(
                array(
                    "David", "Barker"
                )
            ),
            "iTotalRecords" => 256,
            "iTotalDisplayRecords" => 128,
            "sEcho" => 100
        ), $data);
    }
}
