<?php
namespace Daveawb\Datatables\Drivers;

use Daveawb\Datatables\Driver;
use Daveawb\Datatables\Columns\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as Fluent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

use ErrorException;

class Laravel extends Driver {

    /**
     * Array of cached query builders to get row counts
     * @var {Array}
     */
    protected $builders = array();

    /**
     * Build the query
     * @return {Mixed} Configured query builder
     */
    protected function build()
    {
        $q = $this->query;

        foreach ($this->factory->getColumns() as $key => $column)
        {
            if ( strlen($column->sSearch) > 0 )
            	$q = $this->buildWhereClause($q, $column);
            
            if ($column->bSortable && $column->sort)
            {
                foreach($column->fields as $field)
                {
                    $q = $q->orderBy($field, $column->sort_dir);    
                }
            }
        }

        $this->cacheQuery();

        return $q->skip($this->factory->input->iDisplayStart)->limit($this->factory->input->iDisplayLength);
    }
	
    /**
     * Add a where clause on the query object
     * @param {Object} Illuminate\Database\Eloquent\Builder || Illuminate\Database\Query\Builder
     * @param {Object} Daveawb\Datatables\Columns\Column
     */
    protected function buildWhereClause($query, $column)
    {
    	$evaluate = $query;
    	
    	if ($evaluate instanceof Builder)
    	    $evaluate = $query->getQuery();
    	
        foreach($column->fields as $field) 
        {
            $query = ( ! isset($evaluate->wheres) && ! is_array($evaluate->wheres) ) ?
	    		$query->where($column->fields[0], 'LIKE', '%' . $column->sSearch . '%') :
	    		$query->orWhere($column->fields[0], 'LIKE', '%' . $column->sSearch . '%');
        }
        
        return $query;
    } 

    /**
     * Cache the query in its current state
     */
    protected function cacheQuery()
    {
        $query = $this->query;

        if ($query instanceof Builder)
            $query = $query->getQuery();

        $this->builders[] = clone($query);
    }
    
    /**
     * Inject configuration into the driver. Usually this is the primary
     * entry point into a driver. This method is called on construct of
     * the main director class || when a driver is swapped in.
     * @param {Array}
     */
    public function config(array $config)
    {
    	// This driver has no config as it only accepts pre-configured
    	// Query builders or an Eloquent builder.
        $this->config = $config;
    }

    /**
     * Get the results from the built query
     * @return {Array} an array formatted for datatables
     */
    public function get()
    {
        $data = $this->build()->get()->toArray();
    }
    
    /**
     * Get the config name for this driver
     * @return {String}
     */
    protected function getConfigName()
    {
        return "laravel";
    }
        
    public function getTotalRecords()
    {
        return $this->getCount(0);
    }
    
    public function getDisplayRecords()
    {
        return $this->getCount(1);
    }
    /**
     * Get the count by retrieving a cached queries results
     * @param {Integer} Index of cached query
     * @return {Integer} Row count for the query
     */
    protected function getCount($index = 0)
    {
        $query = $this->builders[0];

        $query = $query->addSelect(new Expression('count(*) as aggregate'));

        return (int)$query->first()->aggregate;
    }
    
    
    /**
     * Entry point for this class, this method is called first before any other methods
     * do setup for the query class here.
     * @param {Mixed} Query builder
     */
    public function query($query)
    {
        if ( ! $query instanceof Model && ! $query instanceof Builder && ! $query instanceof Fluent)
        {
            throw new ErrorException(
                sprintf(
                    "Argument 1 passed to %s must be an instance of %s, %s, or %s, %s given", 
                    get_class($this), 
                    "Illuminate\Database\Eloquent\Model", 
                    "Illuminate\Database\Eloquent\Builder", 
                    "Illuminate\Database\Query\Builder", 
                    get_class($query)
                )
            );
        }

        $this->query = $query;

        $this->cacheQuery();
    }
}
