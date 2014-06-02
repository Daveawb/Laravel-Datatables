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
    protected $attributes = array();
    
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
     * @param {String} name
     * @param {Array} data from input
     */
    public function __construct($fields, array $settings)
    {
        $fields = is_array($fields) ? $fields : array($fields);
        
        // Get any interpretation logic
        if (is_array($fields[count($fields) - 1]))
        {
            $this->setInterpretationData(array_pop($fields));
        }
        elseif ($fields[count($fields) - 1] instanceof Closure)
        {
            $this->closure = array_pop($fields);
        }
        
        // Set fields and attributes
        $this->fields = $fields;
        $this->attributes = $settings;
    }
    
    /**
     * Set interpretation data on this column
     * @param {Array} array of interpretation data
     */
    protected function setInterpretationData($data)
    {
        array_walk($data, function(&$values, $func)
        {
            $values = explode(',', $values);
        });
        
        $this->interpret = array_merge($this->interpret, $data);
    }
    
    /**
     * Get the interpretation data
     * @return {Mixed} Closure or Array
     */
    protected function getInterpretationData()
    {
        if ($this->closure instanceof Closure)
            return $this->closure;
        
        return $this->interpret;
    }
    
    /**
     * Run data through this columns interpreters
     * and return the modified results
     * @param {Array} database data
     * @return {String}
     */ 
    public function interpret($field, &$data)
    {
        if (count($this->interpret) < 1)
            return $data->{$field};
            
        foreach($this->interpret as $class => $args)
		{
		    $class = ucfirst($class);
            
            if (class_exists('Daveawb\\Datatables\\Columns\\Expressions\\' . $class))
            {
                $reflector = new ReflectionClass('Daveawb\\Datatables\\Columns\\Expressions\\' . $class);
                $interpreter = $reflector->newInstanceArgs(array($this->fields, $data));
                $data->{$field} = $interpreter->evaluate($args);
            }
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