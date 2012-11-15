<?php 

namespace Altamira\ChartRenderer;
use Altamira\ChartRenderer\RendererInterface;

class TitleRenderer implements RendererInterface
{
    /**
     * Adds open wrapping div and puts title in h3 tags by default, but configurable with titleTag key in style
     * @param  \Altamira\Chart $chart
     * @param  array $styleOptions
     * @return string
     */
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        $tagType = isset( $styleOptions['titleTag'] ) ? $styleOptions['titleTag'] : 'h3';
        $title = $chart->getTitle();
        
        $output = <<<ENDDIV
<div class="altamira-chart-title">
    <{$tagType}>{$title}</{$tagType}>

ENDDIV;
        
        return $output;
    }
    
    /**
     * Closes div created on preRender
     * @param  \Altamira\Chart $chart
     * @param  array $styleOptions
     * @return string
     */
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</div>';
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