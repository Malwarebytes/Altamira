<?php 

namespace Altamira\JsWriter\Ability;

interface Cursorable
{
    /**
     * Determines whether we should use cursor actions on a chart
     */
    public function useCursor();
}