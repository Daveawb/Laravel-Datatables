<?php

class PrependTest extends DatatablesTestCase {
    
    public function testDataIsPrependedToEndOfFirstField()
    {
        $data = new stdClass();
        $data->first_name = "David";
        $data->last_name = "Barker";
        $data->username = "daveawb";
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Prepend(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->evaluate(array(
            "prepend"
        ));
        
        $this->assertEquals("prependDavid", $result);
    }
    
    public function testDataIsPrependedToEndOfFirstFieldWithSeperator()
    {
        $data = new stdClass();
        $data->first_name = "David";
        $data->last_name = "Barker";
        $data->username = "daveawb";
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Prepend(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->evaluate(array(
            "prepend", " "
        ));
        
        $this->assertEquals("prepend David", $result);
    }
}