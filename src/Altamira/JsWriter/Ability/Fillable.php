<?php

namespace Altamira\JsWriter\Ability;

interface Fillable
{
    /**
     * Used for filling series in charts
     * @param string|\Altamira\Chart $series
     * @param array $opts
     */
    public function setFill($series, $opts = array('use'    => true, 
                                                   'stroke' => false, 
                                                   'color'  => null, 
                                                   'alpha'  => null
                                                  )
                            );
}