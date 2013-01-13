<?php
/**
 * Class definition for \Altamira\JsWriter\JqPlot
 * @author relwell
 *
 */
namespace Altamira\JsWriter;
use \Altamira\JsWriter\Ability;
/**
 * This is the class responsible for configuring and then 
 * writing out data for a single chart using the JqPlot library.
 * This is automatically registered based on the library parameter passed
 * on any chart you instantiate. Most configurations are encapsulated by 
 * either the chart or series registered with the charts.
 * @namespace \Altamira\JsWriter
 * @package JsWriter
 * @author relwell
 *
 */
class JqPlot
    extends \Altamira\JsWriter\JsWriterAbstract
    implements Ability\Cursorable,
               Ability\Datable,
               Ability\Fillable,
               Ability\Griddable,
               Ability\Highlightable,
               Ability\Legendable,
               Ability\Shadowable,
               Ability\Labelable,
               Ability\Lineable
{
    /**
     * Used to identify the library correlating to this class
     * @var string
     */
    const LIBRARY = 'jqplot';
    
    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\JqPlot\\';
    
    /**
     * Global and chart-specific options. Stored here to make it easier to json-encode.
     * @var array
     */
    protected $options = array( 'seriesStorage' => array(), 
                                'seriesDefaults' => array(  'highlighter' => array( 'show' => false ),
                                			                'cursor'      => array( 'showTooltip' => false, 'show' => false ),
                                                            'pointLabels' => array( 'show' => false )
                                                         ) 
                              );
    
    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getScript()
     */
    public function getScript()
    {
        $output  = '$(document).ready(function(){';
        $output .= '$.jqplot.config.enablePlugins = true;';

        $num = 0;
        $vars = array();        
        
        foreach($this->chart->getSeries() as $series) {
            $num++;
            $data        = $series->getData();
            $dataPrepped = array();
            $title       = $series->getTitle();
            $labelCopy   = null;
            
            foreach  ($data as &$datum ) {
                $renderData = $datum->getRenderData();
                
                if ( $this->getNestedOptVal( $this->options, 'seriesDefaults', 'pointLabels', 'show' )
                        || $this->getNestedOptVal( $this->options, 'seriesStorage', $title, 'pointLabels', 'show' ) ) {
                    $renderData[] = $datum->getLabel();
                }
                $dataPrepped[] = $renderData;
            }
            $varname = 'plot_' . $this->chart->getName() . '_' . $num;
            $vars[] = '#' . $varname . '#';
            $output .= sprintf( '%s=%s;', $varname, $this->makeJSArray( $dataPrepped ) );
        }
        $output .= sprintf( 'plot = $.jqplot("%s", %s, %s);', 
                            $this->chart->getName(), 
                            $this->makeJSArray( $vars ),
                            $this->getOptionsJS()
                          );
        $output .= '});';

        return $output;

    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Highlightable
     * @see \Altamira\JsWriter\Ability\Highlightable::useHighlighting()
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useHighlighting( array $opts = array( 'size' => 7.5 ) )
    {
        extract( $opts );
        $size = isset( $size ) ? $size : 7.5;
        
        $this->files = array_merge_recursive( array( 'jqplot.highlighter.js' ), $this->files );
        $this->options['highlighter'] = array( 'sizeAdjust' => $size );

        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Zoomable
     * @see \Altamira\JsWriter\Ability\Zoomable::useZooming()
     * @param array $options
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useZooming( array $options = array( 'mode'=>'xy' ) )
    {
        $this->files = array_merge_recursive( array( 'jqplot.cursor.js' ), $this->files );
        $this->setNestedOptVal( $this->options, 'cursor', 'show', true );
        $this->setNestedOptVal( $this->options, 'cursor', 'zoom', true );
    
        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Cursorable
     * @see \Altamira\JsWriter\Ability\Cursorable::useCursor()
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useCursor()
    {
        $this->files = array_merge_recursive( array( 'jqplot.cursor.js' ), $this->files );
        $this->setNestedOptVal( $this->options, 'cursor', 'show', true );
        $this->setNestedOptVal( $this->options, 'cursor', 'showTooltip', true );
        
        return $this;
    }
    
    /**
     * formats a given axis for dates
     * @see \Altamira\JsWriter\Ability\Datable::useDates()
     * @param string $axis
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useDates( $axis = 'x' )
    {
        if ( in_array( $axis, array( 'x', 'y', 'z' ) ) ) {
            $this->files = array_merge_recursive( array( 'jqplot.dateAxisRenderer.js' ), $this->files );
            $this->setNestedOptVal( $this->options, 'axes', $axis.'axis', 'renderer', '#$.jqplot.DateAxisRenderer#' );
        }
            
        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Shadowable
     * @see \Altamira\JsWriter\Ability\Shadowable::setShadow()
     * @param \Altamira\Series|string $series
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setShadow($series, $opts = array('use'    => true, 
                                                     'angle'  => 45, 
                                                     'offset' => 1.25, 
                                                     'depth'  => 3, 
                                                     'alpha'  => 0.1 ) )
    {
        extract($opts);
        
        $series = $this->getSeriesTitle( $series );
        
        $use    = isset( $use )    ? $use    : true;
        $angle  = isset( $angle )  ? $angle  : 45;
        $offset = isset( $offset ) ? $offset : 1.25; 
        $depth  = isset( $depth )  ? $depth  : 3; 
        $alpha  = isset( $alpha )  ? $alpha  : 0.1;
        
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $series, 'shadow', $use )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $series, 'shadowAngle', $angle )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $series, 'shadowOffset', $offset )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $series, 'shadowDepth', $depth )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $series, 'shadowAlpha', $alpha ); 
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Fillable
     * @see \Altamira\JsWriter\Ability\Fillable::setFill()
     * @param \Altamira\Chart|series $series
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setFill($series, $opts = array('use'    => true, 
                                                   'stroke' => false, 
                                                   'color'  => null, 
                                                   'alpha'  => null
                                                  )
                            )
    {
        extract($opts);
        
        $use    = isset( $use)     ? $use    : true;
        $stroke = isset( $stroke ) ? $stroke : false;
        $color  = isset( $color )  ? $color  : null;
        $alpha  = isset( $alpha )  ? $alpha  : null;
        
        $series = $this->getSeriesTitle( $series );
        $this->setNestedOptVal( $this->options, 'seriesStorage', $series, 'fill', $use );
        $this->setNestedOptVal( $this->options, 'seriesStorage', $series, 'fillAndStroke', $stroke);
        
        $this->options['seriesStorage'][$series]['fill'] = $use;
        $this->options['seriesStorage'][$series]['fillAndStroke'] = $stroke;
        
        if (! empty( $color ) ) {
            $this->setNestedOptVal( $this->options, 'seriesStorage', $series, 'fillColor', $color );
        }
        if (! empty( $alpha ) ) {
            $this->setNestedOptVal( $this->options, 'seriesStorage', $series, 'fillAlpha', $alpha );
        }

        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Griddable
     * @see \Altamira\JsWriter\Ability\Griddable::setGrid()
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setGrid( array $opts )
    {
        extract( $opts );
        $on         = isset( $on )         ? $on         : true; 
        $color      = isset( $color )      ? $color      : null;
        $background = isset( $background ) ? $background : null;
        
        $this->setNestedOptVal( $this->options, 'grid', 'drawGridLines', $on );
        $this->options['grid']['drawGridLines'] = $on;
        if(! empty( $color ) ) {
            $this->setNestedOptVal( $this->options, 'grid', 'gridLineColor', $color );
        }
        if(! empty( $background ) ) {
            $this->setNestedOptVal( $this->options, 'grid', 'background', $background );
        }

        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Legendable
     * @see \Altamira\JsWriter\Ability\Legendable::setLegend()
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setLegend( array $opts = array('on'       => 'true', 
                                                   'location' => 'ne', 
                                                   'x'        => 0, 
                                                   'y'        => 0 ) )
    {
        extract( $opts );
        
        $on       = isset( $on )       ? $on       : true;
        $location = isset( $location ) ? $location : 'ne';
        $x        = isset( $x )        ? $x        : 0;
        $y        = isset( $y )        ? $y        : 0;
        
        
        if (! $on ) {
            unset( $this->options['legend'] );
        } else {
            $legend = array();
            $legend['show'] = true;
            if ( $location == 'outside' || $location == 'outsideGrid' ) {
                $legend['placement'] = $location;
            } else {
                $legend['location'] = $location;
            }
            if ( $x != 0 ) {
                $legend['xoffset'] = $x;
            }
            if ( $y != 0 ) {
                $legend['yoffset'] = $y;
            }
            $this->options['legend'] = $legend;
        }

        return $this;
    }
    
    /**
     * Used to format the axis of the registered chart
     * @param string $axis
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setAxisOptions($axis, $name, $value)
    {
        if(strtolower($axis) === 'x' || strtolower($axis) === 'y') {
            $axis = strtolower($axis) . 'axis';

            if ( in_array( $name, array( 'min', 'max', 'numberTicks', 'tickInterval', 'numberTicks' ) ) ) {
                
                $this->setNestedOptVal( $this->options, 'axes', $axis, $name, $value );
                
            } elseif( in_array( $name, array( 'showGridline', 'formatString' ) ) ) {
                
                $this->setNestedOptVal( $this->options, 'axes', $axis, 'tickOptions', $name, $value );
                
            }
        }

        return $this;
    }

    /**
     * Registers a type for a series or entire chart
     * @see \Altamira\JsWriter\JsWriterAbstract::setType()
     * @param string|Altamira\Type\TypeAbstract $type
     * @param array $options
     * @param string $series
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setType( $type, $options = array(), $series = 'default' )
    {
        parent::setType( $type, $options, $series );
        if ( $series == 'default' ) {
            $rendererOptions = $this->types['default']->getRendererOptions();
            if ( $renderer = $this->types['default']->getRenderer() ) {
                $this->options['seriesDefaults']['renderer'] = $renderer;
            }
            if (! empty( $rendererOptions ) ) {
                $this->options['seriesDefaults']['rendererOptions'] = $rendererOptions;
            }
        }
        return $this;
    }

    /**
     * Prepares options and returns JSON
     * @return string
     */
    protected function getOptionsJS()
    {
        $opts = $this->options;
        foreach ( $opts['seriesStorage'] as $label => $options ) {
            $options['label'] = $label;
            $opts['series'][] = $options;
        }
        
        if ( $this->chart->titleHidden() ) {
        	unset( $opts['title'] );
        }
        
        unset($opts['seriesStorage']);
        return $this->makeJSArray( $opts );
    }
    
    /**
     * Initializes default settings for using labels
     * @see \Altamira\JsWriter\Ability\Labelable::useSeriesLabels()
     * @param string|\Altamira\Series $series
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useSeriesLabels( $series )
    {
        if ( !in_array( 'jqplot.pointLabels.js', $this->files ) ) {
            $this->files[] = 'jqplot.pointLabels.js';
        }
        $seriesTitle = $this->getSeriesTitle( $series );
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'show', true )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'edgeTolerance', 3 );
    }
    
    /**
     * Sets label setting option values
     * @see \Altamira\JsWriter\Ability\Labelable::setSeriesLabelSetting()
     * @param string $series
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesLabelSetting( $series, $name, $value )
    {
        if (  ( $name === 'location' && in_array( $value, array( 'n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw' ) ) ) 
            ||( in_array( $name, array( 'xpadding', 'ypadding', 'edgeTolerance', 'stackValue' ) ) ) ) {
            return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'pointLabels', $name, $value );
        }
        return $this;
    }
    
    /**
     * Determines the width of the line we will show, if we're showing it
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesLineWidth()
     * @param string $series
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesLineWidth( $series, $value )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'lineWidth', $value );
    }
    
    /**
     * Determines whether we show the line for a series
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesShowLine()
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesShowLine( $series, $bool )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'showLine', $bool );
    }
    
    /**
     * Determines whether we show the marker for a series
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesShowMarker()
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesShowMarker( $series, $bool )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'showMarker', $bool );
    }
    
    /**
     * Sets the style of the marker
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesMarkerStyle()
     * @param string|\Altamira\Series $series
     * @param string $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesMarkerStyle( $series, $value )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'markerOptions', 'style', $value );
    }
    
    /**
     * Sets the size of the marker
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesMarkerSize()
     * @param string|\Altamira\Series $series
     * @param mixed $value
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setSeriesMarkerSize( $series, $value )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'markerOptions', 'size', $value );
    }
    
    /**
     * Sets the labels demarcated on a given axis
     * @param string $axis
     * @param array $ticks
     * @return \Altamira\JsWriter\JqPlot
     */
    public function setAxisTicks( $axis, array $ticks )
    {
        return $this->setNestedOptVal( $this->options, 'axes', $axis.'axis', 'ticks', $ticks );
    }

}