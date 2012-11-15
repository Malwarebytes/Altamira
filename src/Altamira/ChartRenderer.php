<?php

namespace Altamira;
use Altamira\ChartRenderer\RendererInterface;

use Altamira\ChartRenderer;

/**
 * Used to iterate over charts and render all of their components 
 * based on sub-renderers registered in a renderer chain
 */
class ChartRenderer
{
    /**
     * The singleton instance
     * @var \Altamira\ChartRenderer
     */
    protected static $instance;
    /**
     * Ordered set of renderer classes
     * @var array
     */
    protected static $rendererChain = array();
    
    /**
     * Cannot be invoked.
     * @see \Altamira\ChartRenderer::getInstance
     */
    protected function __construct(){}
    
    /**
     * Used to enforce the singleton design pattern within class. 
     * This helps provide a static fluent interface.
     * @return \Altamira\ChartRenderer
     */
    protected static function getInstance()
    {
        if ( self::$instance === null ) {
            self::$instance = new ChartRenderer();
        }
        return self::$instance;
    }
    
    /**
     * Renders a single chart by passing it through the renderer chain
     * @param  Chart $chart
     * @param  array $styleOptions
     * @return string the output generated from renders
     */
    public static function render( Chart $chart, array $styleOptions = array() )
    {
        if ( empty( self::$rendererChain ) ) {
            self::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
        }

        $outputString = '';

        for ( $i = count( self::$rendererChain ) - 1; $i >= 0; $i-- ) 
        {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array( array( $renderer, 'preRender' ), array( $chart, $styleOptions ) );
        }
        
        for ( $i = 0; $i < count( self::$rendererChain ); $i++ ) 
        {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array( array( $renderer, 'postRender' ), array( $chart, $styleOptions ) );
        }

        return $outputString;
    }
    
    /**
     * Adds a renderer to the end of the renderer chain
     * @param string $renderer
     * @throws \UnexpectedValueException
     * @return \Altamira\ChartRenderer
     */
    public static function pushRenderer( $renderer )
    {
        if (! in_array( 'Altamira\ChartRenderer\RendererInterface', class_implements( $renderer ) ) ) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }

        array_push( self::$rendererChain, $renderer );
        
        return self::getInstance();
    }
    
    /**
     * Prepends a renderer to the beginning of renderer chain
     * @param string $renderer
     * @throws \UnexpectedValueException
     * @return \Altamira\ChartRenderer
     */
    public static function unshiftRenderer( $renderer )
    {
        if (! in_array( 'Altamira\ChartRenderer\RendererInterface', class_implements( $renderer ) ) ) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }

        array_unshift( self::$rendererChain, $renderer );
        
        return self::getInstance();
    }
    
    /**
     * Clears the renderer chain
     * @return \Altamira\ChartRenderer
     */
    public static function reset()
    {
        self::$rendererChain = array();
        
        return self::getInstance();
    }
}