<?php namespace Daveawb\Datatables\Columns\Expressions;

use Daveawb\Datatables\Columns\Expression;

class Prepend implements Expression {
    
    public function __construct(array $fields, $dataModel)
    {
        $this->fields = $fields;
        $this->data = $dataModel;
    }
    
    public function evaluate($args = array())
    {
        $separator = count($args) > 1 ? $args[1] : '';
        
        return $args[0] . $separator . $this->data->{$this->fields[0]};
    }
}