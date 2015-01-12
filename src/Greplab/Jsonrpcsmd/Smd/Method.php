<?php
namespace Greplab\Jsonrpcsmd\Smd;
use Greplab\Jsonrpcsmd\Smd;

/**
 * Class to analyze the shape of a method of one service.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Smd
 */
class Method
{
    /**
     * @var Smd
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
     * @param Smd $smd
     * @param \Greplab\Jsonrpcsmd\Smd\Service $class
     * @param \ReflectionMethod $method
     */
    public function __construct(Smd $smd, Service $class, \ReflectionMethod $method)
    {
        $this->smd = $smd;
        $this->service = $class;
        $this->method = $method;
        
        $this->use_canonical = $smd->getUseCanonical();
        
        //Walking the parameters of the method
        foreach ($method->getParameters() as $param) {
            $this->params[] = new Parameter($param, $this, $class);
        }
    }
    
    /**
     * Return the name of the method.
     * @return string
     */
    public function getName()
    {
        return $this->method->getName();
    }
    
    /**
     * Return the params of the method.
     * @return Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return un array description of the method.
     * @return array
     */
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

    /**
     * Return the method description as an json string.
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return the method description as an json string.
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
    
}