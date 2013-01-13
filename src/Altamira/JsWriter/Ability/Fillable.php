<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Fillable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around series fill (e.g. calculus-type stuff, filling under a curve, etc)
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Fillable
{
    /**
     * Used for filling series in charts
     * @param string|\Altamira\Chart $series
     * @param array $opts
     */
    public function setFill($series, $opts = array('use'    => true, 
                                                   'stroke' => false, 
                                                   'color'  => null, 
                                                   'alpha'  => null
                                                  )
                            );
}