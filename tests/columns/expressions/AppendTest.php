<?php

class AppendTest extends DatatablesTestCase {
    
    public function testDataIsAppendedToEndOfFirstField()
    {
        $data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb"
        );
        
        $append = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $append->evaluate(array(
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
        
        $append = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
            "last_name"
        ), $data);
        
        $result = $append->evaluate(array(
            "append", " "
        ));
        
        $this->assertEquals("David append", $result);
    }
	
	public function testDataPassedAsAnArrayIsEvaluatedAndAppended()
	{
		$data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb",
            "title" => "Mr"
        );
        
        $append = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "title",
        ), $data);
        
        $result = $append->evaluate(array(
            array("first_name", "last_name", "username")," "
        ));
        
        $this->assertEquals("Mr David Barker daveawb", $result);
	}
	
	public function testDataPassedIsSameInDataOutputFieldData()
	{
		$data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb",
            "title" => "Mr"
        );
        
        $append = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
        ), $data);
        
        $result = $append->evaluate(array(
            "last_name"," "
        ));
        
        $this->assertEquals("David Barker", $result);
	}
	
	public function testPlainEvaluationDoesNotIncludeField()
	{
		$data = array(
            "first_name" => "David",
            "last_name" => "Barker",
            "username" => "daveawb",
            "title" => "Mr"
        );
        
        $append = new Daveawb\Datatables\Columns\Expressions\Append(array(
            "first_name",
        ), $data);
        
        $result = $append->plain(array(
            "last_name"," "
        ));
        
        $this->assertEquals(" Barker", $result);
	}
}