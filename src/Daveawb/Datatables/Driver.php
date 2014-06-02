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
	 * Set the query object on the driver
	 * @param {Mixed} Query builder
	 */
	abstract public function query($query);
    /**
     * Set the factory object on the driver
     * @param {Object} Daveawb\Datatables\Columns\Factory
     */
    abstract public function factory(Factory $factory);


    /**
     * Get the results from the built query
     * @return {Array} an array formatted for datatables
     */
    abstract public function get();
}
