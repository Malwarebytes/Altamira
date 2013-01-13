<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Shadowable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around applying a shadow to content rendered on a chart
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Shadowable
{
    /**
     * Sets the shadowing options on how a specific series is rendered in a chart
     * @param string|\Altamira\Series $series
     * @param array $opts
     */
    public function setShadow($series, $opts = array('use'=>true, 
                                                     'angle'=>45, 
                                                     'offset'=>1.25, 
                                                     'depth'=>3, 
                                                     'alpha'=>0.1)
                              );
}