<?php

class LaravelDriverModuleTests extends DatatablesTestCase {
    
    public function setUp()
    {
        parent::setUp();
        
        $this->app['request']->replace($this->testData);
    }
    
    /**
     * Module tests
     */
    public function testResultsAreSortedAscending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => true,
            "iSortCol_0" => 0,
            "sSortDir_0" => "asc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals("Barry", $data->aaData[0][0]);
    }
    
    public function testResultsAreSortedAscendinUsingQueryBuilder()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => true,
            "iSortCol_0" => 0,
            "sSortDir_0" => "asc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users'));
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());

        $this->assertEquals("Barry", $data->aaData[0][0]);
    }
    
    public function testResultsAreSortedDescending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => true,
            "iSortCol_0" => 0,
            "sSortDir_0" => "desc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals("Englebert", $data->aaData[0][0]);
    }
    
    public function testResultsAreSortedDescendingUsingQueryBuilder()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => true,
            "iSortCol_0" => 0,
            "sSortDir_0" => "desc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users'));
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertEquals("Englebert", $data->aaData[0][0]);
    }
    
    public function testMultipleFieldsAreSortedAscending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => false,
            "iSortCol_0" => 0,
            "sSortDir_0" => "asc"
        ));
        
        $expected = array(
            "Barry Evans",
            "Barry Manilow",
            "Barry Scott",
            "Barry White",
            "Barry Williams"
        );
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(with(new UserModel())->where('first_name', '=', 'Barry'));
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            array("id")
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        for ($i = 0; $i < count($data->aaData); $i++)
        {
            $this->assertEquals($expected[$i], $data->aaData[$i][0]);
        }
    }
    
    public function testMultipleFieldsAreSortedAscendingUsingQueryBuilder()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => false,
            "iSortCol_0" => 0,
            "sSortDir_0" => "asc"
        ));
        
        $expected = array(
            "Barry Evans",
            "Barry Manilow",
            "Barry Scott",
            "Barry White",
            "Barry Williams"
        );
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users')->where('first_name', '=', 'Barry'));
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            array("id")
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        for ($i = 0; $i < count($data->aaData); $i++)
        {
            $this->assertEquals($expected[$i], $data->aaData[$i][0]);
        }
    }
    
    public function testMultipleFieldsAreSortedDescending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => false,
            "iSortCol_0" => 0,
            "sSortDir_0" => "desc"
        ));
        
        $expected = array(
            "Barry Evans",
            "Barry Manilow",
            "Barry Scott",
            "Barry White",
            "Barry Williams"
        );
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(with(new UserModel())->where('first_name', '=', 'Barry'));
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            array("id")
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        for ($i = 0; $i < count($data->aaData); $i++)
        {
            $this->assertEquals($expected[4 - $i], $data->aaData[$i][0]);
        }
    }
    
    public function testMultipleFieldsAreSortedDescendingUsingQueryBuilder()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => true,
            "bSortable_1" => false,
            "iSortCol_0" => 0,
            "sSortDir_0" => "desc"
        ));
        
        $expected = array(
            "Barry Evans",
            "Barry Manilow",
            "Barry Scott",
            "Barry White",
            "Barry Williams"
        );
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users')->where('first_name', '=', 'Barry'));
        
        $datatable->columns(array(
            array("first_name", "last_name", array("combine" => "first_name,last_name, ")),
            array("id")
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        for ($i = 0; $i < count($data->aaData); $i++)
        {
            $this->assertEquals($expected[4 - $i], $data->aaData[$i][0]);
        }
    }

    public function testSearchReturnsOnlyResultsWithSearchString()
    {
        $testData = array_merge($this->testData, array(
            "bSearchable_0" => true,
            "bSearchable_1" => false,
            "sSearch" => "Barry"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(new UserModel());
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertCount(5, $data->aaData);
    }
    
    public function testSearchReturnsOnlyResultsWithSearchStringUsingQueryBuilder()
    {
        $testData = array_merge($this->testData, array(
            "bSearchable_0" => true,
            "bSearchable_1" => false,
            "sSearch" => "Barry"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users'));
        
        $datatable->columns(array(
            "first_name",
            "last_name"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertCount(5, $data->aaData);
    }
    
    public function testTableJoinsReturnCorrectData()
    {
        $this->app['request']->replace($this->testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users')
            ->select(DB::raw('users.*, group_concat(roles.role) as role'))
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->groupBy('users.id')
        );
            
        $datatable->columns(array(
            "first_name",
            "role"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertCount(6, $data->aaData);
        $this->assertEquals(6, $data->iTotalRecords);
        $this->assertEquals(6, $data->iTotalDisplayRecords);
        
        foreach($data->aaData as $data) 
        {
            $this->assertTrue($data[1] === "admin" || $data[1] === 'user' || $data[1] === 'admin,user');
        }
    }
    
    
    public function testTableJoinsAllowSortingByJoinedValuesAscending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => false,
            "bSortable_1" => true,
            "iSortCol_0" => 1,
            "sSortDir_0" => "asc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users')
            ->select(DB::raw('users.*, group_concat(roles.role) as role'))
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->groupBy('users.id')
        );
            
        $datatable->columns(array(
            "first_name",
            "role"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertCount(6, $data->aaData);
        $this->assertEquals(6, $data->iTotalRecords);
        $this->assertEquals(6, $data->iTotalDisplayRecords);
        
        foreach($data->aaData as $key => $data) 
        {
            if ($key <= 1)
                $this->assertTrue($data[1] === "admin,user");
            else
                $this->assertTrue($data[1] === 'user');
        }
    }

    public function testTableJoinsAllowSortingByJoinedValuesDescending()
    {
        $testData = array_merge($this->testData, array(
            "bSortable_0" => false,
            "bSortable_1" => true,
            "iSortCol_0" => 1,
            "sSortDir_0" => "desc"
        ));
        
        $this->app['request']->replace($testData);
        
        $datatable = $this->getDatatable();
        
        $datatable->query(DB::table('users')
            ->select(DB::raw('users.*, group_concat(roles.role) as role'))
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->groupBy('users.id')
        );
            
        $datatable->columns(array(
            "first_name",
            "role"
        ));
        
        $result = $datatable->result();
        
        $data = json_decode($result->getContent());
        
        $this->assertCount(6, $data->aaData);
        $this->assertEquals(6, $data->iTotalRecords);
        $this->assertEquals(6, $data->iTotalDisplayRecords);
        
        foreach($data->aaData as $key => $data) 
        {
            if ($key >= 4)
                $this->assertTrue($data[1] === "admin,user");
            else
                $this->assertTrue($data[1] === 'user');
        }
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
