<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Legendable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around displaying a legend
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
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