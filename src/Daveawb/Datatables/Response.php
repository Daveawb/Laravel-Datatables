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
    
    /**
     * Query driver
     * @var {Object} Daveawb\Datatables\Driver
     */
    protected $driver;
    
    /**
     * Column factory
     * @var {Object} Daveawb\Datatables\Columns\Factory
     */
    protected $factory;
    
    /**
     * Row attributes
     * @var {Array}
     */
    protected $attributes;
    
    public function __construct(Repository $config, Driver $driver, Factory $factory, array $attributes)
    {
        $this->config = $config;
        $this->driver = $driver;
        $this->factory = $factory;
        $this->attributes= $attributes;
    }
    
    /**
     * Filter the results and organise them by column order. This is the point
     * that column fields are interpreted and applied to the results field.
     * @return {Array}
     */
    public function filter($data)
    {
        $filtered = array();
        
        for ($i = 0; $i < count($data); $i++)
        {
            foreach($this->factory->getColumns() as $key => $column)
            {
                $column->interpret($column->fields[0], $data[$i]);
                
                $filtered[$i][$column->mDataProp] = $data[$i][$column->fields[0]];
            }
            
            $filtered[$i] = array_merge($filtered[$i], $this->attributes($data[$i]));
        }
        
        return $filtered;
    }
    
    public function get()
    {
        return $this->formattedResponse(
            $this->filter(
                $this->driver->get()
            )
        );    
    }
    
    protected function formattedResponse($data)
    {
        return array(
            "aaData" => $data,
            "iTotalRecords" => $this->driver->getTotalRecords(),
            "iTotalDisplayRecords" => $this->driver->getDisplayRecords(),
            "sEcho" => $this->factory->input->sEcho
        );
    }
    
    private function attributes($data)
    {
        $attributes = $this->attributes;
        
        foreach($attributes as &$attribute)
        {
            if (array_key_exists($attribute, $data))
            {
                $attribute = $data[$attribute];
            }
        }
        
        return $attributes;
    }
}