<?php namespace Daveawb\Datatables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as Fluent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

use ErrorException;

class Query {
    
    protected $query;
	
	protected $builders = array();
    
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
		
    	$this->cacheQuery('initial');
    }
    
    protected function build()
    {
        $q = $this->query;
		
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
        
		$this->cacheQuery('result');
        
        return $q->skip($this->input->iDisplayStart)->limit($this->input->iDisplayLength);
    }
    
    public function get()
    {
        $data = $this->build()->get();
		
        return array(
            "sEcho" => $this->input->sEcho,
            "aaData" => $data,
            "iTotalDisplayRecords" => $this->getFilteredCount(),
            "iTotalRecords" => $this->getTotalCount()
        );
    }
	
	protected function cacheQuery($ns)
	{
		$query = $this->query;
		
		if ($query instanceof \Illuminate\Database\Eloquent\Builder)
			$query = $query->getQuery();

		$this->builders[$ns] = clone($query);
	}

	
	protected function getTotalCount()
	{
		$query = $this->builders['initial'];
		
		$query = $query->addSelect(new Expression('count(*) as dttotalcount'));

		return (int) $query->first()->dttotalcount;
	}
	
	protected function getFilteredCount()
	{
		$query = $this->builders['result'];
			
		$query = $query->addSelect(new Expression('count(*) as dtfilteredcount'));
		
		return (int) $query->first()->dtfilteredcount;
	}
}
