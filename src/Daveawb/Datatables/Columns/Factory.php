<?php
namespace Daveawb\Datatables\Columns;

use Daveawb\Datatables\ValidationException;

use Illuminate\Validation\Factory as ValidatorFactory;

/**
 * Column factory class
 */
class Factory {
		
	/**
	 * Input object for use by the factory, this factory
	 * also provides a minimal facade for access to input
	 * data held by this class.
	 * @var Daveawb\Datatables\Columns\Input
	 */
	public $input;
	
	/**
	 * The column array that holds the manufactured
	 * column objects indexed by mDataProp
	 */
	protected $columns = array();
	
	/**
	 * Constructor dependency injecting the input class
	 * that manages the retrieval of data from the request
	 * @param {Object} Daveawb\Datatables\Input
	 */
	public function __construct(Input $input, ValidatorFactory $validator)
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
    public function create($field, $key)
    {        
        $data = $this->input->getColumn($key);
        
		if ( ! empty ($this->input->sSearch) && empty ($data['sSearch']) )
            $data['sSearch'] = $this->input->sSearch;
		
        $this->columns[$data['mDataProp']] = new Column($field, $data);
    }
	
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
	
	public function getColumn($index)
	{
		if (array_key_exists($index, $this->columns))
			return $this->columns[$index];
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
}
