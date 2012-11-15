<?php

namespace Altamira\JsWriter\Ability;

interface Legendable
{
    /**
     * Configures the legend component of a chart
     * @param array $opts
     */
    public function setLegend( array $opts = array('on'       => 'true', 
                                                   'location' => 'ne', 
                                                   'x'        => 0, 
                                                   'y'        => 0)
                             );
}