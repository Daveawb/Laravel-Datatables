<?php
namespace Daveawb\Datatables\Columns\Input;

use Daveawb\Datatables\InputMissingException;

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
class OneNineInput extends BaseInput {

    /**
     * Array of fields that have only one value from the input
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
     * Array of fields that use iColumns as their iterator
     * @var {Array}
     */
    protected $columnFields = array(
        'bSearchable',
        'bSortable',
        'mDataProp',
        'bRegex',
        'sSearch'
    );
	
	/**
	 * Array of fields that use iSortingCols as their iterator
	 * @var {Array}
	 */
    protected $sortingFields = array(
        'iSortCol',
        'sSortDir'
    );

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
     * @return {Object} Self
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

    /**
     * Get sorting data and apply it to the correct column
     * @return {Object} Self
     */
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
}
