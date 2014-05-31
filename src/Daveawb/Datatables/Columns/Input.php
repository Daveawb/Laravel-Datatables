<?php
namespace Daveawb\Datatables\Columns;

use Daveawb\Datatables\InputMissingException;

use Illuminate\Http\Request;

/**
 * Input class to handle all Datatables input
 * a raw array of datatable information is passed
 * in and an array of attributes useable by the
 * tabularise class is returned
 *
 * @package Daveawb\Datatables
 * @author David Barker
 * @license MIT
 */
class Input {

    /**
     * Array of allowed attributes. These are datatables internal
     * attributes. These attributes are mapped one:one to their
     * respective input attribute.
     * @var {Array}
     */
    protected $globalFields = array(
        'bRegex',
        'iColumns',
        'iDisplayLength',
        'iDisplayStart',
        'iSortingCols',
        'sEcho',
        'sSearch'
    );

    /**
     * Array of iterative attributes. These are datatables internal
     * attributes. These attributes are iterative, meaning they will
     * have x number of values created from them based on the second
     * parameter that will always be an integer.
     * @var {Array}
     */
    protected $columnFields = array(
        'bSearchable',
        'bSortable',
        'mDataProp',
        'bRegex',
        'sSearch'
    );

    protected $sortingFields = array(
        'iSortCol',
        'sSortDir'
    );

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
     * Gather the global settings
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
     * @return void
     */
    protected function mapGlobals()
    {
        foreach ($this->globalFields as $value)
        {
            $this->applyValue('global', $value);
        }

        return $this;
    }

    /**
     * Map iterative fields to the attributes array
     * using the values assigned in the allowed fields.
     * This allows us to create the correct number of
     * fields based on number of columns / sortable fields
     * amongst other types that can be added if required.
     *
     * @param {Array}
     * @return void
     */
    protected function mapColumns()
    {
        foreach ($this->columnFields as $columnField)
        {
            for ($i = 0; $i  < $this->attributes['global']['iColumns']; $i++)
            {
                $this->applyValue($i, $columnField, $i);
            }
        }

        return $this;
    }

    protected function mapSorting()
    {
        for ($i = 0; $i  < $this->attributes['global']['iSortingCols']; $i++)
        {
            $ns = $this->request->get('iSortCol' . '_' . $i);
            $direction = $this->request->get('sSortDir' . '_' . $i);

            $this->attributes[$ns]['sort'] = true;
            $this->attributes[$ns]['sort_dir'] = $direction;
        }

        return $this;
    }

    /**
     * Apply a field to the attributes array
     * @param {String}
     * @return void
     */
    protected function applyValue($ns, $value, $mapping = null)
    {
        $mapping = is_null($mapping) ? $value : $value . '_' . $mapping;

        $fetched = $this->request->get($mapping);

        if (is_null($fetched))
            throw new InputMissingException(sprintf("%s was missing from the input", $value));

        if ( ! isset($this->attributes[$ns]))
            $this->attributes[$ns] = array();

        $this->attributes[$ns][$value] = ($value  === 'sEcho' || $value[0]  === 'i') ? intval($fetched, 10) : $fetched;
    }

    /**
     * Magic method to get a specific attribute
     */
    public function __get($name)
    {
        $self = $this->build();

        if (array_key_exists($name, $self->attributes['global']))
            return $self->attributes['global'][$name];

        return null;
    }

    /**
     * Magic method to set a specific attribute
     */
    public function __set($name, $value)
    {
        $self = $this->build();
        $self->attributes['global'][$name] = $value;
    }

}
