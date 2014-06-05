<?php

class CombineTest extends DatatablesTestCase {
    
    public function testCombineTakesInNumericIndexes()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Combine(array(
            "first_name",
            "last_name",
            "username"
        ), $data);
        
        $result = $combine->evaluate(array(
            "0", "1", "2", " "
        ));
        
        $this->assertEquals("David Barker daveawb", $result);
    }
    
    public function testCombineTakesInFieldNames()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Combine(array(
            "first_name",
            "last_name",
            "username"
        ), $data);
        
        $result = $combine->evaluate(array(
            "first_name", "last_name", "username", " "
        ));
        
        $this->assertEquals("David Barker daveawb", $result);
    }
}
