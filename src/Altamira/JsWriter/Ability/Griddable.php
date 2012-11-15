<?php 

namespace Altamira\JsWriter\Ability;

interface Griddable
{
    /**
     * Sets options relating to chart grid
     * @param array $opts
     */
    public function setGrid( array $opts );
}