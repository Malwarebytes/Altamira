<?php 

namespace Altamira\JsWriter\Ability;

interface Highlightable
{
    public function useHighlighting(array $opts = array('size'=>7.5) );
}