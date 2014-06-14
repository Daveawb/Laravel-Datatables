<?php
namespace Daveawb\Datatables\Columns\Input;

use Illuminate\Http\Request;

abstract class BaseInput {

    /**
     * Array of fields that have only one value from the input
     * @var {Array}
     */
    protected $globalFields = array();

    /**
     * Array of fields that use iColumns as their iterator
     * @var {Array}
     */
    protected $columnFields = array();

    /**
     * Array of fields that use iSortingCols as their iterator
     * @var {Array}
     */
    protected $sortingFields = array();

    /**
     * Array of mapped attributes
     * @var {Array}
     */
    protected $attributes = array();

    /**
     * Instance of request
     * @var {Object} Illuminate\Http\Request
     */
    protected $request;

    /**
     * Initialized flag, if true the class will
     * not rebuild from raw input.
     * @var {Boolean}
     */
    protected $initialized = false;

    /**
     * We need to dependency inject the request object.
     * @param {Object} Illuminate\Http\Request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the mapped attributes
     * @return {Array}
     */
    public function get()
    {
        $this->build();

        return $this->attributes;
    }

    /**
     * Build the input into the format
     * this package requires
     */
    protected function build()
    {
        if ($this->initialized)
            return $this;

        $this->initialized = true;

        return $this->mapGlobals()->mapColumns()->mapSorting();
    }

    /**
     * Gather settings for a specific column
     * keyed by $key
     * @param {Integer} column key
     */
    public function getColumn($key)
    {
        $this->build();

        return $this->attributes[$key];
    }

    /**
     * Gather the global settings or get
     * a single global setting.
     */
    public function getGlobals($field = null)
    {
        $this->build();

        if ( ! is_null($field) && array_key_exists($field, $this->attributes['global']))
            return $this->attributes['global'][$field];

        return $this->attributes['global'];
    }

    /**
     * Map allowed fields to the attributes array
     *
     * @param {Array}
     * @return {Object} Self
     */
    abstract protected function mapGlobals();

    /**
     * Map iterative fields to the attributes array
     * using the values assigned in the allowed fields.
     * This allows us to create the correct number of
     * fields based on number of columns / sortable fields
     * amongst other types that can be added if required.
     *
     * @param {Array}
     * @return {Object} Self
     */
    abstract protected function mapColumns();

    /**
     * Get sorting data and apply it to the correct column
     * @return {Object} Self
     */
    abstract protected function mapSorting();

    /**
     * Magic method to get a specific attribute from the global
     * attributes array. This can't be used to get specific
     * column data.
     * @param {String} The name of the attribute
     * @return {Mixed} The value of the attribute or null
     */
    public function __get($name)
    {
        $self = $this->build();

        if (array_key_exists($name, $self->attributes['global']))
            return $self->attributes['global'][$name];

        return null;
    }

    /**
     * Magic method to set a specific attribute on the global
     * attributes array.
     * @param {String} Attribute name
     * @param {Mixed} Attribute value
     * @return void
     */
    public function __set($name, $value)
    {
        $self = $this->build();
        $self->attributes['global'][$name] = $value;
    }

}
