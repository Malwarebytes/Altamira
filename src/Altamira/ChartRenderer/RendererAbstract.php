<?php 

namespace Altamira\ChartRenderer;

abstract class RendererAbstract
{
    abstract public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() );
    
    abstract public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() );
    
    public static function renderStyle( array $styleOptions = array() ) 
    {
        return '';
    }
    
    public static function renderData( \Altamira\Chart $chart ) 
    {
        return '';
    }
}