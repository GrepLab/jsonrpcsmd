<?php
namespace Greplab\Jsonrpcsmd\Smd;

class Method
{
    /**
     * @var \Greplab\Jsonrpcsmd\Smd
     */
    protected $smd;
    
    /**
     * @var \Greplab\Jsonrpcsmd\Smd\Service
     */
    protected $service;
    
    /**
     * @var \ReflectionMethod
     */
    protected $method;
    
    /**
     * @var array
     */
    protected $params = array();
    
    /**
     * @var boolean
     */
    protected $use_canonical;
    
    /**
     * Constructor.
     * @param \Greplab\Jsonrpcsmd\Smd $smd
     * @param \Greplab\Jsonrpcsmd\Smd\Service $class
     * @param \ReflectionMethod $method
     */
    public function __construct(\Greplab\Jsonrpcsmd\Smd $smd, Service $class, \ReflectionMethod $method)
    {
        $this->smd = $smd;
        $this->service = $class;
        $this->method = $method;
        
        $this->use_canonical = $smd->getUseCanonical();
        
        //Recorriendo los par�metros
        foreach ($method->getParameters() as $param) {
            $this->params[] = new Parameter($param, $this, $class);
        }
    }
    
    /**
     * Entrega el nombre del m�todo.
     * @return string
     */
    public function getName()
    {
        return $this->method->getName();
    }
    
    /**
     * Devuelve los par�metros del m�todo.
     * @return Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }
    
    public function toArray() {
        $params = array();
        foreach ($this->getParams() as $param) {
            $params[$param->getName()] = $param->toArray();
        }
        $m = array(
            //'description',
            //'transport' => null,
            'parameters' => $params,
            //'returns' => null
        );
        if ($this->use_canonical) {
            $m['target'] = $this->smd->getTarget(). '/' . $this->service->getDottedClassname() . '.' . $this->getName();
        }
        return $m;
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