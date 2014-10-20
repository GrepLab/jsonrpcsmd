<?php
namespace Greplab\Jsonrpcsmd\Smd;

class Parameter
{
    protected $service;
    protected $method;
    protected $param;
    
    public function __construct(\ReflectionParameter $param, Method $method, Service $class)
    {
        $this->service = $class;
        $this->method = $method;
        $this->param = $param;
    }
    
    public function getName()
    {
        return $this->param->getName();
    }
    
    public function toArray()
    {
        return array(
            //'name' => $this->param->getName(),
            'optional' => $this->param->isOptional(),
            'default' => $this->param->isDefaultValueAvailable() ? $this->param->getDefaultValue() : null
        );
    }
    
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}