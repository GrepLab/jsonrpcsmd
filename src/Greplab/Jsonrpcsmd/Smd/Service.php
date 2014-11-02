<?php namespace Greplab\Jsonrpcsmd\Smd;

/**
 * Representa a un servicio remoto.
 * 
 * @author Daniel Zegarra <dzegarra@greplab.com>
 */
class Service  
{
    const PRESENTATION_PLAIN = 'plain';
    
    const PRESENTATION_TREE = 'tree';
    
    /**
     * El json de servicios puede armarse de dos formas. Mostrando repetidamente el 
     * nombre del servicio y método o agrupando los métodos.
     * @var string
     */
    protected $presentation = self::PRESENTATION_PLAIN;
    
    /**
     * @var \Greplab\Jsonrpcsmd\Smd
     */
    protected $smd;
    
    /**
     * @var \ReflectionClass
     */
    protected $reflectedclass;
    
    /**
     * Lista de métodos del servicio
     * @var Method[]
     */
    protected $methods = array();
    
    /**
     * Lee el contenido de la clase y entrega el resultado del análisis.
     * Devuelve FALSE si la clase entregada no es un servicio válido.
     * @return Service
     */
    static public function read(\Greplab\Jsonrpcsmd\Smd $smd, $classname) 
    {
        $reflectedclass = new \ReflectionClass($classname);

        if ($smd->service_validator && is_callable($smd->service_validator)) {
            if ( call_user_func_array($smd->service_validator, array($reflectedclass)) === false ) {
                return false;
            }
        }

        return new Service($smd, $reflectedclass);
    }

    /**
     * Constructor.
     * 
     * @param \Greplab\Jsonrpcsmd\Smd $smd
     * @param \ReflectionClass $reflectedclass
     */
    public function __construct(\Greplab\Jsonrpcsmd\Smd $smd, \ReflectionClass $reflectedclass)
    {
        $this->smd = $smd;
        $this->reflectedclass = $reflectedclass;
        
        //Recorriendo los métodos públicos de la clase
        foreach ($reflectedclass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            
            $name = $method->getName();
            //Ignorar los métodos mágicos
            if ('__' == substr($name, 0, 2)) continue;
        
            $this->methods[] = new Method($smd, $this, $method);
        }
    }
    
    /**
     * Devuelve los métodos del servicio.
     * 
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }
    
    /**
     * Entrega el nombre de la clase.
     * @return string
     */
    public function getClassname()
    {
        return $this->reflectedclass->getName();
    }
    
    /**
     * Entrega el nombre de la clase reemplazando los separadores de paquetes por puntos.
     * @return string
     */
    public function getDottedClassname()
    {
        return str_replace(['\\','_'], '.', $this->getClassname());
    }
    
    /**
     * Entrega una representación plana de los m�todos de la clase.
     * 
     * @return array
     */
    protected function toArrayPlain()
    {
        $classname = $this->getDottedClassname();
        $methods = array();
        foreach ($this->getMethods() as $method) {
            $methodfullname = $classname . '.' . $method->getName();
            $methods[$methodfullname] = $method->toArray();
        }
        return $methods;
    }
    
    /**
     * 
     * @param unknown $classname
     * @return multitype:
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
     * Entrega una representación de arbol de los métodos de la clase.
     *
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
    
    public function toArray()
    {
        $fn = 'toArray' . ucfirst($this->presentation);
        if (!method_exists($this, $fn)) {
            throw new \Exception('No existe el método ' . $fn);
        }
        return $this->$fn();
    }
    
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    public function __toString()
    {
        return $this->toJson();
    }
    
}