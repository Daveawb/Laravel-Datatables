<?php

class PrependTest extends DatatablesTestCase {
    
    public function testDataIsPrependedToEndOfFirstField()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
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
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
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