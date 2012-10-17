<?php 

namespace Altamira\ChartRenderer;
use Altamira\ChartRenderer\RendererInterface;

class DefaultRenderer implements RendererInterface
{
    
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        $style = self::renderStyle( $styleOptions );
        
        return <<<ENDDIV
<div class="{$chart->getLibrary()}" id="{$chart->getName()}" style="{$style}">
ENDDIV;
        
    }
    
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</div>';
    }
    
    public static function renderStyle( array $styleOptions = array() )
    {
        $style = '';
        foreach ( $styleOptions as $key=>$val ) {
            $style .= "$key: $val; ";
        }
        return $style;
    }
    
}