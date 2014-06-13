<?php namespace Daveawb\Datatables;

class Response {
    
    public function __construct(array $columns, array $results, array $attributes)
    {
        $this->columns = $columns;
        $this->results = $results;
        $this->attributes= $attributes;
        $this->data = $results['aaData'];
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
        
        $this->results['aaData'] = $filtered;
    }
    
    public function get()
    {
        $this->filter();
        
        return $this->results;
    }
}