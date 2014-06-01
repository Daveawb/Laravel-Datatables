<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as FluentBuilder;
use Illuminate\Http\JsonResponse;

use ErrorException;

class Datatable {
    
    /**
     * Instance of the column factory
     * @var {Object} Daveawb\Datatables\Columns\Factory
     */
    protected $factory;

    /**
     * The Eloquent model to use in our query
     * @var {Mixed} An instance of a model or builder
     */
    protected $model;
	
	/**
	 * The driver to use for the database
	 * @var {Object} Daveawb\Datatables\Driver
	 */
	protected $driver;

    /**
     * Attributes that are applied to each row
     * @var {Array} row attributes
     */
    protected $attributes = array();

    /**
     * Construct and get all the Input
     * @param {Object} Daveawb\Datatables\Support\Input
     * @return void
     */
    public function __construct(Factory $factory, Driver $driver, JsonResponse $json)
    {
        $this->factory = $factory;
		$this->driver = $driver;
        $this->json = $json;
    }
    
    /**
     * Dual purpose getter and setter for row attributes
     * setter : Inject a row attribute into the Datatable
     * getter : return row attribute by name
     * @param {String} attribute name
     * @param {Mixed} attribute value
     * @return {Mixed} void on set, attribute value on get
     */
    public function attribute($name, $value = null)
    {
        if (is_null($value) && array_key_exists($name, $this->attributes))
            return $this->attributes[$name];
        else if (is_null($value))
            return $value;

        $this->attributes[$name] = $value;
    }
    
    /**
     * Set the columnar data required to build output
     * @param {Array} columnar data
     */
    public function columns(array $columns)
    {
    	// Validate the columns against a set of rules
    	$this->factory->validate($columns);
		
        foreach($columns as $key => $column)
        {
            $this->factory->create($column, $key);
        }
    }
    
    /**
     * Dual purpose getter and setter for input
     * setter : Inject an input attribute
     * getter : return input attribute by name
     * @param {String} attribute name
     * @param {Mixed} attribute value
     * @return {Mixed} void on set, attribute value on get
     */
    public function input($name)
    {
        return $this->input->{$name};
    }
    
    /**
     * Inject a model - The driver checks for the correct types
	 * and does not need to be done here. This is to allow for
	 * multiple drivers using different objects / configurations
	 * in the future.
	 * @param {Mixed} Data model to use
     */
    public function model($model)
    {
        $this->model = $model;
    }
    
    /**
     * Alias to model that sets a fluent query builder on the class
     * instead of a model.
     * @param Illuminate\Database\Query\Builder
     */
    public function query(FluentBuilder $builder)
    {
        $this->model = $builder;
    }
    
    /**
     * Gather results using the default driver or a specified
	 * driver that has been injected.
	 * @return {Object} Illuminate\Http\JsonResponse
     */
    public function result()
    {
    	$this->driver->setup($this->model, $this->factory);
        
        return $this->response($this->driver->get());
    }
    
	/**
	 * Build a response object using the result data set and
	 * the columns factory to configure and order results.
	 * @param {Array} of results from the driver
	 * @return {Object} Illuminate\Http\JsonResponse
	 */
    protected function response($results)
    {
        $response = new Response($this->factory->getColumns(), $results);
        
        return $this->json->setData($response->get());
    }
}
