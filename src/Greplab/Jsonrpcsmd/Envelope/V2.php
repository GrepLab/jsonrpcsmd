<?php namespace Greplab\Jsonrpcsmd\Envelope;

use Greplab\Jsonrpcsmd\Smd;

/**
 * Format the result using the version JSON-RPC 2.0.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Envelope
 */
class V2 implements Base {

    protected $smd;

    public function __constructor(Smd $smd)
    {
        $this->smd = $smd;
    }

    public function build(array $map)
    {
        return array(
            'transport' => $this->smd->getTransport(),
            'envelope' => $this->smd->getEnvelope(),
            'contentType' => $this->smd->getContentType(),
            'serviceUrl' => $this->smd->getTarget(),
            'methods' => $map
        );
    }

}