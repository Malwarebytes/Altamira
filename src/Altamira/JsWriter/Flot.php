<?php
/**
 * Class definition for \Altmaira\JsWriter\Flot
 * @author relwell
 */
namespace Altamira\JsWriter;
use Altamira\JsWriter\Ability;
use Altamira\ChartDatum;
/**
 * JsWriter responsible for storing options and rendering
 * values to cause JqPlot to render a specific chart.
 * Generally, these method calls will be encapsulated by the 
 * chart that registers its own JsWriter upon instantiation.
 * @namespace \Altamira\JsWriter
 * @package JsWriter
 * @author relwell
 */
class Flot
    extends JsWriterAbstract
    implements Ability\Cursorable,
               Ability\Datable,
               Ability\Fillable,
               Ability\Griddable,
               Ability\Highlightable,
               Ability\Legendable,
               Ability\Shadowable,
               Ability\Zoomable,
               Ability\Labelable,
               Ability\Lineable
{
    /**
     * Identifies the string value of which library this jsWriter is responsible for
     * @var string
     */
    const LIBRARY = 'flot';
    
    /**
     * Used to identify the type namespace for this particualr JsWriter 
     * @var string
     */
    protected $typeNamespace = '\\Altamira\\Type\\Flot\\';

    /**
     * Used to track whether dates have been registered on this chart
     * @var array
     */
    protected $dateAxes = array('x'=>false, 'y'=>false);
    
    /**
     * Used to track whether zooming has been set
     * @var bool
     */
    protected $zooming = false;
    
    /**
     * Used to track whether highlighting has been set
     * @var bool
     */
    protected $highlighting = false;
    
    /**
     * Stores labels for each point
     * @var array
     */
    protected $pointLabels = array();
    
    /**
     * Stores settings for label placement
     * @var array
     */
    protected $labelSettings = array('location'=>'w','xpadding'=>'0','ypadding'=>'0');

    /**
     * (non-PHPdoc)
     * @see \Altamira\JsWriter\JsWriterAbstract::getScript()
     */
    public function getScript()
    {
        $name = $this->chart->getName();
        $dataArrayJs = '[';

        $counter = 0;
        foreach ( $this->chart->getSeries() as $title => $series ) {

            $dataArrayJs .= $counter++ > 0 ? ', ' : '';

            $dataArrayJs .= '{';

            // associate Xs with Ys in cases where we need it
            $data = $series->getData();

            $formattedData = array();
            foreach ( $data as $datum ) { 
                if (! $datum instanceof ChartDatum\ChartDatumAbstract ) {
                    throw new \UnexpectedValueException('Chart data should be an object inheriting from ChartDatumAbstract');
                }
                foreach ( $this->dateAxes as $axis => $flag ) {
                    if ( $flag ) {
                        $date = \DateTime::createFromFormat( 'm/d/Y', $datum[$axis] );
                        $datum[$axis] = $date->getTimestamp() * 1000;
                    }
                }
                        
                if ( $this->useLabels ) {
                    $dataPoints = "{$datum['x']},{$datum['y']}";
                    $this->pointLabels[$dataPoints] = $datum->getLabel();
                }
                
                $formattedData[] = $datum->getRenderData();
            }
            
            $dataArrayJs .= 'data: '.$this->makeJSArray($formattedData);
            
            if ( isset( $this->types['default'] ) && 
               ( $this->types['default'] instanceOf \Altamira\Type\Flot\Bubble
                || $this->types['default'] instanceOf \Altamira\Type\Flot\Donut ) ) {
                $dataArrayJs .= ', label: "' . str_replace('"', '\\"', $series->getTitle() ) . '"';
            }

            $this->prepOpts( $this->options['seriesStorage'][$title] );

            $opts = substr( json_encode( $this->options['seriesStorage'][$title] ), 1, -1 );

            if (strlen($opts) > 2) {
                $dataArrayJs .= ',' . $opts;
            }

            $dataArrayJs .= '}';
        }

        $dataArrayJs .= ']';

        $optionsJs = ($js = $this->getOptionsJs()) ? ", {$js}" : ', {}';

        $extraFunctionCallString = implode("\n", $this->getExtraFunctionCalls($dataArrayJs, $optionsJs));

        return sprintf( self::SCRIPT_OUTPUT, $name, $dataArrayJs, $optionsJs, $extraFunctionCallString );
    }

    /**
     * Populates script output with hacks required to give Flot featural parity with jqPlot
     * @param unknown_type $dataArrayJs
     * @param unknown_type $optionsJs
     * @return multitype:string
     */
    public function getExtraFunctionCalls($dataArrayJs, $optionsJs)
    {
        $extraFunctionCalls = array();

        if ($this->zooming) {
            $extraFunctionCalls[] = sprintf( self::ZOOMING_FUNCTION, $dataArrayJs, $optionsJs, $dataArrayJs, $optionsJs );
        }

        if ($this->useLabels) {
            $seriesLabels = json_encode($this->pointLabels);

            $top = '';
            $left = '';
            $pixelCount = '15';

            for ( $i = 0; $i < strlen($this->labelSettings['location']); $i++ ) {
                switch ( $this->labelSettings['location'][$i] ) {
                    case 'n':
                        $top = '-'.$pixelCount;
                        break;
                    case 'e':
                        $left = '+'.$pixelCount;
                        break;
                    case 's':
                        $top = '+'.$pixelCount;
                        break;
                    case 'w':
                        $left = '-'.$pixelCount;
                }
            }

            $paddingx = '-'.(isset($this->labelSettings['xpadding']) ? $this->labelSettings['xpadding'] : '0');
            $paddingy = '-'.(isset($this->labelSettings['ypadding']) ? $this->labelSettings['ypadding'] : '0');

            $extraFunctionCalls[] = sprintf( self::LABELS_FUNCTION, $seriesLabels, $left, $paddingx, $top, $paddingy );

        }

        if ($this->highlighting) {

            $formatPoints = "x + ',' + y";

            foreach ($this->dateAxes as $axis=>$flag) {
                if ($flag) {
                    $formatPoints = str_replace($axis, "(new Date(parseInt({$axis}))).toLocaleDateString()",$formatPoints);
                }
            }

            $extraFunctionCalls[] =  sprintf( self::HIGHLIGHTING_FUNCTION, $formatPoints );

        }

        return $extraFunctionCalls;

    }

    /**
     * Sets an option for a given axis
     * @param string $axis
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\Flot
     */
    public function setAxisOptions($axis, $name, $value)
    {
        if( strtolower($axis) === 'x' || strtolower($axis) === 'y' ) {
            $axis = strtolower($axis) . 'axis';

            if ( array_key_exists( $name, $this->nativeOpts[$axis] ) ) {
                $this->setNestedOptVal( $this->options, $axis, $name, $value );
            } else {
                $key = 'axes.'.$axis.'.'.$name;

                if ( isset( $this->optsMapper[$key] ) ) {
                    $this->setOpt($this->options, $this->optsMapper[$key], $value);
                }

                if ( $name == 'formatString' ) {
                    $this->options[$axis]['tickFormatter'] = $this->getCallbackPlaceholder('function(val, axis){return "'.$value.'".replace(/%d/, val);}');
                }

            }
        }

        return $this;
    }

    /**
     * Registers a series, performing some additional logic
     * @see \Altamira\JsWriter\JsWriterAbstract::initializeSeries()
     * @param \Altamira\Series|string $series
     * @return \Altamira\JsWriter\Flot
     */
    public function initializeSeries( $series )
    {
        parent::initializeSeries($series);
        $title = $this->getSeriesTitle($series);
        $this->options['seriesStorage'][$title]['label'] = $title; 
        return $this;
    }

    /**
     * Mutates the option array to the format required for flot
     * @return Ambigous <string, mixed>
     */
    protected function getOptionsJS()
    {
        foreach ($this->optsMapper as $opt => $mapped)
        {
            if (($currOpt = $this->getOptVal($this->options, $opt)) && ($currOpt !== null)) {
                $this->setOpt($this->options, $mapped, $currOpt);
                $this->unsetOpt($this->options, $opt);
            }
        }

        $opts = $this->options;

        // stupid pie plugin
        if ( $this->getOptVal( $opts, 'seriesStorage', 'pie', 'show' ) === null ) {
            $this->setNestedOptVal( $opts, 'seriesStorage', 'pie', 'show', false );
        }
        
        $this->unsetOpt( $opts, 'seriesStorage' );
        $this->unsetOpt( $opts, 'seriesDefault' );

        return $this->makeJSArray($opts);
    }

    /**
     * Retrieves a nested value or null
     * @param array $opts
     * @param mixed $option
     * @return Ambigous <>|NULL|multitype:
     */
    protected function getOptVal(array $opts, $option)
    {
        $ploded = explode('.', $option);
        $arr = $opts;
        $val = null;
        while ($curr = array_shift($ploded)) {
            if (isset($arr[$curr])) {
                if (is_array($arr[$curr])) {
                    $arr = $arr[$curr];
                } else {
                    return $arr[$curr];
                }
            } else {
                return null;
            }
        }
    }

    /**
     * Sets a value in a nested array based on a dot-concatenated string
     * Used primarily for mapping
     * @param array $opts
     * @param string $mapperString
     * @param mixed $val
     */
    protected function setOpt(array &$opts, $mapperString, $val)
    {
        $args = explode( '.', $mapperString );
        array_push( $args, $val );
        $this->setNestedOptVal( $opts, $args ); 
    }

    /**
     * Handles nested mappings 
     * @param array $opts
     * @param string $mapperString
     */
    protected function unsetOpt(array &$opts, $mapperString)
    {
        $ploded = explode('.', $mapperString);
        $arr = &$opts;
        while ($curr = array_shift($ploded)) {
            if (isset($arr[$curr])) {
                if (is_array($arr[$curr])) {
                    $arr = &$arr[$curr];
                } else {
                    unset($arr[$curr]);
                }
            }
        }
    }

    /**
     * Implemented from \Altamira\JsWriter\Ability\Highlightable
     * @see \Altamira\JsWriter\Ability\Highlightable::useHighlighting()
     * @param array $opts
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useHighlighting(array $opts = array('size'=>7.5))
    {
        $this->highlighting = true;

        return $this->setNestedOptVal( $this->options, 'grid', 'hoverable', true )
                    ->setNestedOptVal( $this->options, 'grid', 'autoHighlight', true );
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Cursorable
     * @see \Altamira\JsWriter\Ability\Cursorable::useCursor()
     * @return \Altamira\JsWriter\JqPlot
     */
    public function useCursor()
    {
        return $this->setNestedOptVal( $this->options, 'cursor', array('show' => true, 'showTooltip' => true) );
    }
    
    /**
     * formats a given axis for dates
     * @see \Altamira\JsWriter\Ability\Datable::useDates()
     * @param string $axis
     * @return \Altamira\JsWriter\Flot
     */
    public function useDates($axis = 'x')
    {
        $this->dateAxes[$axis] = true;
        
        $this->setNestedOptVal( $this->options, $axis.'axis', 'mode', 'time' );
        $this->setNestedOptVal( $this->options, $axis.'axis', 'timeformat', '%d-%b-%y' );

        array_push($this->files, 'jquery.flot.time.js');

        return $this;
    }

    /**
     * Implemented from \Altamira\JsWriter\Ability\Zoomable
     * @see \Altamira\JsWriter\Ability\Zoomable::useZooming()
     * @param array $options
     * @return \Altamira\JsWriter\Flot
     */
    public function useZooming( array $options = array('mode'=>'xy') )
    {
        $this->zooming = true;
        $this->setNestedOptVal( $this->options, 'selection', 'mode', $options['mode'] );
        $this->files[] = 'jquery.flot.selection.js';
        return $this;
    }

    /**
     * Implemented from \Altamira\JsWriter\Ability\Griddable
     * @see \Altamira\JsWriter\Ability\Griddable::setGrid()
     * @param array $opts
     * @return \Altamira\JsWriter\Flot
     */
    public function setGrid(array $opts)
    {

        $gridMapping = array('on'=>'show',
                             'background'=>'backgroundColor'
                            );
        
        foreach ($opts as $key=>$value) {
            if ( array_key_exists( $key, $this->nativeOpts['grid'] ) ) {
                $this->setNestedOptVal( $this->options, 'grid', $key, $value );
            } else if ( isset( $gridMapping[$key] ) ) {
                $this->setNestedOptVal( $this->options, 'grid', $gridMapping[$key], $value );
            }
        }

        return $this;

    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Legendable
     * @see \Altamira\JsWriter\Ability\Legendable::setLegend()
     * @param array $opts
     * @return \Altamira\JsWriter\Flot
     */
    public function setLegend(array $opts = array('on' => 'true', 
                                                  'location' => 'ne', 
                                                  'x' => 0, 
                                                  'y' => 0))
    {
        $opts['on']       = isset($opts['on']) ? $opts['on'] : true;
        $opts['location'] = isset($opts['location']) ? $opts['location'] : 'ne';

        $legendMapper = array('on'       => 'show',
                              'location' => 'position');

        foreach ($opts as $key=>$val) {
            if ( array_key_exists($key, $this->nativeOpts['legend']) ) {
                $this->setNestedOptVal( $this->options, 'legend', $key, $val );
            } else if ( in_array($key, array_keys($legendMapper)) ) {
                $this->setNestedOptVal( $this->options, 'legend', $legendMapper[$key], $val );
            }
        }

        $margin = array(
                    isset($opts['x']) ? $opts['x'] : 0, 
                    isset($opts['y']) ? $opts['y'] : 0
                );

        return $this->setNestedOptVal( $this->options, 'legend', 'margin', $margin );
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Fillable
     * @see \Altamira\JsWriter\Ability\Fillable::setFill()
     * @param \Altamira\Chart|series $series
     * @param array $opts
     * @return \Altamira\JsWriter\Flot
     */
    public function setFill($series, $opts = array('use'    => true,
                                                   'stroke' => false,
                                                   'color'  => null,
                                                   'alpha'  => null
                                                  ))
    {

        // @todo add a method of telling flot whether the series is a line, bar, point
        if ( isset( $opts['use'] ) && $opts['use'] == true ) {
            $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'line', 'fill', true );
            
            if ( isset( $opts['color'] ) ) {
                $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'line', 'fillColor', $opts['color'] );
            }
        }

        return $this;
    }
    
    /**
     * Implemented from \Altamira\JsWriter\Ability\Shadowable
     * @see \Altamira\JsWriter\Ability\Shadowable::setShadow()
     * @param \Altamira\Series|string $series
     * @param array $opts
     * @return \Altamira\JsWriter\Flot
     */
    public function setShadow($series, $opts = array('use'    => true,
                                                     'angle'  => 45,
                                                     'offset' => 1.25,
                                                     'depth'  => 3,
                                                     'alpha'  => 0.1) )
    {
        
        if (! empty( $opts['use'] ) ) {
            $depth = ! empty( $opts['depth'] ) ? $opts['depth'] : 3;
            $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'shadowSize', $depth );
        }

        return $this;
    }
    
    /**
     * Initializes default settings for using labels
     * @see \Altamira\JsWriter\Ability\Labelable::useSeriesLabels()
     * @param string|\Altamira\Series $series
     * @return \Altamira\JsWriter\Flot
     */
    public function useSeriesLabels( $series )
    {
        $this->useLabels = true;
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'pointLabels', 'edgeTolerance', 3 );
    }
    
    /**
     * Sets label setting option values
     * NOTE: FLOT DOES NOT SUPPORT SERIES-SPECIFIC LABEL SETTINGS
     * The options you set here are global label settings.
     * @see \Altamira\JsWriter\Ability\Labelable::setSeriesLabelSetting()
     * @param string $series
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\Flot
     */
    public function setSeriesLabelSetting( $series, $name, $value )
    {
        // jqplot supports this, but we're just going to do global settings. overwrite at your own peril.
        $this->labelSettings[$name] = $value;
        return $this;
    }
    
    /**
     * Determines the width of the line we will show, if we're showing it
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesLineWidth()
     * @param string $series
     * @param mixed $value
     * @return \Altamira\JsWriter\Flot
     */
    public function setSeriesLineWidth( $series, $value )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'lines', 'linewidth', $value );
    }
    
    /**
     * Determines whether we show the line for a series
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesShowLine()
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\Flot
     */
    public function setSeriesShowLine( $series, $bool )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'lines', 'show', $bool );
    }
    
    /**
     * Determines whether we show the marker for a series
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesShowMarker()
     * @param string|\Altamira\Series $series
     * @param bool $bool
     * @return \Altamira\JsWriter\Flot
     */
    public function setSeriesShowMarker( $series, $bool )
    {
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'points', 'show', $bool );
    }
    
    /**
     * Sets the style of the marker
     * @see \Altamira\JsWriter\Ability\Lineable::setSeriesMarkerStyle()
     * @param string|\Altamira\Series $series
     * @param string $value
     * @return \Altamira\JsWriter\Flot
     */
    public function setSeriesMarkerStyle( $series, $value )
    {
        // jqplot compatibility preprocessing
        $value = str_replace('filled', '', $value);
        $value = strtolower($value);

        if (! in_array('jquery.flot.symbol.js', $this->files)) {
            $this->files[] = 'jquery.flot.symbol.js';
        }
        
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'points', 'symbol', $value );
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
        return $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), 'points', 'radius', (int) ($value / 2) );
    }

    /**
     * Responsible for setting the tick labels on a given axis
     * @param string $axis
     * @param array $ticks
     * @return \Altamira\JsWriter\Flot
     */
    public function setAxisTicks($axis, array $ticks = array() )
    {
        if ( in_array($axis, array('x', 'y') ) ) {

            $isString = false;
            $alternateTicks = array();
            $cnt = 1;

            foreach ($ticks as $tick) {
                if (!(ctype_digit($tick) || is_int($tick))) {
                    $isString = true;
                    // this is O(2N) so deal with it
                    foreach ( $ticks as $tick ) {
                        $alternateTicks[] = array($cnt++, $tick);
                    }
                    break;
                }
            }
            
            $this->setNestedOptVal( $this->options, $axis.'axis', 'ticks', $isString ? $alternateTicks : $ticks );

        }

        return $this;
    }

    /**
     * Prepares default values for a series array
     * @param array $opts
     */
    public function prepOpts( &$opts = array() )
    {
        $opts = is_null( $opts ) ? array() : $opts ;
        if (   (!(isset($this->types['default']) && $this->types['default'] instanceOf \Altamira\Type\Flot\Bubble))
            && (!(isset($this->types['default']) && $this->types['default'] instanceOf \Altamira\Type\Flot\Bar))
                ) {
            if ( (! isset($this->options['seriesStorage']['points'])) && (!isset($opts['points']) || !isset($opts['points']['show'])) ) {
                // show points by default
                $this->setNestedOptVal( $opts, 'points', 'show', true );
            }
            
            if ( (! isset($this->options['seriesStorage']['lines'])) && (!isset($opts['lines']) || !isset($opts['lines']['show'])) ) {
                // show lines by default
                $this->setNestedOptVal( $opts, 'lines', 'show', true );
            }
        }
    }

    /**
     * maps jqplot-originating option data structure to flot
     * @var array
     */
    private $optsMapper = array('axes.xaxis.tickInterval' => 'xaxis.tickSize',
                                'axes.xaxis.min'          => 'xaxis.min',
                                'axes.xaxis.max'          => 'xaxis.max',
                                'axes.xaxis.mode'         => 'xaxis.mode',
                                'axes.xaxis.ticks'        => 'xaxis.ticks',

                                'axes.yaxis.tickInterval' => 'yaxis.tickSize',
                                'axes.yaxis.min'          => 'yaxis.min',
                                'axes.yaxis.max'          => 'yaxis.max',
                                'axes.yaxis.mode'         => 'yaxis.mode',
                                'axes.yaxis.ticks'        => 'yaxis.ticks',

                                'legend.show'             => 'legend.show',
                                'legend.location'         => 'legend.position',
                                'seriesColors'            => 'colors',
                                );


    /**
     * api-native functionality
     * @var array
     */
    private $nativeOpts = array('legend' => array(  'show'=>null,
                                                    'labelFormatter'=>null,
                                                    'labelBoxBorderColor'=>null,
                                                    'noColumns'=>null,
                                                    'position'=>null,
                                                    'margin'=>null,
                                                    'backgroundColor'=>null,
                                                    'backgroundOpacity'=>null,
                                                    'container'=>null),

                                'xaxis' => array(   'show'=>null,
                                                    'position'=>null,
                                                    'mode'=>null,
                                                    'color'=>null,
                                                    'tickColor'=>null,
                                                    'min'=>null,
                                                    'max'=>null,
                                                    'autoscaleMargin'=>null,
                                                    'transform'=>null,
                                                    'inverseTransform'=>null,
                                                    'ticks'=>null,
                                                    'tickSize'=>null,
                                                    'minTickSize'=>null,
                                                    'tickFormatter'=>null,
                                                    'tickDecimals'=>null,
                                                    'labelWidth'=>null,
                                                    'labelHeight'=>null,
                                                    'reserveSpace'=>null,
                                                    'tickLength'=>null,
                                                    'alignTicksWithAxis'=>null,
                                                ),

                                'yaxis' => array(   'show'=>null,
                                                    'position'=>null,
                                                    'mode'=>null,
                                                    'color'=>null,
                                                    'tickColor'=>null,
                                                    'min'=>null,
                                                    'max'=>null,
                                                    'autoscaleMargin'=>null,
                                                    'transform'=>null,
                                                    'inverseTransform'=>null,
                                                    'ticks'=>null,
                                                    'tickSize'=>null,
                                                    'minTickSize'=>null,
                                                    'tickFormatter'=>null,
                                                    'tickDecimals'=>null,
                                                    'labelWidth'=>null,
                                                    'labelHeight'=>null,
                                                    'reserveSpace'=>null,
                                                    'tickLength'=>null,
                                                    'alignTicksWithAxis'=>null,
                                                ),

                                 'xaxes' => null,
                                 'yaxes' => null,

                                 'series' => array(
                                                    'lines' => array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                    'points'=> array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                    'bars' => array('show'=>null, 'lineWidth'=>null, 'fill'=>null, 'fillColor'=>null),
                                                  ),

                                 'points' => array('radius'=>null, 'symbol'=>null),

                                 'bars' => array('barWidth'=>null, 'align'=>null, 'horizontal'=>null),

                                 'lines' => array('steps'=>null),

                                 'shadowSize' => null,

                                 'colors' => null,

                                 'grid' =>  array(  'show'=>null,
                                                    'aboveData'=>null,
                                                    'color'=>null,
                                                    'backgroundColor'=>null,
                                                    'labelMargin'=>null,
                                                    'axisMargin'=>null,
                                                    'markings'=>null,
                                                    'borderWidth'=>null,
                                                    'borderColor'=>null,
                                                    'minBorderMargin'=>null,
                                                    'clickable'=>null,
                                                    'hoverable'=>null,
                                                    'autoHighlight'=>null,
                                                    'mouseActiveRadius'=>null
                                                )


                                );

    /**
     * This is the immutable string component of the zooming function 
     * we have designed for Flot. It's intended to be passed to sprintf. 
     * @var string
     */
    const ZOOMING_FUNCTION = <<<ENDSCRIPT
placeholder.bind("plotselected", function (event, ranges) {
    jQuery.plot(placeholder, %s,
      $.extend(true, {}%s, {
      xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
      yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
  }));
});
placeholder.on('dblclick', function(){ plot.clearSelection(); jQuery.plot(placeholder, %s%s); });
ENDSCRIPT;
    
    /**
     * This is the immutable string component of the labeling function 
     * we have designed for Flot. It's intended to be passed to sprintf. 
     * @var string
     */
    const LABELS_FUNCTION = <<<ENDJS
var pointLabels = %s;

$.each(plot.getData()[0].data, function(i, el){
    var o = plot.pointOffset({
        x: el[0], y: el[1]});
        $('<div class="data-point-label">' + pointLabels[el[0] + ',' + el[1]] + '</div>').css( {
            position: 'absolute',
            left: o.left%s%s,
            top: o.top-5%s%s,
            display: 'none',
            'font-size': '10px'
        }).appendTo(plot.getPlaceholder()).fadeIn('slow');
});
ENDJS;
    
    /**
     * This is the immutable string component of the highlighting function 
     * we have designed for Flot. It's intended to be passed to sprintf. 
     * @var string
     */
    const HIGHLIGHTING_FUNCTION = <<<ENDJS

function showTooltip(x, y, contents) {
    $('<div id="flottooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5,
        border: '1px solid #fdd',
        padding: '2px',
        'background-color': '#fee',
        opacity: 0.80
    }).appendTo("body").fadeIn(200);
}

var previousPoint = null;

placeholder.bind("plothover", function (event, pos, item) {
    if (item) {
        if (previousPoint != item.dataIndex) {
            previousPoint = item.dataIndex;

            $("#flottooltip").remove();
            var x = item.datapoint[0].toFixed(2),
                y = item.datapoint[1].toFixed(2);

            showTooltip(item.pageX, item.pageY,
                        %s);
        }
    }
    else {
        $("#flottooltip").remove();
        previousPoint = null;
    }
});
ENDJS;
    
    /**
     * This is the string value of actual "plot" call to Flot,
     * intended to be passed to sprintf
     * @var string
     */
    const SCRIPT_OUTPUT = <<<ENDSCRIPT
jQuery(document).ready(function() {
    var placeholder = jQuery('#%s');
    var plot = jQuery.plot(placeholder, %s%s);
    %s
});

ENDSCRIPT;
    
   
}