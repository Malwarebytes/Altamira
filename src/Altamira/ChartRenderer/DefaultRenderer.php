<?php 

namespace Altamira\ChartRenderer;

class DefaultRenderer extends RendererAbstract
{
    
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        $style = self::renderStyle( $styleOptions );
        $data = self::renderData( $chart );
        
        return <<<ENDDIV
<div class="{$chart->getLibrary()}" id="{$chart->getName()}" style="{$style}"{$data}>
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