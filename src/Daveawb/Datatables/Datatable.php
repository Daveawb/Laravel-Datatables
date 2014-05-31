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
     * @var {Object} Illuminate\Database\Eloquent\Model
     */
    protected $model;

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
    public function __construct(Factory $factory, JsonResponse $json)
    {
        $this->factory = $factory;
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
     * Dual purpose getter and setter for models
     * setter : Inject a model into the Datatable
     * getter : return current model
     * @param {Object} Illuminate\Database\Eloquent\Model ||
     * Illuminate\Database\Eloquent\Builder
     * @return {Mixed} void on set, Illuminate\Database\Eloquent\Model on get
     */
    public function model($model)
    {
        if ($model instanceof Model || $model instanceof Builder)
        {
            $this->model = $model;
        }
        else
        {
            throw new ErrorException(
                sprintf(
                    "Argument 1 passed to %s must be an instance of %s or %s, %s given", 
                    get_class($this), 
                    Model,
                    Builder,
                    $model
                )
            );
        }
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
     * Gather results
     */
    public function result()
    {
        $query = new Query($this->model, $this->factory);
        
        return $this->response($query->get());
    }
    
    protected function response($results)
    {
        $response = new Response($this->factory->getColumns(), $results);
        
        return $this->json->setData($response->get());
    }
}
