<?php namespace Daveawb\Datatables\Columns;

use Closure;
use ReflectionClass;

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
    protected $attributes = array(
        "sort" => false
    );
    
    /**
     * Interpretation data
     * @var {Array}
     */
    protected $interpret = array();
    
    /**
     * Closure, this property is set when a closure is
     * passed as the final argument instead of an interpreter
     * @var {Object} Closure
     */
    protected $closure;

    /**
     * Build the column with data passed in by
     * the factory
     * @param string
     * @param array $settings
     */
    public function __construct($fields, array $settings)
    {
        $fields = is_array($fields) ? $fields : array($fields);
        
        // Get any interpretation logic
        if (is_array($fields[count($fields) - 1]))
        {
            $this->setInterpretationData(array_pop($fields));
        }
        
        if ($fields[count($fields) - 1] instanceof Closure)
        {
            $this->closure = array_pop($fields);
        }
        
        // Set fields and attributes
        $this->fields = $fields;
        $this->attributes = $settings;

        $this->checkColumn($fields);
    }
    
    /**
     * Set interpretation data on this column
     * @param array
     */
    protected function setInterpretationData($data)
    {
        array_walk($data, function (&$values)
        {
            $values = explode(',', $values);
        });
        
        $this->interpret = array_merge($this->interpret, $data);
    }

    protected function checkColumn($columns)
    {
        foreach ($columns as $column)
        {
            if (strstr($column, '.'))
            {
                $this->sort = false;
            }
        }
    }

    /**
     * Run data through this columns interpreters
     * and return the modified results
     * @param $field
     * @param $data
     */
    public function interpret($field, &$data)
    {		
        if (count($this->interpret) < 1 && is_null($this->closure))
            return array_get($data, $field);
            
        foreach($this->interpret as $class => $args)
		{
		    $class = ucfirst($class);
            
            if (class_exists('Daveawb\\Datatables\\Columns\\Expressions\\' . $class))
            {
                $reflector = new ReflectionClass('Daveawb\\Datatables\\Columns\\Expressions\\' . $class);
                $interpreter = $reflector->newInstanceArgs(array($this->fields, $data));
                $data[$field] = $interpreter->evaluate($args);
            }
        }
        
        if ($this->closure instanceof Closure)
        {
            $data[$field] = call_user_func_array($this->closure, array($field, $data));
        }
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