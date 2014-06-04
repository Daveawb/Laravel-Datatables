<?php namespace Daveawb\Datatables\Columns\Expressions;

use Daveawb\Datatables\Columns\Expression;

class Combine implements Expression {
    
    public function __construct(array $fields, $dataModel)
    {
        $this->fields = $fields;
        $this->data = $dataModel;
    }
    
    public function evaluate($args = array())
    {
        $separator = array_pop($args);
        
        $output = '';
        
        $count = count($this->fields);
        
        foreach($args as $arg)
        {
            if ($this->inRange($arg, 0, $count))
            {
                $output .= $this->data[$this->fields[$arg]];
            }
			elseif(array_key_exists($arg, $this->data))
            {
                $output .= $this->data[$arg];
            }
			else
			{
				$output .= $arg;
			}
            
            $output .= $separator;
        }
        
        return rtrim($output, $separator);
    }
	
	public function plain($args = array())
	{
		return $this->evaluate($args);
	}
    
    private function inRange($predicate, $start = 0, $stop = 0)
    {
        if ( is_numeric($predicate) && is_numeric($start) && is_numeric($stop) )
        {
            return $predicate <= $stop && $predicate >= $start;
        }
        
        return false;
    }
}