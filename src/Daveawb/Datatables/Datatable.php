<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;

/** @noinspection PhpInconsistentReturnPointsInspection */
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
     * @param Factory $factory
     * @param Driver $driver
     * @param JsonResponse $json
     * @param Repository $config
     */
    public function __construct(Factory $factory, Driver $driver, JsonResponse $json, Repository $config)
    {
        $this->factory = $factory;
        $this->driver = $driver;
        $this->json = $json;
        $this->config = $config;
        
        $this->driver->setConfig($config);
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
     * @param array $columns
     * @throws ValidationException
     */
    public function columns(array $columns)
    {
        $this->factory->validate($columns);
        
        foreach ($columns as $key => $column)
        {
            $this->factory->create($column, $key);
        }
    }

    /**
     * Set a different driver to use instead of
     * current default loaded driver
     * @param Driver $driver
     */
    public function driver(Driver $driver)
    {
        $this->driver = $driver;
        
        $this->driver->setConfig($this->config);
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
     * Gather results using the default driver or a specified
     * driver that has been injected.
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function result()
    {
        $this->driver->factory($this->factory);

        return $this->response();
    }

    /**
     * Build a response object using the result data set and
     * the columns factory to configure and order results.
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function response()
    {
        $response = new Response($this->config, $this->driver, $this->factory, $this->attributes);

        return $this->json->setData($response->get());
    }
}
