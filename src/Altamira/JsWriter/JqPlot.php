<?php

namespace Altamira\JsWriter;

use \Altamira\JsWriter\Ability;

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
    const LIBRARY = 'jqplot';
    
    protected $typeNamespace = '\\Altamira\\Type\\JqPlot\\';
    
    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::generateScript()
     */
    public function generateScript()
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
                if ( array_key_exists( $title, $this->seriesLabels ) && is_array( $this->seriesLabels[$title] ) ) {
                    $datum->setLabel( $this->seriesLabels[$title][$num-1] );
                }
                $dataPrepped[] = $datum->getRenderData();
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
    
    public function useHighlighting( array $opts = array( 'size' => 7.5 ) )
    {
        extract( $opts );
        $size = isset( $size ) ? $size : 7.5;
        
        $this->files = array_merge_recursive( array( 'jqplot.highlighter.min.js' ), $this->files );
        $this->options['highlighter'] = array( 'sizeAdjust' => $size );

        return $this;
    }
    
    /**
     * 
     * @param array $options
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useZooming( array $options = array( 'mode'=>'xy' ) )
    {
        $this->files = array_merge_recursive( array( 'jqplot.cursor.min.js' ), $this->files );
        $this->setNestedOptVal( $this->options, 'cursor', 'show', true );
        $this->setNestedOptVal( $this->options, 'cursor', 'zoom', true );
    
        return $this;
    }
    
    /**
     * 
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useCursor()
    {
        $this->files = array_merge_recursive( array( 'jqplot.cursor.min.js' ), $this->files );
        $this->setNestedOptVal( $this->options, 'cursor', 'show', true );
        $this->setNestedOptVal( $this->options, 'cursor', 'showTooltip', true );
        
        return $this;
    }
    
    /**
     * formats a given axis for dates
     * @param string $axis
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useDates( $axis = 'x' )
    {
        if ( in_array( $axis, array( 'x', 'y', 'z' ) ) ) {
            $this->files = array_merge_recursive( array( 'jqplot.dateAxisRenderer.min.js' ), $this->files );
            $this->setNestedOptVal( $this->options, 'axes', $axis.'axis', 'renderer', '#$.jqplot.DateAxisRenderer#' );
        }
            
        return $this;
    }
    
    /**
     * 
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
        
        $this->options['seriesStorage'][$series]['shadow'] = $use;
		$this->options['seriesStorage'][$series]['shadowAngle'] = $angle;
		$this->options['seriesStorage'][$series]['shadowOffset'] = $offset;
		$this->options['seriesStorage'][$series]['shadowDepth'] = $depth;
		$this->options['seriesStorage'][$series]['shadowAlpha'] = $alpha;

		return $this;
    }
    
    /**
     * 
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
     * 
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
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getTypeOptions()
     */
    protected function getTypeOptions( array $options )
    {
        if(isset($this->types['default'])) {
            $options = array_merge_recursive( $options, $this->types['default']->getOptions() );
        }

        return $options;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getSeriesOptions()
     */
    protected function getSeriesOptions( array $options )
    {
        $types = $this->types;
        
        $defaults = array(  'highlighter' => array( 'show' => false ),
			                'cursor'      => array( 'showTooltip' => false, 'show' => false ),
                            'pointLabels' => array( 'show' => false )
                         );

        if( isset( $types['default'] ) ) {
            $renderer = $types['default']->getRenderer();
            
            if (isset( $renderer ) ) {
                $defaults['renderer'] = $renderer;
            }
            
            $defaults['rendererOptions'] = $types['default']->getRendererOptions();
            if( count( $defaults['rendererOptions'] ) == 0 ) {
                unset( $defaults['rendererOptions'] );
            }
            
            $options['seriesDefaults'] = $defaults;
        }

        $seriesOptions = array();
        if ( isset( $this->options['seriesStorage'] ) ) {
            foreach($this->options['seriesStorage'] as $title => $opts) {
                if( isset( $types[$title] ) ) {
                    $type = $types[$title];
                    $opts['renderer'] = $type->getRenderer();
                    array_merge_recursive( $opts, $type->getSeriesOptions() );
                }
                $opts['label'] = $title;
                
                $seriesOptions[] = $opts;
            }
        }
        
        $options['seriesStorage'] = $seriesOptions;
        $options['seriesDefaults'] = $defaults;
        
        return $options;
    }

    public function getOptionsJS()
    {
        $opts = $this->options;
        $opts['series'] = $opts['seriesStorage'];
        unset($opts['seriesStorage']);
        return $this->makeJSArray( $opts );
    }
    
    /**
     * Initializes default settings for using labels
     * @param string|\Altamira\Series $series
     * @param array $labels an array of strings for labels, in order
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useSeriesLabels( $series, array $labels = array() )
    {
        if ( !in_array( 'jqplot.pointLabels.min.js', $this->files ) ) {
            $this->files[] = 'jqplot.pointLabels.min.js';
        }
        $seriesTitle = $this->getSeriesTitle( $series );
        $this->seriesLabels[$seriesTitle] = $labels;
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'show', true )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'labels', $labels )
                    ->setNestedOptVal( $this->options, 'seriesStorage', $seriesTitle, 'pointLabels', 'edgeTolerance', 3 );
    }
    
    /**
     * Sets label setting option values
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