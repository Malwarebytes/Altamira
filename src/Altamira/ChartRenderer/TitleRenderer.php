<?php 

namespace Altamira\ChartRenderer;
use Altamira\ChartRenderer\RendererInterface;

class TitleRenderer implements RendererInterface
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
    
    public static function renderStyle( array $styleOptions = array() )
    {
        return '';
    }

}