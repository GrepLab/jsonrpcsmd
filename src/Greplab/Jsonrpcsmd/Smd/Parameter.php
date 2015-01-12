<?php
namespace Greplab\Jsonrpcsmd\Smd;

/**
 * Class to analyze the param of one method.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Smd
 */
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

    /**
     * Return the param's name.
     * @return string
     */
    public function getName()
    {
        return $this->param->getName();
    }

    /**
     * Return a representation of the current param.
     * @return array
     */
    public function toArray()
    {
        return array(
            //'name' => $this->param->getName(),
            'optional' => $this->param->isOptional(),
            'default' => $this->param->isDefaultValueAvailable() ? $this->param->getDefaultValue() : null
        );
    }

    /**
     * Return a representation of the current param as a json string.
     * @return array
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return a representation of the current param as a json string.
     * @return array
     */
    public function __toString()
    {
        return $this->toJson();
    }
}