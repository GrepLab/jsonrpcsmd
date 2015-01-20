<?php namespace Greplab\Jsonrpcsmd\Smd;

use Greplab\Jsonrpcsmd\Smd;

/**
 * Class in charge of reflect and build a map of one class.
 * Each instance of this class represent a unique class reflected.
 * 
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Smd
 */
class Service  
{
    const PRESENTATION_PLAIN = 'plain';
    
    const PRESENTATION_TREE = 'tree';
    
    /**
     * The map can be presented of two modes:
     *  - Showing the service name in each method
     *  - Nesting the methods names inside a service parent.
     * @var string
     */
    protected $presentation = self::PRESENTATION_PLAIN;
    
    /**
     * @var Smd
     */
    protected $smd;
    
    /**
     * @var \ReflectionClass
     */
    protected $reflectedclass;
    
    /**
     * List of methods of the service.
     * @var Method[]
     */
    protected $methods = array();

    /**
     * Read the content of one class and return the result of the analysis.
     * Return FALSE if the cass found is not a valid service.
     *
     * @param Smd $smd
     * @param string $classname
     * @return Service
     */
    static public function read(Smd $smd, $classname)
    {
        $reflectedclass = new \ReflectionClass($classname);
        return self::isValid($smd, $reflectedclass) ? new Service($smd, $reflectedclass) : false;
    }

    /**
     * Validate a class using the custom validation closure.
     *
     * @param Smd $smd
     * @param \ReflectionClass $reflectedclass
     * @return bool
     */
    static protected function isValid(Smd $smd, \ReflectionClass $reflectedclass)
    {
        $validator = $smd->getServiceValidator();
        if ($validator && is_callable($validator)) {
            if ( call_user_func_array($validator, [$reflectedclass]) === false ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Constructor.
     *
     * @param Smd $smd
     * @param \ReflectionClass $reflectedclass
     */
    public function __construct(Smd $smd, \ReflectionClass $reflectedclass)
    {
        $this->smd = $smd;
        $this->reflectedclass = $reflectedclass;
        
        // Walwing the public methods of this class
        foreach ($reflectedclass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            
            $name = $method->getName();
            // Ignore magic methods
            if ('__' == substr($name, 0, 2)) continue;
        
            $this->methods[] = new Method($smd, $this, $method);
        }
    }
    
    /**
     * Return the method of the service.
     *
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }
    
    /**
     * Return the name of the reflected class.
     *
     * @return string
     */
    public function getClassname()
    {
        return $this->reflectedclass->getName();
    }
    
    /**
     * Return the name of the class replacing the package separators by dots.
     *
     * @return string
     */
    public function getDottedClassname()
    {
        return str_replace(['\\','_'], '.', $this->getClassname());
    }

    /**
     * Try to resolve the name of the service using the custom resolve closure. If a resolve function is not defined
     * then the {@link self::getDottedClassname()} method is used.
     *
     * @return string
     */
    public function resolveClassname()
    {
        $resolver = $this->smd->getNameResolver();
        if ( $resolver && is_callable($resolver) ) {
            return call_user_func_array($resolver, [$this]);
        }
        return $this->getDottedClassname();
    }
    
    /**
     * Return a plain representation of the class methods.
     *
     * @return array
     */
    protected function toArrayPlain()
    {
        $classname = $this->resolveClassname();
        $methods = array();
        foreach ($this->getMethods() as $method) {
            $method_fullname = $classname . '.' . $method->getName();
            $methods[$method_fullname] = $method->toArray();
        }
        return $methods;
    }
    
    /**
     * This method is still in development.
     *
     * @todo implement the method toArrayTree()
     * @param string $classname
     * @return string
     */
    protected function buildLevel($classname)
    {
        $parts = explode('.', $classname);
        
        $lastLevel = &$this->methods;
        foreach ($parts as $part) {
            $lastLevel[$part] = array();
            $lastLevel = &$lastLevel[$part];
        }
        
        return $lastLevel;
    }
    
    /**
     * Return a tree representation of the methods of the reflected class.
     *
     * @todo This method is still in development.
     * @return array
     */
    protected function toArrayTree()
    {
        $classname = $this->getDottedClassname();
        $lastLevel = $this->buildLevel($classname);
        
        foreach ($this->getMethods() as $method) {
            $lastLevel[$method->getName()] = $method->toArray();
        }
        
        return $this->methods;
    }

    /**
     * Build and return the class representation as an array.
     *
     * @return array
     * @throws \Exception If the current representation mode cannot be used
     */
    public function toArray()
    {
        $fn = 'toArray' . ucfirst($this->presentation);
        if (!method_exists($this, $fn)) {
            throw new \Exception('There is no method ' . $fn . '.');
        }
        return $this->$fn();
    }

    /**
     * Return this class representation as a json string.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return this class representation as a json string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
    
}