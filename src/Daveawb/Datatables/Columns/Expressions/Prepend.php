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
        return $this->validate($args) . $this->data[$this->fields[0]];
    }
	
	public function plain($args = array())
	{
		return $this->validate($args);
	}
	
	protected function validate($args)
	{
		$separator = count($args) > 1 ? $args[1] : '';
		
    	if( is_array($args[0]) )
		{
			$combine = $this->getCombine();
			
			array_push($args[0], $separator);
			
			$args[0] = $combine->plain($args[0]);
		}
		elseif ( array_key_exists($args[0], $this->data) )
		{
			$args[0] = $this->data[$args[0]];
		}
		
		return $args[0] . $separator;
	}
	
	protected function getCombine()
	{
		return new Combine($this->fields, $this->data);
	}
}