<?php
/**
 * Class definition for \Altamira\JsWriter\D3
 * @author relwell
 */
namespace Altamira\JsWriter;
use Altamira\JsWriter\Ability;
/**
 * JsWriter responsible for storing options and 
 * rendering values to cause D3 to render a specific chart.
 * @namespace \Altamira\JsWriter
 * @package JsWriter
 * @author relwell
 */
class D3
    extends JsWriterAbstract
    implements Ability\Fillable,
               Ability\Legendable,
               Ability\Cursorable,
               Ability\Zoomable,
               Ability\Labelable
{

    /**
     * Identifies the string value of which library this jsWriter is responsible for
     * @var string
     */
    const LIBRARY = 'd3';

    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\D3\\';

    /**
     * Allows us to register nvd3 framework as a plugin to D3 
     * @var array
     */
    protected $files = array( 'nv.d3.js' );
    
    /**
     * Holds on to additional directives that the setting of specific options may register
     * @var array
     */
    protected $extraDirectives = array();
    
    /**
     * Same as with Flot
     */
    protected $useSeriesLabels = true;
    
    /**
     * CSS
     * @var array
     */
    protected $extraCSS = array();
    
    /**
     * Allows us to control the style of series a bit better
     * @var array
     */
    protected $extraStyle = array();
    
    /** 
     * Not sure what to do with this yet. NVD3 is more functional than the other libs
     * @see \Altamira\JsWriter\JsWriterAbstract::getOptionsJS()
     */
    protected function getOptionsJS() 
    {
        return array();
    }

    /** 
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getScript()
     */
     public function getScript() 
     {
         return sprintf( self::ON_RENDER_END, implode( "\n", $this->extraStyle ) )
              . sprintf( self::ADD_GRAPH, $this->getType()->getChart(),
                                          implode( "\n", $this->extraDirectives ),
                                          $this->chart->getName(),
                                          $this->writeData(),
                                          $this->chart->getName()
                       )
               . sprintf( self::MAKE_CSS, implode( "\n", $this->extraCSS ) );
              ;
         
         ;
         
         
     }
     
    
    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getType()
     */
    public function getType( $series = null )
    {
        if ( ! isset( $this->types['default'] ) ) {
            $this->setType( 'Line' );
        }
        return $this->types['default'];
    }
    
    /**
     * Used for filling series in charts
     * @param string|\Altamira\Series $series
     * @param array $opts
     */
    public function setFill($series, $opts = array('use'    => true, 
                                                   'stroke' => false, 
                                                   'color'  => null, 
                                                   'alpha'  => null
                                                  )
                            )
    {
        if ( isset( $opts['use'] ) && !$opts['use'] ) {
            $this->setUseFill( $series, false );
        }
        if ( isset( $opts['color'] ) ) {
            $this->setFillColor( $series, $opts['color'] );
        }
        if ( isset( $opts['stroke'] ) ) {
            $this->setStrokeColor( $series, $opts['stroke'] );
        }
        
    }
    
    /**
     * Sets the fill color (we're splitting out array-based functionality so we can refactor interfaces soon)
     * @param string|\Altamira\Series $series
     * @param string $color
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    public function setFillColor( $series, $color ) {
        return $this->setSeriesOption( $this->getSeriesTitle( $series ), 'color', $color );
    }
    
    /**
     * Determines whether to use fill for a series
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    public function setUseFill( $series, $bool ) {
        return $bool ? $this : $this->setFillColor( $series, 'rgba(0, 0, 0, 0)' );
    }
    
    /**
     * Sets the stroke color for the series
     * @param \Altamira\Series|string $series
     * @param string $color
     * @return \Altamira\JsWriter\D3
     */
    public function setStrokeColor( $series, $color ) 
    {
        $call = $this->getType()->setStrokeColor( $this->chart->getName(), $this->getSeriesIndex( $series ), $color );
        if (! empty( $call ) ) { 
            $this->extraCSS[] = $call;
        }
        return $this->setStyleForSeries( $series, 'stroke', $color )
                    ->setStyleForSeries( $series, 'stroke-opacity', '1' )
                    ->setStyleForSeries( $series, 'stroke-width', '1px' );
    }
    
    /**
     * Sets the legend -- used to turn legend OFF
     * @see \Altamira\JsWriter\Ability\Legendable::setLegend()
     * @param array $opts
     * @return \Altamira\JsWriter\D3
     */
    public function setLegend( array $opts = array('on'       => 'true', 
                                                    'location' => 'ne', 
                                                    'x'        => 0, 
                                                    'y'        => 0) )
    {
        if ( isset( $opts['on'] ) && !( $opts['on'] ) ) {
            $this->extraDirectives[] = "chart.showLegend(false)\n";
        }
        return $this;
    }
    
    /**
     * Actually turns OFF cursor the first time. 
     * @todo probably a bad thing
     * @see \Altamira\JsWriter\Ability\Cursorable::useCursor()
     * @return \Altamira\JsWriter\D3
     */
    public function useCursor()
    {
        if ( isset( $this->options['hideCursor'] ) && $this->options['hideCursor'] ) {
            $this->extraDirectives[] = "chart.tooltips(true);\n";
            unset( $this->options['hideCursor'] );
        } else {
            $this->extraDirectives[] = "chart.tooltips(false);\n";
            $this->options['hideCursor'] = true;
        }
        return $this;
    }
    
    /**
     * Turns on focus chart
     * @see \Altamira\JsWriter\Ability\Zoomable::useZooming()
     * @param array
     * @return \Altamira\JsWriter\D3
     */
    public function useZooming( array $options = array( 'mode'=>'xy' ) )
    {
        $this->setType( 'LineWithFocus' );
        return $this;
    }
    
    /**
     * Activates the option for labeling the provided series 
     * @param  string|\Altamira\Series $series
     */
    public function useSeriesLabels( $series )
    {
        if ( $this->useSeriesLabels == true ) {
            $this->extraDirectives[] = "chart.showLabels(false);";
            $this->useSeriesLabels = false;
        } else {
            $this->extraDirectives[] = "chart.showLabels(true);";
            $this->useSeriesLabels = true;
        }
        return $this;
    }
    
    /**
     * Specifies a labeling option for the provided series
     * @param string|\Altamira\Series $series
     * @param string $name
     * @param mixed $value
     */
    public function setSeriesLabelSetting( $series, $name, $value )
    {
        //@todo
        return $this;
    }
    
    public function setAxisOptions() { /* @todo */ return $this; }
    public function setAxisTicks() { /* @todo */ return $this; }
    
    
    /**
     * Allows us to set the style of a specific series
     * @param \Altamira\Series|string $series
     * @param string $key
     * @param string $value
     * @return \Altamira\JsWriter\D3
     */
    protected function setStyleForSeries( $series, $key, $value ) 
    {
        $this->extraStyle[] = sprintf( self::SET_STYLE_FOR_SERIES, 
                                       $this->chart->getName(), 
                                       $this->getSeriesIndex( $series ),
                                       $key, 
                                       $value,
                                       $key,
                                       $value );
        return $this;
    }
     
    /**
     * Gives us the ordering of the series. This allows us to do some css selector black magic.
     * @param \Altamira\Series|string $series
     * @return int|false
     */
    protected function getSeriesIndex( $series ) 
    {
        $array = array_keys( $this->options['seriesStorage'] );
        return array_search( $this->getSeriesTitle( $series ), $array, true );
    }
    
    /**
     * Provided data registered in a series, declare the data for that series
     * @return string
     */
    protected function writeData()
    {
        $jsonBuffer = "[\n";
        $counter = 0;
        foreach ( $this->chart->getSeries() as $series )
        {
            if ( $counter++ > 0 ) {
                $jsonBuffer .= "\t,\n";
            }
            
            $data = $this->getDataFromSeries( $series );
            
            $jsonBuffer .= "\t".json_encode( $data )."\n";
        }
        
        $jsonBuffer .= "\n]";
        
        return $jsonBuffer;
    }
    
    /**
     * Adds values to a series based on any options set
     * @param \Altamira\Series $series
     */
    protected function getDataFromSeries( \Altamira\Series $series )
    {
        $title = $this->getSeriesTitle( $series );
        $data = array( 'values' => array(), 'key' => $title );

        $style = array();
        if ( $color = $this->getSeriesOption( $series, 'color' ) ) {
            $data['color'] = $color;
        }
        
        foreach ( $series->getData() as $datum )
        {
            $data['values'][] = $this->getDataFromDatum( $datum );
        }
        
        return $data;
    }
    
    /**
     * Prepares the array which we turn into JSON for a given datum
     * @param \Altamira\ChartDatum $datum
     */
    protected function getDataFromDatum( \Altamira\ChartDatum\ChartDatumAbstract $datum ) 
    {
        $datumArray = $datum->toArray();
        // reformat bubble radius to size
        if ( isset( $datumArray['radius'] ) ) {
            $datumArray['size'] = $datumArray['radius'];
            unset( $datumArray['radius'] );
        }
        return $datumArray;
    }
    
    /**
     * Lets you customize your chart
     * @param array $functionCalls
     * @return \Altamira\JsWriter\D3
     */
    public function pushExtraFunctionCalls( array $functionCalls )
    {
        $this->extraDirectives += $functionCalls;
        return $this;
    }

    
    const ADD_GRAPH = <<<ENDSCRIPT
nv.addGraph(function() {  
    %s
    %s
    d3.select('#%s svg')
      .datum(%s)
      .transition()
      .duration(500)
      .call(chart);
    nv.utils.windowResize(function() { d3.select('#%s svg').call(chart) });
    return chart;
});
ENDSCRIPT;
    
    const SET_STYLE_FOR_SERIES = <<<ENDSCRIPT
jQuery('#%s .nv-series-%s').css( "%s", "%s" );
ENDSCRIPT;
    
    const ON_RENDER_END = <<<ENDSCRIPT
nv.dispatch.on('render_end', function(e) {
    %s
});    
ENDSCRIPT;
    
    const MAKE_CSS = <<<ENDSCRIPT
jQuery( 'body' ).append( jQuery('<style></style>').text("%s") );
ENDSCRIPT;
    

}