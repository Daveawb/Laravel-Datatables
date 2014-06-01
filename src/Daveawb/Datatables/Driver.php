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
     * Query class constructor
     * @param {Mixed} Query builder
     * @param {Object} Daveawb\Datatables\Columns\Factory
     */
    abstract public function __construct($query, Factory $factory);


    /**
     * Get the results from the built query
     * @return {Array} an array formatted for datatables
     */
    abstract public function get();
}
