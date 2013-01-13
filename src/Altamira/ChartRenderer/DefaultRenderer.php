<?php 
/**
 * Class definition for \Altamira\ChartREnderer\DefaultRenderer
 * @author relwell
 *
 */
namespace Altamira\ChartRenderer;
use Altamira\ChartRenderer\RendererInterface;

/**
 * This class is responsible for creating the placeholder div that 
 * the chart rendering libraries fill later on.
 * If we were to plug this into a framework, the first thing I would do is see if 
 * I could abstract these strings into something more class-based.
 * It might be a good idea to do this with PHP's native DOM class architecture.
 * @author relwell
 */
class DefaultRenderer implements RendererInterface
{
    /**
     * Responsible for generating opening tags and any content that needs to go directly inside 
     * @param \Altamira\Chart $chart
     * @param array $styleOptions
     * @return string
     */
    public static function preRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        $style = self::renderStyle( $styleOptions );
        
        return <<<ENDDIV
<div class="{$chart->getLibrary()}" id="{$chart->getName()}" style="{$style}">
ENDDIV;
        
    }
    
    /**
     * Responsible for closing any tags opened on preRender()
     * @param \Altamira\Chart $chart
     * @param array $styleOptions
     * @return string
     */
    public static function postRender( \Altamira\Chart $chart, array $styleOptions = array() )
    {
        return '</div>';
    }
    
    /**
     * Responsible for generating inline style as needed, called by preRender()
     * @param array $styleOptions
     * @return string
     */
    public static function renderStyle( array $styleOptions = array() )
    {
        $style = '';
        foreach ( $styleOptions as $key => $val ) 
        {
            $style .= "$key: $val; ";
        }
        return $style;
    }
    
}