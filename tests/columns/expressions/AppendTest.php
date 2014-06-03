<?php

class AppendTest extends DatatablesTestCase {
    
    public function testDataIsAppendedToEndOfFirstField()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->evaluate(array(
            "append"
        ));
        
        $this->assertEquals("Davidappend", $result);
    }
    
    public function testDataIsAppendedToEndOfFirstFieldWithSeperator()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->evaluate(array(
            "append", " "
        ));
        
        $this->assertEquals("David append", $result);
    }
}