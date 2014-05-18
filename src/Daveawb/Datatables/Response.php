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
                $filtered[$rowKey][$column->mDataProp] = $result->{$column->name};
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