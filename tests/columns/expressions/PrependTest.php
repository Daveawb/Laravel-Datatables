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
	
	public function testDataPassedAsAnArrayIsEvaluatedAndAppended()
	{
		$data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb",
            "title" => "Mr"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Prepend(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->evaluate(array(
            array("prepended item:","title", "prepended to"), " "
        ));
        
        $this->assertEquals("prepended item: Mr prepended to David", $result);
	}
	
	public function testPlainEvaluationDoesNotReturnField()
	{
		$data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb",
            "title" => "Mr"
        );
        
        $combine = new Daveawb\Datatables\Columns\Expressions\Prepend(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $combine->plain(array(
            array("prepended item:","title", "prepended to"), " "
        ));
        
        $this->assertEquals("prepended item: Mr prepended to ", $result);
	}
	
}