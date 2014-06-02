<?php
namespace Daveawb\Datatables\Columns;

use Daveawb\Datatables\ValidationException;
use Daveawb\Datatables\Columns\Input\BaseInput;

use Illuminate\Validation\Factory as ValidatorFactory;

/**
 * Column factory class, creates columns and stores them
 * @author David Barker
 * @package daveawb\datatables
 */
class Factory {
		
	/**
	 * Input object for use by the factory, this factory
	 * also provides a minimal facade for access to input
	 * data held by this class.
	 * @var Daveawb\Datatables\Columns\Input\Input
	 */
	public $input;
	
	/**
	 * The column array that holds the manufactured
	 * column objects indexed by mDataProp
	 * @var {Array} containing instances of Daveawb\Datatables\Columns\Column
	 */
	protected $columns = array();
	
	/**
	 * Constructor dependency injecting the input class
	 * that manages the retrieval of data from the request and
	 * Laravels Validator factory.
	 * @param {Object} Daveawb\Datatables\Input
	 * @param {Object} Illuminate\Validation\Factory
	 */
	public function __construct(BaseInput $input, ValidatorFactory $validator)
	{
		$this->validator = $validator;
		$this->input = $input;
	}
    
    /**
     * Create a new column with developer settings and passed
     * datatables input
     * @param {String} field to map to
     * @param {Mixed} the mDataProp key for the column
     */
    public function create($fields, $key)
    {        
        $data = $this->input->getColumn($key);
        
		if ( (strlen($this->input->sSearch) > 0) && ( strlen($data['sSearch']) < 1 ) )
            $data['sSearch'] = $this->input->sSearch;
		
        $this->columns[$data['mDataProp']] = new Column($fields, $data);
    }
	
	/**
	 * Validate raw column data
	 * @param {Array} of raw column data
	 */
	public function validate(array $columns)
	{
		$data = array(
			"col_count" => count($columns),
			"col_expected" => $this->input->iColumns
		);
		
		$rules = array(
			"col_count" => "required|integer|same:col_expected",
			"col_expected" => "required|integer"
		);
		
		$validator = $this->validator->make($data, $rules);
		
		if ($validator->fails())
			throw new ValidationException($validator);
	}
	
	/**
	 * Get a specific column by its index
	 * @param {Integer} Column index
	 * @return {Object} Daveawb\Datatables\Columns\Column
	 */
	public function getColumn($index, $data = null)
	{        
		if (array_key_exists($index, $this->columns))
			return $this->columns[$index];
	}
	
	/**
	 * Get the full list of columns
	 * @param {Array} containing instances of Daveawb\Datatables\Columns\Column
	 */
	public function getColumns()
	{
		return $this->columns;
	}
}
