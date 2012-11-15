<?php

namespace Altamira\JsWriter\Ability;

interface Datable
{
    /**
     * Determines if a chart axis should use a date format
     * @param unknown_type $axis
     */
    public function useDates( $axis = 'x' );
}