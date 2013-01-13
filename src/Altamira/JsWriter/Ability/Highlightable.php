<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Highlightable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around point highlighting
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Highlightable
{
    /**
     * Determines whether to allow highlighting of points, and the highlight size in its options
     * @param array $opts
     */
    public function useHighlighting(array $opts = array('size'=>7.5) );
}