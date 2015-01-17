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
        /*function setValue(&$level, $name) {
            $level[$name] = array();
            return $level[$name];
        }

        $mapa = array();
        $last_level = &$mapa;
        foreach ($map as $svc_name=>$svc_map) {
            $svc_name_parts = explode('.', $svc_name);
            foreach ($svc_name_parts as $part) {
                $last_level = setValue($last_level, $part);
            }
        }*/

        return array(
            'envelope' => 'JSON-RPC-2.0',
            'transport' => $this->smd->getTransport(),
            'contentType' => $this->smd->getContentType(),
            'serviceUrl' => $this->smd->getTarget(),
            'methods' => $map
        );
    }

}