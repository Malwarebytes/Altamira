<?php 

namespace Altamira\JsWriter\Ability;

interface Zoomable
{
    public function useZooming(array $options = array('mode'=>'xy'));
}