<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

abstract class Driver {
    /**
     * Instance of the query builder to use
     * @var {Mixed}
     */
    protected $query;
    
    /**
     * Instance of the column factory
     * @var {Object} Daveawb\Datatables\Columns\Factory
     */
    protected $factory;
	
    /**
     * Set the query object on the driver
     * @param {Mixed} Query builder
     */
    abstract public function query($query);


    /**
     * Get the results from the built query
     * @return {Array} an array formatted for datatables
     */
    abstract public function get();
    
    /**
     * Set the factory object on the driver
     * @param {Object} Daveawb\Datatables\Columns\Factory
     */
    public function factory(Factory $factory)
    {
        $this->factory = $factory;
    }
}
