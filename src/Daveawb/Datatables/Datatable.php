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

        foreach ($columns as $key => $column)
        {
            $this->factory->create($column, $key);
        }
    }
    
    public function driver(Driver $driver)
    {
        $this->driver = $driver;
    }
    
    /**
     * Inject a query object, string or configuration
	 * into the driver. Type checking is done in the 
	 * driver. This method acts as a proxy to the query
	 * method on the driver.
	 * @param {Mixed} Driver configuration
     */
    public function query($query)
    {
        $this->driver->query($query);
    }

    /**
     * Build a response object using the result data set and
     * the columns factory to configure and order results.
     * @param {Array} of results from the driver
     * @return {Object} Illuminate\Http\JsonResponse
     */
    private function response($results)
    {
        $response = new Response($this->factory->getColumns(), $results);

        return $this->json->setData($response->get());
    }
    
    /**
     * Gather results using the default driver or a specified
     * driver that has been injected.
     * @return {Object} Illuminate\Http\JsonResponse
     */
    public function result()
    {
        $this->driver->factory($this->factory);

        return $this->response($this->driver->get());
    }
}
