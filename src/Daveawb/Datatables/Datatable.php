<?php namespace Daveawb\Datatables;

use Daveawb\Datatables\Support\Input;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use ErrorException;

class Datatable implements DatatableInterface {
	
	/**
	 * The Eloquent model to use in our query
	 * @var {Object} Illuminate\Database\Eloquent\Model
	 */
	protected $model;
	
	/**
	 * Construct and get all the Input
	 * @param {Object} Daveawb\Datatables\Support\Input
	 * @return void
	 */
	public function __construct(Input $input) 
	{
		$this->input = $input->build()->get();
	}
	
	/**
	 * Dual purpose getter and setter for models
	 * setter : Inject a model into the Datatable
	 * getter : return current model
	 * @param {Object} Illuminate\Database\Eloquent\Model
	 * @return {Mixed} void on set, Illuminate\Database\Eloquent\Model on get
	 */
	public function model($model = null)
	{
		if (is_null($model))
			return $this->model;
		
		if ($model instanceof Model)
			$this->model = $model;
		else
			throw new ErrorException(
				sprintf("Argument 1 passed to %s must be an instance of %s, %s given", get_class($this), get_class(Model), $model)
			);
	}
	
	/**
	 * Set the model to an Eloquent builder instance allowing
	 * complex queries to be run before datatables applys
	 * its own queries.
	 * @param {Object} Illuminate\Database\Eloquent\Builder
	 * @return void
	 */
	public function builder(Builder $builder)
	{
		$this->model = $builder;
	}
	
	/**
	 * Dual purpose getter and setter for row attributes
	 * setter : Inject a row attribute into the Datatable
	 * getter : return row attribute by name
	 * @param {String} attribute name
	 * @param {Mixed} attribute value
	 * @return {Mixed} void on set, attribute value on get
	 */
	public function rowAttribute($name, $value = null)
	{
		if (is_null($value) && array_key_exists($name, $this->rowAttributes))
			return $this->rowAttributes[$name];
		else if (is_null($value))
			return $value;

		$this->rowAttributes[$name] = $value;
	}
}