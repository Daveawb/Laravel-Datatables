<?php
namespace Daveawb\Datatables;

use Daveawb\Datatables\Columns\Factory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as FluentBuilder;

use ErrorException;

class Datatable implements DatatableInterface {
    
    /**
     * Instance of the column factory
     * @var {Object} Daveawb\Datatables\Columns\Factory
     */
    protected $input;
    
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
     * Column specifications
     * @var {Array}
     */
    protected $columns = array();

    /**
     * Construct and get all the Input
     * @param {Object} Daveawb\Datatables\Support\Input
     * @return void
     */
    public function __construct(Input $input, Factory $factory)
    {
        $this->factory = $factory;
        $this->input = $input->build();
        
        $this->factory->input($input);
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
        // Validate column data against expected data
        $this->validateColumns($columns);
        
        foreach($columns as $key => $column)
        {
            $this->columns[$key] = $this->factory->create($column, $key);
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
        $query = new Query($this->model, $this->input, $this->columns);
        
        return $this->response($query->get());
    }
    
    public function response($results)
    {
        //dd($results);
        $response = new Response($this->columns, $results);
        $response->filter();
    }

    /**
     * Validate columnar data against input to maintain
     * consistency before proceeding.
     * @var {Array} columnar data
     */
    private function validateColumns(array $columns)
    {
        // Check that we have the correct number of columns
        if (count($columns) !== $this->input->iColumns)
        {
            throw new ColumnCountException(
                sprintf(
                    "%s columns given, expected %s",
                    count($columns),
                    $this->input->iColumns
                )
            );
        }
    }

}
