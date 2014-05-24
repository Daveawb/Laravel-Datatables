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
    	// First we need to cache the aggregate query and columns
    	$aggregate = $this->query->aggregate;
		$columns = $this->query->columns;
		
        $this->totalCount = $this->query->count();
		
		$q = $this->query;
		
		// Re-populate the query with prior query data
		$q->aggregate = $aggregate;
		$q->columns = $columns;
        
        foreach($this->columns as $key => $column)
        {
            if ( ! empty($column->sSearch) ) 
            {
                $q = $q->orWhere($column->name, 'LIKE', '%' . $column->sSearch . '%');
            }
            
            if ( $column->sortable )
            {
                $q = $q->orderBy($column->name, $column->sortDirection);
            }
        }
        
		// We need to cache the aggregate query and columns again
    	$aggregate = $this->query->aggregate;
		$columns = $this->query->columns;
		
        $this->filteredCount = $this->query->count();
		
		// and Re-populate the query with prior query data again
		$q->aggregate = $aggregate;
		$q->columns = $columns;
        
        return $q->skip($this->input->iDisplayStart)->limit($this->input->iDisplayLength);
    }
    
    public function get()
    {
        $data = $this->build()->get();
     	       
        return array(
            "sEcho" => $this->input->sEcho,
            "aaData" => $data,
            "iTotalDisplayRecords" => $this->filteredCount,
            "iTotalRecords" => $this->totalCount
        );
    }
}
