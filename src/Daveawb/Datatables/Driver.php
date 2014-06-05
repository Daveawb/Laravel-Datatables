<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

use Illuminate\Config\Repository;

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
     * Configuration options
     * @var {Array}
     */
    protected $config;
    
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
     * Inject configuration options into the driver
     * @param {Array} of configuration options
     */
    abstract public function config(array $config);
    
    /**
     * Get the configuration name used for this driver
     * @return {String}
     */
    abstract protected function getConfigName();
    
    /**
     * Set the factory object on the driver
     * @param {Object} Daveawb\Datatables\Columns\Factory
     */
    public function factory(Factory $factory)
    {
        $this->factory = $factory;
    }
    
    /**
     * Set the config class on this driver
     * @param {Object} Illuminate\Config\Repository
     */
    public function setConfig(Repository $config)
    {
        $driverConfig = $this->getConfigName();
        
        $cfgArray = $driverConfig === "laravel" ?
            array() :
            $config->get("datatables::database.connections.{$driverConfig}");
        
        $this->config($cfgArray);
    }
}
