<?php namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;
use Daveawb\Datatables\Driver;

use Illuminate\Config\Repository;

class Response {
    
    /**
     * Configuration object
     * @var {Object} Illuminate\Config\Repository
     */
    protected $config;
    
    public function __construct(Repository $config, Driver $driver, Factory $columns, array $attributes)
    {
        $this->config = $config;
        $this->driver = $driver;
        $this->columns = $columns->getColumns();
        $this->attributes= $attributes;
    }
    
    public function filter()
    {
        $filtered = array();
        
        for ($i = 0; $i < count($this->data); $i++)
        {
            foreach($this->columns as $key => $column)
            {
                $column->interpret($column->fields[0], $this->data[$i]);
                
                $filtered[$i][$column->mDataProp] = $this->data[$i][$column->fields[0]];
            }
            
            $filtered[$i] = array_merge($filtered[$i], $this->attributes);
        }
        
        $this->results = $filtered;
    }
    
    public function get()
    {
        $this->data = $this->driver->get();
        
        $this->filter();
        
        return $this->results;
    }
    
    private function responseArray()
    {
        return array(
            "aaData" => array(),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "sEcho" => 0
        );
    }
}