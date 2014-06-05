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
        $args = $this->validate($args);
        
        return $this->data[$this->fields[0]] . $this->separator . $args[0];
    }
	
	public function plain($args = array())
	{
		$args = $this->validate($args);
		
		return $this->separator . $args[0];
	}
	
	protected function validate($args)
	{
		$this->separator = count($args) > 1 ? $args[1] : '';
		
    	if( is_array($args[0]) )
		{
			$combine = $this->getCombine();
			
			array_push($args[0], $this->separator);
			
			$args[0] = $combine->plain($args[0]);
		}
		elseif ( array_key_exists($args[0], $this->data) )
		{
			$args[0] = $this->data[$args[0]];
		}
		
		return $args;
	}
	
	protected function getCombine()
	{
		return new Combine($this->fields, $this->data);
	}
}