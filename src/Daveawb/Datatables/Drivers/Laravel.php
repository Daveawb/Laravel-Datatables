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
     * Instance of the query builder to use
     * @var {Mixed}
     */
    protected $query;

    /**
     * Array of cached query builders to get row counts
     * @var {Array}
     */
    protected $builders = array();

    /**
     * Entry point for this class, this method is called first before any other methods
	 * do setup for the query class here.
     * @param {Mixed} Query builder
     * @param {Object} Daveawb\Datatables\Columns\Factory
     */
    public function setup($query, Factory $factory)
    {
        if ( ! $query instanceof Model && ! $query instanceof Builder && ! $query instanceof Fluent)
        {
            throw new ErrorException(sprintf("Argument 1 passed to %s must be an instance of %s, %s, or %s, %s given", get_class($this), "Illuminate\Database\Eloquent\Model", "Illuminate\Database\Eloquent\Builder", "Illuminate\Database\Query\Builder", get_class($query)));
        }

        $this->query = $query;
        $this->factory = $factory;

        $this->cacheQuery();
    }

    /**
     * Build the query
     * @return {Mixed} Configured query builder
     */
    protected function build()
    {
        $q = $this->query;

        foreach ($this->factory->getColumns() as $key => $column)
        {
            if ( ! empty($column->sSearch))
            	$q = $this->buildWhereClause($q, $column);

            if ($column->bSortable && $column->sort)
                $q = $q->orderBy($column->fields[0], $column->sort_dir);
        }

        $this->cacheQuery();

        return $q->skip($this->factory->input->iDisplayStart)->limit($this->factory->input->iDisplayLength);
    }

    /**
     * Get the results from the built query
     * @return {Array} an array formatted for datatables
     */
    public function get()
    {
        $data = $this->build()->get();

        return array(
            "sEcho" => $this->factory->input->sEcho,
            "aaData" => $data,
            "iTotalDisplayRecords" => $this->getCount(1),
            "iTotalRecords" => $this->getCount(0)
        );
    }
	
	/**
	 * Build a where clause for on the query
	 */
	protected function buildWhereClause($query, $column)
	{
		return (empty($q->wheres)) ?
			$q->where($column->fields[0], 'LIKE', '%' . $column->sSearch . '%') :
			$q->orWhere($column->fields[0], 'LIKE', '%' . $column->sSearch . '%');
	}

    /**
     * Cache the query in its current state
     */
    protected function cacheQuery()
    {
        $query = $this->query;

        if ($query instanceof \Illuminate\Database\Eloquent\Builder)
            $query = $query->getQuery();

        $this->builders[] = clone($query);
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

}