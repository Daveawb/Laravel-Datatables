<?php

class ColumnTest extends DatatablesTestCase {
    
    public function testConstructorTakesFieldsAndSettings()
    {
        $column = new Daveawb\Datatables\Columns\Column($fields = array(
                "id"
            ),
            $settings = array(
                "mDataProp" => 0,
                "bSearchable" => false,
                "bSortable" => true,
                "bRegex" => false,
                "sSearch" => ""
            )
        );
        
        $this->assertEquals($fields, $this->getProperty($column, "fields"));
        $this->assertEquals($settings, $this->getProperty($column, "attributes"));
        
        return $column;
    }
    
    /**
     * @depends testConstructorTakesFieldsAndSettings
     */
    public function testGetAndSetAttributes($column)
    {
        $this->assertEquals(0, $column->mDataProp);
        
        $column->mDataProp = 1;
        
        $this->assertEquals(1, $column->mDataProp);
    }
    

    public function testInterprationLogicIsSetAndSeparatedUsingCommaSeparatedValues()
    {
        $column = new Daveawb\Datatables\Columns\Column($fields = array(
                "id", "first_name", array("combine" => "0,1,2,3,4,5,6,7")
            ),
            $settings = array(
                "mDataProp" => 0,
                "bSearchable" => false,
                "bSortable" => true,
                "bRegex" => false,
                "sSearch" => ""
            )
        );
        
        $method = $this->getMethod($column, "getInterpretationData");
        $data = $method->invoke($column);
        
        $this->assertTrue(is_array($data));
        
        $this->assertEquals(array(
            "combine" => array("0", "1", "2", "3", "4", "5", "6", "7")
        ), $data);
    }
    
    public function testInterpratationLogicIsSetAsAClosure()
    {
        $column = new Daveawb\Datatables\Columns\Column($fields = array(
                "id", "first_name", function($id, $first_name, $document)
                {
                    return $id . ' ' . $first_name;
                }
            ),
            $settings = array(
                "mDataProp" => 0,
                "bSearchable" => false,
                "bSortable" => true,
                "bRegex" => false,
                "sSearch" => ""
            )
        );
        
        $method = $this->getMethod($column, "getInterpretationData");
        $data = $method->invoke($column);
        
        $this->assertInstanceOf("Closure", $data);
        $this->assertEquals($data(1, "David", array()), "1 David");
    }
    
    public function testDataIsReturnedAfterRunningThroughInterpreter()
    {
        $column = new Daveawb\Datatables\Columns\Column(array(
                "first_name", "last_name", array("combine" => "0,1, ")
            ),
            $settings = array(
                "mDataProp" => 0,
                "bSearchable" => false,
                "bSortable" => true,
                "bRegex" => false,
                "sSearch" => ""
            )
        );
        
        $dbData = new stdClass();
        $dbData->first_name = "David";
        $dbData->last_name = "Barker";
        $dbData->username = "daveawb";
        
        $data = $column->interpret("first_name", $dbData);
        
        $this->assertEquals("David Barker", $dbData->first_name);
    }
}
