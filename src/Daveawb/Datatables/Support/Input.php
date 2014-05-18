<?php namespace Daveawb\Datatables\Support;

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
	 * We need to dependency inject the request object.
	 * @param {Object} Illuminate\Http\Request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
     * Array of allowed attributes. These are datatables internal
	 * attributes. These attributes are mapped one:one to their 
	 * respective input attribute.
     * @var {Array}
     */
    protected $allowed = array (
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
    protected $iterative =  array (
        'bSearchable'   => 'iColumns',
        'bSortable'     => 'iColumns',
        'mDataProp'     => 'iColumns',
        'bRegex'        => 'iColumns',
        'sSearch'       => 'iColumns',
        'iSortCol'      => 'iSortingCols',
        'sSortDir'      => 'iSortingCols'
    );
	
	/**
	 * Array of mapped attributes
	 * @var {Array}
	 */
	protected $attributes;
	
	/**
	 * Get the mapped attributes
	 * @return {Array}
	 */
	public function get()
	{
		return $this->attributes;
	}
	
	public function build()
	{
		$this->mapAllowed();
		$this->mapIterative();
		
		return $this;
	}

	/**
	 * Map allowed fields to the attributes array
	 * 
	 * @param {Array}
	 * @return void
	 */
	protected function mapAllowed()
	{
		foreach ($this->allowed as $value)
		{
			$this->applyValue($value);
		}
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
	protected function mapIterative()
	{
        foreach ($this->iterative as $key => $value)
        {
            for ($i = 0; $i < $this->attributes[$value]; $i++)
            {
				$this->applyValue($key . '_' . $i);
            }
        }
	}
	
	/**
	 * Apply a field to the attributes array
	 * @param {String}
	 * @return void
	 */
	protected function applyValue($value)
	{
		$fetched = $this->request->get($value, null);
		
		if ( is_null($fetched) )
			throw new InputMissingException(sprintf("%s was missing from the input", $value));
		
		$this->attributes[$value] = ($value === 'sEcho' || $value[0] === 'i') ? intval($fetched, 10) : $fetched; 
	}
}
