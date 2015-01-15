<?php namespace Greplab\Jsonrpcsmd\Envelope;

use Greplab\Jsonrpcsmd\Smd;

/**
 * Base of the envelope clases.
 *
 * @author Daniel Zegarra <dzegarra@greplab.com>
 * @package Greplab\Jsonrpcsmd\Envelope
 */
abstract class Base {

    protected $smd;

    /**
     * Constructor.
     * Just receive the Smd instance.
     * @param Smd $smd
     */
    public function __constructor(Smd $smd)
    {
        $this->smd = $smd;
    }

    /**
     * Return the map of services and methods as an array.
     * @param array $map The list of services
     * @return array
     */
    abstract public function build(array $map);

}