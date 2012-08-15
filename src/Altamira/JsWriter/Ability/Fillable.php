<?php

namespace Altamira\JsWriter\Ability;

interface Fillable
{
    public function setFill($series, $opts = array('use' => true, 
                                                   'stroke' => false, 
                                                   'color' => null, 
                                                   'alpha' => null
                                                  )
                            );
}