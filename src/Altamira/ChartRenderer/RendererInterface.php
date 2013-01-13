<?php
/**
 * Class definition for \Altamira\ChartRenderer\RendererInterface
 * @author relwell
 *
 */
namespace Altamira\ChartRenderer;

/**
 * Builds a standard interface about how we render HTML items
 * This interface allows for opening and closing tags around other logic, as well as style
 */
interface RendererInterface
{
    /**
     * A hook for including things like opening tags
     * @param \Altamira\Chart $chart
     * @param array $styleOptions
     */
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() );
    /**
     * A hook for including things like closing tags
     * @param \Altamira\Chart $chart
     * @param array $styleOptions
     */
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() );
    /**
     * Used to hold metadata on style about the item rendered
     * @param array $styleOptions
     */
    public static function renderStyle( array $styleOptions = array() );
    
}