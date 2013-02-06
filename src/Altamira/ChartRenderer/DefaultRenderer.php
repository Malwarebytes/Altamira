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
        $dataAttributes = self::getAttributesFromChart( $chart );
        
        return <<<ENDDIV
<div class="{$chart->getLibrary()} chart" id="{$chart->getName()}" {$dataAttributes} style="{$style}">
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
    
    /**
     * Populates data elements for the chart, which can be used for AJAX modifications.
     * @param \Altamira\Chart $chart
     * @return string
     */
    public static function getAttributesFromChart( \Altamira\Chart $chart )
    {
        $attributes = array(
                'library' => $chart->getLibrary(),
                'name' => $chart->getName()
        );
        
        if ( $type = $chart->getJsWriter()->getType() ) {
            $attributes['type'] = $type->getName();
        }
        
        $dataString = '';
        foreach ( $attributes as $key => $value )
        {
            $dataString .= sprintf( 'data-%s="%s" ', $key, $value );
        }
        
        return $dataString;
    }
    
}