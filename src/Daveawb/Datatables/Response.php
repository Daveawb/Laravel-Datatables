<?php namespace Daveawb\Datatables;

class Response {
    
    public function __construct(array $columns, array $results)
    {
        $this->columns = $columns;
        $this->results = $results;
        $this->data = $results['aaData'];
    }
    
    public function filter()
    {
        $filtered = array();
        
        foreach($this->data as $rowKey => $result)
        {
            foreach($this->columns as $key => $column)
            {
                $column->interpret($column->fields[0], $result);
                
                $filtered[$rowKey][$column->mDataProp] = $result->{$column->fields[0]};
            }
        }
        
        $this->results['aaData'] = $filtered;
    }
    
    public function get()
    {
        $this->filter();
        
        return $this->results;
    }
}