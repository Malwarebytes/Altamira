<?php

namespace Altamira\ChartRenderer;

interface RendererInterface
{
    
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() );
    
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() );
    
    public static function renderStyle( array $styleOptions = array() );
    
}