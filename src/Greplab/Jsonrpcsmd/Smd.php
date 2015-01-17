<?php namespace Greplab\Jsonrpcsmd;

/**
 * Class in charge of build the map of services and methods.
 *
 * To use this create a instance of {@link Smd}, add class names with {@link addClass} an finally use the methods {@link toArray} or {@link toJson} for get the map.
 *
 * I based in the specs publish in:
 *  - http://www.simple-is-better.org/json-rpc/jsonrpc20-smd.html
 *  - http://dojotoolkit.org/reference-guide/1.10/dojox/rpc/smd.html
 *  - And the source code of http://framework.zend.com/manual/1.12/en/zend.json.server.html#zend.json.server.details.zendjsonserversmd
 *
 * Notice: This library just create and return the map of service. To respond the calls of remote methods you have to use other library or create the routes yourself.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd
 */
class Smd
{
    
    /**
     * List of services to map.
     */
    protected $services = array();
    
    /**
     * Transport method by default
     */
    protected $transport = 'POST';
    
    /**
     * Type of content who has to be specified in the header.
     */
    protected $contentType = 'application/json';
    
    /**
     * Standard version of JSON-RPC used in the calls.
     * Use the name of the class used for construct the respond. The classes availables reside in the Envelope directory.
     * You can create a new format and put in the Envelope directory. Don't forget of define the correct namespace.
     */
    protected $envelope = 'V2';

    /**
     * The URL for the remote calls.
     * @var string
     */
    protected $target;

    /**
     * Use a different url for each method using the service and method names.
     * @var bool
     */
    protected $useCanonical = false;

    /**
     * Closure used as service validator.
     * This closure will be executed for each attempt to reflect a class. Is the function return FALSE the class will not be considered.
     * You can use this to implement a customized validator of services.
     */
    public $service_validator;
    
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
     * Constructor.
     * @param string $target The URL target of the remote calls
     * @param string $envelope
     */
    public function __construct($target=null, $envelope=null)
    {
        if (!is_null($target)) {
            $this->setTarget($target);
        }
        if (!is_null($envelope)) {
            $this->setEnvelope($envelope);
        }
    }
    
    /**
     * Add a class to the list of accessible classes externally.
     * @param string $class
     * @return \Greplab\Jsonrpcsmd\Smd
     */
    public function addClass($class)
    {
        $reflectedclass = Smd\Service::read($this, $class);
        if ( $reflectedclass !== false ) {
            $this->services[] = Smd\Service::read($this, $class);
        }
        return $this;
    }

    /**
     * Return the service map as an associative array.
     * @throws \Exception Is the target is not defined yet
     * @return array
     */
    public function toArray()
    {
        $target = $this->getTarget();
        if (empty($target)) throw new \Exception('The target is not defined');

        $map = [];
        foreach ($this->services as $service) {
            $map = array_merge($map, $service->toArray());
        }
        return $this->formatRespond($map);
    }

    /**
     * Format the response including the map.
     * @param array $map
     * @return array
     */
    protected function formatRespond($map) {
        $envelope = \App::make('Greplab\Jsonrpcsmd\Envelope\\' . $this->envelope, array($this));
        return $envelope->build($map);
    }

    /**
     * Return the map of services as a json string.
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return the map of services as a json string.
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
    
}