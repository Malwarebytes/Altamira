<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Cursorable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around showing cursor actions on a chart
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Cursorable
{
    /**
     * Determines whether we should use cursor actions on a chart
     */
    public function useCursor();
}