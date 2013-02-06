<?php
/**
 * Class definition for \Altamira\ChartRenderer\SVGRenderer
 */
namespace Altamira\ChartRenderer;
use Altamira\ChartRenderer\RendererInterface;
/**
 * Responsible for creating an SVG element for D3
 * @author relwell
 */
class SVGRenderer implements RendererInterface
{
    /**
     * Adds open wrapping div and puts title in h3 tags by default, but configurable with titleTag key in style
     * If the chart has been set to hide its title, then it will not display
     * @param  \Altamira\Chart $chart
     * @param  array $styleOptions
     * @return string
     */
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '<svg>';
    }
    
    /**
     * Closes div created on preRender
     * @param  \Altamira\Chart $chart
     * @param  array $styleOptions
     * @return string
     */
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</svg>';
    }
    
    /**
     * Does nothing for now, but must be implemented by RendererInterface 
     * @param  array $styleOptions
     * @return string
     */
    public static function renderStyle( array $styleOptions = array() )
    {
        return '';
    }
    
}