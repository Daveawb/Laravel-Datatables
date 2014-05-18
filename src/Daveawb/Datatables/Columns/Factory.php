<?php
namespace Daveawb\Datatables\Columns;

use Daveawb\Datatables\Input;

/**
 * Column factory class
 */
class Factory {
    
    /**
     * An array of global input variables
     * @param {Array}
     */
    protected $globals = array();
    
    /**
     * Create a new column with developer settings and passed
     * datatables input
     * @param {String} field to map to
     * @param {Mixed} the mDataProp key for the column
     */
    public function create($field, $key)
    {
        if ( ! $this->input )
            return false;
        
        $data = $this->input->gather($key);
        
        foreach($this->sorting as $sort)
        {
            if ($sort[0] === $key)
            {
                $data['sortable'] = true;
                $data['sortDirection'] = $sort[1];
            }
        }
        
        if ( ! empty ($this->globals['sSearch']) )
            $data['sSearch'] = $this->globals['sSearch'];
        
        return new Column($field, $data);
    }

    /**
     * Set the input class on the factory
     * @param {String} Daveawb\Datatables\Input
     */
    public function input(Input $input)
    {
        $this->input = $input;
        
        $this->globals = $this->input->gatherGlobals();
        
        $this->sorting = $this->input->gatherSortables();
    }

}
