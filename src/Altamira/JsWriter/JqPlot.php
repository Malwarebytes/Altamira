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
    
    protected $library = 'jqplot';
    protected $typeNamespace = '\\Altamira\\Type\\JqPlot\\';
    
    public function generateScript()
    {
        $output  = '$(document).ready(function(){';
        $output .= '$.jqplot.config.enablePlugins = true;';
        
        $num = 0;
        $vars = array();
        
        $useTags = (isset($this->types['default']) 
                 && $this->types['default']->getUseTags()) 
                 || ($this->chart->getUseTags());
        
        
        foreach($this->chart->getSeries() as $series) {
            $num++;
            $data = $series->getData($useTags);
            $title = $series->getTitle();
            
            if (isset($this->seriesLabels[$title]) 
                && !empty($this->seriesLabels[$title])) {
                $labelCopy = $this->seriesLabels[$title];
                foreach ($data as &$datum) {
                    $datum[] = array_shift($labelCopy);
                }
                
            }

            $varname = 'plot_' . $this->chart->getName() . '_' . $num;
            $vars[] = '#' . $varname . '#';
            $output .= $varname . ' = ' . $this->makeJSArray($data) . ';';
        }
        
        $output .= 'plot = $.jqplot("' . $this->chart->getName() . '", ';
        $output .= $this->makeJSArray($vars);
        $output .= ', ';
        $output .= $this->getOptionsJS();
        $output .= ');';
        $output .= '});';
        
        return $output;
        
    }
    
    public function useHighlighting(array $opts = array('size'=>7.5))
    {
        extract($opts);
        $size = isset($size) ? $size : 7.5;
        
        $this->files = array_merge_recursive(array('jqplot.highlighter.min.js'), $this->files);
        $this->options['highlighter'] = array('sizeAdjust' => $size);

        return $this;
    }
    
    public function useZooming(array $options = array('mode'=>'xy'))
    {
        $this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
        $this->options['cursor'] = array('zoom' => true, 'show' => true);
    
        return $this;
    }
    
    public function useCursor()
    {
        $this->files = array_merge_recursive(array('jqplot.cursor.min.js'), $this->files);
        $this->options['cursor'] = array('show' => true, 'showTooltip' => true);
        
        return $this;
    }
    
    public function useDates($axis = 'x')
    {
        $this->files = array_merge_recursive(array('jqplot.dateAxisRenderer.min.js'), $this->files);
        if(strtolower($axis) === 'x') {
            $this->options['axes']['xaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
        } elseif(strtolower($axis) === 'y') {
            $this->options['axes']['yaxis']['renderer'] = '#$.jqplot.DateAxisRenderer#';
        }
    
        return $this;
    }
    
    public function setShadow($series, $opts = array('use'=>true, 
                                                     'angle'=>45, 
                                                     'offset'=>1.25, 
                                                     'depth'=>3, 
                                                     'alpha'=>0.1))
    {
        extract($opts);
        
        $use = isset($use) ? $use : true;
        $angle = isset($angle) ? $angle : 45;
        $offset = isset($offset) ? $offset : 1.25; 
        $depth = isset($depth) ? $depth : 3; 
        $alpha = isset($alpha) ? $alpha : 0.1;
        
        $this->options['seriesStorage'][$series]['shadow'] = $use;
		$this->options['seriesStorage'][$series]['shadowAngle'] = $angle;
		$this->options['seriesStorage'][$series]['shadowOffset'] = $offset;
		$this->options['seriesStorage'][$series]['shadowDepth'] = $depth;
		$this->options['seriesStorage'][$series]['shadowAlpha'] = $alpha;
		
		return $this;
    }
    
    public function setFill($series, $opts = array('use' => true, 
                                                   'stroke' => false, 
                                                   'color' => null, 
                                                   'alpha' => null
                                                  )
                            ) 
    {
        extract($opts);
        
        $use = isset($use) ? $use : true;
        $stroke = isset($stroke) ? $stroke : false;
        $color = isset($color) ? $color : null;
        $alpha = isset($alpha) ? $alpha : null;
        
        $this->options['seriesStorage'][$series]['fill'] = $use;
        $this->options['seriesStorage'][$series]['fillAndStroke'] = $stroke;
        
        if($color !== null) {
            $this->options['seriesStorage'][$series]['fillColor'] = $color;
        }
        if($alpha !== null) {
            $this->options['seriesStorage'][$series]['fillAlpha'] = $alpha;
        }
        
        return $this;
    }
    
    public function setGrid(array $opts)
    {
        extract($opts);
        $on = isset($on) ? $on : true; 
        $color = isset($color) ? $color : null;
        $background = isset($background) ? $background : null;
        
        $this->options['grid']['drawGridLines'] = $on;
        if($color !== null) {
            $this->options['grid']['gridLineColor'] = $color;
        }
        if($background !== null) {
            $this->options['grid']['background'] = $background;
        }
    
        return $this;
    }
    
    public function setLegend(array $opts = array('on' => 'true', 
                                                  'location' => 'ne', 
                                                  'x' => 0, 
                                                  'y' => 0))
    {
        extract($opts);
        
        $on = isset($on) ? $on : 'true';
        $location = isset($location) ? $location : 'ne';
        $x = isset($x) ? $x : 0;
        $y = isset($y) ? $y : 0;
        
        
        if(!$on) {
            unset($this->options['legend']);
        } else {
            $legend = array();
            $legend['show'] = true;
            if($location == 'outside' || $location == 'outsideGrid') {
                $legend['placement'] = $location;
            } else {
                $legend['location'] = $location;
            }
            if($x != 0)
                $legend['xoffset'] = $x;
            if($y != 0)
                $legend['yoffset'] = $y;
            $this->options['legend'] = $legend;
        }
    
        return $this;
    }
    
    public function setAxisOptions($axis, $name, $value)
    {
        if(strtolower($axis) === 'x' || strtolower($axis) === 'y') {
            $axis = strtolower($axis) . 'axis';
        
            if (in_array($name, array('min', 'max', 'numberTicks', 'tickInterval', 'numberTicks'))) {
                $this->options['axes'][$axis][$name] = $value;
            } elseif(in_array($name, array('showGridline', 'formatString'))) {
                $this->options['axes'][$axis]['tickOptions'][$name] = $value;
            }
        }
        
        return $this;
    }
    
    protected function getTypeOptions(array $options)
    {
        if(isset($this->types['default'])) {
            $options = array_merge_recursive($options, $this->types['default']->getOptions());
        }
        
        if(isset($options['axes'])) {
            foreach($options['axes'] as $axis => $contents) {
                if(isset($options['axes'][$axis]['renderer']) && is_array($options['axes'][$axis]['renderer'])) {
                    $options['axes'][$axis]['renderer'] = $options['axes'][$axis]['renderer'][0];
                }
            }
        }
        
        return $options;
    }
    
    protected function getSeriesOptions(array $options)
    {
        $types = $this->types;
        if(isset($types['default'])) {
            $defaults = $options['seriesDefaults'];
            $renderer = $types['default']->getRenderer();
            if(isset($renderer))
                $defaults['renderer'] = $renderer;
            $defaults['rendererOptions'] = $types['default']->getRendererOptions();
            if(count($defaults['rendererOptions']) == 0)
                unset($defaults['rendererOptions']);
            $options['seriesDefaults'] = $defaults;
        }
        
        $seriesOptions = array();
        foreach($this->options['seriesStorage'] as $title => $opts) {
            if(isset($types[$title])) {
                $type = $types[$title];
                $opts['renderer'] = $type->getRenderer();
                array_merge_recursive($opts, $type->getSeriesOptions());
            }
            $opts['label'] = $title;
            
            $seriesOptions[] = $opts;
        }
        $options['seriesStorage'] = $seriesOptions;
        
        return $options;
    }
    
    public function getOptionsJS()
    {
        $opts = $this->options;
        $opts['series'] = $opts['seriesStorage'];
        unset($opts['series']);
        
        return $this->makeJSArray($opts);
    }
    
    public function setSeriesOption( \Altamira\Series $series, $name, $value)
    {
        $this->options['seriesStorage'][$series->getTitle()][$name] = $value;
        
        return $this;
    }
    
    public function useSeriesLabels( \Altamira\Series $series, array $labels = array() )
    {
        $this->seriesLabels[$series->getTitle()] = $labels;
        $this->options['seriesStorage'][$series->getTitle()]['pointLabels']['show'] = true;
        
        if (!in_array('jqplot.pointLabels.min.js', $this->files)) {
            $this->files[] = 'jqplot.pointLabels.min.js';
        }
        
        return $this;
    }
    
    public function setSeriesLabelSetting( \Altamira\Series $series, $name, $value )
    {
        if($name === 'location' && in_array($value, array('n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'))) {
            $this->setSeriesOption($series, 'pointLabels', (($a = $this->getSeriesOption($series, 'pointLabels')) && is_array($a) ? $a : array()) + array('location'=>$value));
        } elseif(in_array($name, array('xpadding', 'ypadding', 'edgeTolerance', 'stackValue'))) {
            $this->setSeriesOption($series, 'pointLabels', (($a = $this->getSeriesOption($series, 'pointLabels')) && is_array($a) ? $a : array()) + array($name=>$value));
        }
        
        return $this;
    }
    
    public function setSeriesLineWidth( \Altamira\Series $series, $value )
    {
        $this->options['seriesStorage'][$series->getTitle()]['lineWidth'] = $value;
        return $this;
    }
    
    public function setSeriesShowLine( \Altamira\Series $series, $bool )
    {
        $this->options['seriesStorage'][$series->getTitle()]['showLine'] = $bool;
        return $this;
    }
    
    public function setSeriesShowMarker( \Altamira\Series $series, $bool )
    {
        $this->options['seriesStorage'][$series->getTitle()]['showMarker'] = $bool;
        return $this;
    }
    
    public function setSeriesMarkerStyle( \Altamira\Series $series, $value )
    {
        $this->options['seriesStorage'][$series->getTitle()]['markerOptions'] = ( isset($this->options['seriesStorage'][$series->getTitle()]['markerOptions'])
                                                                       ? $this->options['seriesStorage'][$series->getTitle()]['markerOptions']
                                                                       : array() )
                                                                       + array('style'=>$value);
        return $this;
    }
    
    public function setSeriesMarkerSize( \Altamira\Series $series, $value )
    {
        $this->options['seriesStorage'][$series->getTitle()]['markerOptions'] = ( isset($this->options['seriesStorage'][$series->getTitle()]['markerOptions'])
                ? $this->options['seriesStorage'][$series->getTitle()]['markerOptions']
                : array() )
                + array('size'=>$value);
        return $this;
    }
    
    public function setAxisTicks($axis, $ticks)
    {

        if(strtolower($axis) === 'x') {
            $this->options['axes']['xaxis']['ticks'] = $ticks;
        } elseif(strtolower($axis) === 'y') {
            $this->options['axes']['yaxis']['ticks'] = $ticks;
        }
        
        return $this;
    }
    
}