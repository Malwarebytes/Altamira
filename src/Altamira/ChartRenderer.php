<?php 

namespace Altamira;
use Altamira\ChartRenderer\RendererInterface;

use Altamira\ChartRenderer;

class ChartRenderer
{
    protected static $instance;
    protected static $rendererChain = array();
    
    protected function __construct(){}
    
    protected static function getInstance()
    {
        if ( self::$instance === null ) {
            self::$instance = new ChartRenderer();
        }
        return self::$instance;
    }
    
    public static function render( Chart $chart, array $styleOptions = array() )
    {
        if ( empty(self::$rendererChain) ) {
            self::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
        } 
            
        $outputString = '';

        for ( $i = count(self::$rendererChain)-1; $i >= 0; $i-- ) {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array(array($renderer, 'preRender'), array( $chart, $styleOptions ));
        }
        
        for ( $i = 0; $i < count(self::$rendererChain); $i++ ) {
            $renderer = self::$rendererChain[$i];
            $outputString .= call_user_func_array(array($renderer, 'postRender'), array( $chart, $styleOptions ));
        }
        
        return $outputString;
    }
    
    public static function pushRenderer( $renderer )
    {
        if (! in_array( 'Altamira\ChartRenderer\RendererInterface', class_implements( $renderer ) ) ) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }
        
        array_push( self::$rendererChain, $renderer );
        
        return self::getInstance();
    }
    
    public static function unshiftRenderer( $renderer )
    {
        if (! in_array( 'Altamira\ChartRenderer\RendererInterface', class_implements( $renderer ) ) ) {
            throw new \UnexpectedValueException( "Renderer must be instance of or string name of a class implementing RendererInterface" );
        }
        
        array_unshift( self::$rendererChain, $renderer );
        
        return self::getInstance();
    }
    
    public static function reset()
    {
        self::$rendererChain = array();
        
        return self::getInstance();
    }
    
}