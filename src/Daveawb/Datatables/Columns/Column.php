<?php namespace Daveawb\Datatables\Columns;

class Column {
    
	/**
	 * Array of fields this columns maps to, the
	 * first indexed field in always the primary
	 * @var {Array}
	 */
	public $fields = array();
	
	/**
	 * The columns attributes
	 * @var {Array}
	 */
	protected $attributes = array();
	
    /**
     * Build the column with data passed in by
     * the factory
     * @param {String} name
     * @param {Array} data from input
     */
    public function __construct($fields, array $settings)
    {
    	$fields = is_array($fields) ? $fields : array($fields);
		
        $this->fields = $fields;
        
        $this->attributes = $settings;
    }
		
	/**
	 * magic getter
	 */
	public function __get($key)
	{
		return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
	}
	
	/**
	 * magic setter
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}
}
