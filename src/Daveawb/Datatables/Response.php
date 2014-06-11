<?php namespace Daveawb\Datatables;

class Response {
    
    public function __construct(array $columns, array $results, array $attributes)
    {
        $this->columns = $columns;
        $this->results = $results;
        $this->data = $results['aaData'];
        $this->attributes = $attributes;
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
            
            $filtered[$i] = array_merge($filtered[$i], $this->attributes($this->results['aaData'][$i]));
        }
        
        $this->results['aaData'] = $filtered;
    }
    
    public function get()
    {
        $this->filter();
        
        return $this->results;
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