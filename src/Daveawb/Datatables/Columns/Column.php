<?php namespace Daveawb\Datatables\Columns;

class Column {
    
    public $sortable = false;
    
    public $sortDirection = "asc";
    
	protected $attributes = array();
	
    /**
     * Build the column with data passed in by
     * the factory
     * @param {String} name
     * @param {Array} data from input
     */
    public function __construct($name, array $settings)
    {
        $this->name = $name;
        
        foreach ($settings as $setting => $value)
        {
            $this->{$setting} = $value;
        }
    }
	
	/**
	 * Custom query for this column, this column should not have a spot on the
	 * base query if this method is invoked and will return a separate result
	 * set specific to this column alone.
	 */
	public function query()
	{
		
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
