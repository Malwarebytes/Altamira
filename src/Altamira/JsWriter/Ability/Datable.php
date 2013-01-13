<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Datable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around handling dates within charts
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Datable
{
    /**
     * Determines if a chart axis should use a date format
     * @param string $axis
     */
    public function useDates( $axis = 'x' );
}