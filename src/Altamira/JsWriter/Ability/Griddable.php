<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Griddable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around a background grid in a chart
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Griddable
{
    /**
     * Sets options relating to chart grid
     * @param array $opts these include 'on', 'backgroundColor', 'color', but vary between jsWriters
     */
    public function setGrid( array $opts );
}