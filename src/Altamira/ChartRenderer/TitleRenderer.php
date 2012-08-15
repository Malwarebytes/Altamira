<?php 

namespace Altamira\ChartRenderer;

class TitleRenderer extends RendererAbstract
{
    
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        $tagType = isset($styleOptions['titleTag']) ? $styleOptions['titleTag'] : 'h3';
        $title = $chart->getTitle();
        
        $output = <<<ENDDIV
<div class="altamira-chart-title">
    <{$tagType}>{$title}</{$tagType}>

ENDDIV;
        
        return $output;
    }
    
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</div>';
    }

}