<?php

class InterpreterModuleTests extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
    }
    
    public function testCombineInterpreterReturnsCorrectData()
    {
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Barry Evans");
    }
    
    public function testAppendInterpreterReturnsCorrectData()
    {
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("append" => "%")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Barry%");
    }
    
    public function testMultipleInterpretersReturnCorrectData()
    {
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            array("first_name", "last_name", array("append" => "%", "prepend" => "Mr, ", "combine" => "first_name,last_name, ")),
            "id"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals($data->aaData[0][0], "Mr Barry% Evans");
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
