<?php 

namespace Altamira\JsWriter\Ability;

interface Shadowable
{
    public function setShadow($series, $opts = array('use'=>true, 
                                                     'angle'=>45, 
                                                     'offset'=>1.25, 
                                                     'depth'=>3, 
                                                     'alpha'=>0.1)
                              );
}