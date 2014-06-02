<?php namespace Daveawb\Datatables\Columns\Expressions;

use Daveawb\Datatables\Columns\Expression;

class Append implements Expression {
    
    public function __construct(array $fields, $dataModel)
    {
        $this->fields = $fields;
        $this->data = $dataModel;
    }
    
    public function evaluate($args = array())
    {
        $separator = count($args) > 1 ? $args[1] : '';
        
        return $this->data->{$this->fields[0]} . $separator . $args[0];
    }
}