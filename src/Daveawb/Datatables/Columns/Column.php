<?php namespace Daveawb\Datatables\Columns;

class Column {
    
    public $sortable = false;
    
    public $sortDirection = "asc";
    
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
}
