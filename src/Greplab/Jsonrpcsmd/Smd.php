<?php namespace Greplab\Jsonrpcsmd;

/**
 * Esta clase es la encargada de inventariar y crear el mapa de servicios y métodos.
 * 
 * @author Daniel Zegarra <dzegarra@greplab.com>
 */
class Smd
{
    const ENV_JSONRPC_2 = 'JSON-RPC-2.0';
    const ENV_JSONRPC_1 = 'JSON-RPC-1.0';
    
    /**
     * Lista de servicios a mapear
     */
    protected $services = array();
    
    /**
     * Medio de transporte por defecto
     */
    protected $transport = 'POST';
    
    /**
     * Tipo de contenido que debe especificarse en la cabecera de las llamadas
     */
    protected $contentType = 'application/json';
    
    /**
     * Versión del estandar JSON-RPC que debe usarse en las llamadas
     */
    protected $envelope = self::ENV_JSONRPC_2;
    
    protected $target;
    
    protected $useCanonical = false;
    
    public function getTransport()
    {
        return $this->transport;
    }
    public function setTransport($type)
    {
        $this->transport = $type;
    }
    
    public function getContentType()
    {
        return $this->contentType;
    }
    public function setContentType($type)
    {
        $this->contentType = $type;
    }
    
    public function getEnvelope()
    {
        return $this->envelope;
    }
    public function setEnvelope($type)
    {
        $this->envelope = $type;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    public function setTarget($url)
    {
        $this->target = $url;
    }
    
    public function getUseCanonical()
    {
        return $this->useCanonical;
    }
    public function setUseCanonical($value)
    {
        $this->useCanonical = $value;
    }
    
    /**
     * Constructor
     *
     * Setup server description
     * @param string $target
     * @param boolean $useCanonical
     */
    public function __construct($target=null)
    {
        if (!is_null($target)) {
            $this->setTarget($target);
        }
    }
    
    /**
     * Agrega una clase a la lista de clases accesibles externamente.
     * @param string $class
     * @return \Greplab\Jsonrpcsmd\Smd
     */
    public function addClass($class)
    {
        $this->services[] = new Smd\Service($this, $class);
        return $this;
    }
    
    /**
     * Devuelve el mapa de servicios.
     * @return array
     */
    public function toArray()
    {
        $target = $this->getTarget();
        if (empty($target)) {
            throw new \Exception('The target is not defined');
        }
        $services = [];
        foreach ($this->services as $service) {
            $services = array_merge($services, $service->toArray());
        }
        return array(
            'transport' => $this->getTransport(),
            'envelope' => $this->getEnvelope(),
            'contentType' => $this->getContentType(),
            'serviceUrl' => $target,
            'methods' => $services
        );
    }

    /**
     * Devuelve el mapa de servicios como una cadena JSON.
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
    
    public function __toString()
    {
        return $this->toJson();
    }
    
}