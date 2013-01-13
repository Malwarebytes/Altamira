<?php
/**
 * Class definition for \Altamira\JsWriter\Ability\Labelable
 * @author relwell
 */
namespace Altamira\JsWriter\Ability;
/**
 * This interface dictates that the implementing class can control
 * and transform options around labeling series
 * @todo Create and implement point labeling. This might should be its own interface.
 * @namespace \Altamira\JsWriter\Ability
 * @package JsWriter
 * @subpackage Ability
 * @author relwell
 */
interface Labelable
{
    /**
     * Activates the option for labeling the provided series 
     * @param  string|\Altamira\Series $series
     */
    public function useSeriesLabels( $series );
    
    /**
     * Specifies a labeling option for the provided series
     * @param string|\Altamira\Series $series
     * @param string $name
     * @param mixed $value
     */
    public function setSeriesLabelSetting( $series, $name, $value );
    
}