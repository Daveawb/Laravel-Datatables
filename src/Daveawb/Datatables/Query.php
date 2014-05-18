<?php namespace Daveawb\Datatables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as Fluent;
use Illuminate\Database\Eloquent\Builder;

use ErrorException;

class Query {
    
    protected $query;
    
    public function __construct($query, Input $input, array $columns)
    {
        if( ! $query instanceof Model && ! $query instanceof Builder && ! $query instanceof Fluent )
        {
            throw new ErrorException(
                sprintf(
                    "Argument 1 passed to %s must be an instance of %s, %s, or %s, %s given", 
                    get_class($this),
                    Model, 
                    Builder,
                    Fluent,
                    $query
                )
            );
        }
        
        $this->query = $query;
        $this->input = $input;
        $this->columns = $columns;
    }
    
    protected function build()
    {
        $this->totalCount = $this->query->count();
        
        foreach($this->columns as $key => $column)
        {
            if ( ! empty($column->sSearch) ) 
            {
                $this->query->orWhere($column->name, 'LIKE', '%' . $column->sSearch . '%');
            }
            
            if ( $column->sortable )
            {
                $this->query->orderBy($column->name, $column->sortDirection);
            }
        }
        
        $this->filteredCount = $this->query->count();
        
        $this->query->skip($this->input->iDisplayStart)->limit($this->input->iDisplayLength);
    }
    
    public function get()
    {
        $this->build();
        
        $data = ($this->query instanceof Illuminate\Database\Eloquent\Builder) ?
            $this->query->get()->toArray() :
            $this->query->get();
            
        return array(
            "sEcho" => $input->sEcho,
            "aaData" => $data,
            "iTotalDisplayRecords" => $this->filteredCount,
            "iTotalRecords" => $this->totalCount
        );
    }
}
