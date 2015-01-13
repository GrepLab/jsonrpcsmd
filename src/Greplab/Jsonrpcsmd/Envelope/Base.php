<?php namespace Greplab\Jsonrpcsmd\Envelope;

use Greplab\Jsonrpcsmd\Smd;

/**
 * Base of the envelope clases.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Envelope
 */
interface Base {

    public function __constructor(Smd $smd);

    public function build(array $map);

}