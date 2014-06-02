<?php

class AppendTest extends DatatablesTestCase {
    
    public function testDataIsAppendedToEndOfFirstField()
    {
        $data = new stdClass();
        $data->first_name = "David";
        $data->last_name = "Barker";
        $data->username = "daveawb";
        
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
        $data = new stdClass();
        $data->first_name = "David";
        $data->last_name = "Barker";
        $data->username = "daveawb";
        
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