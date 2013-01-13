<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Zoomable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around allowing zooming around the chart
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Zoomable
{
    /**
     * Allows the user to zoom based on the 'mode' key value in the array argument passed
     * Modes include x, y, and xy
     * @param array $options
     */
    public function useZooming(array $options = array('mode'=>'xy'));
}