<?php namespace Greplab\Jsonrpcsmd\Envelope;

use Greplab\Jsonrpcsmd\Smd;

/**
 * Format the result using the version JSON-RPC 2.0.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Envelope
 */
class V2 extends Base {

    public function build(array $map)
    {
        return array(
            'envelope' => 'JSON-RPC-2.0',
            'transport' => $this->smd->getTransport(),
            'contentType' => $this->smd->getContentType(),
            'serviceUrl' => $this->smd->getTarget(),
            'methods' => $map
        );
    }

}