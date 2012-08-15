<?php

namespace Altamira\JsWriter\Ability;

interface Legendable
{
    public function setLegend(array $opts = array('on' => 'true', 
                                                  'location' => 'ne', 
                                                  'x' => 0, 
                                                  'y' => 0)
                             );
}