<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    protected $options = array('series'=>array());
    protected $files = array();
    protected $callbacks = array();
    protected $seriesLabels = array();
    protected $series = array();
    protected $types = array();
    protected $library;
    protected $typeNamespace;
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function getScript()
    {
        $options = array_merge($this->chart->getOptions(), $this->options);
        $this->options = $this->getTypeOptions($this->getSeriesOptions($options));
        
        return $this->generateScript();
    }
    
    public function makeJSArray($array)
    {
        $options = json_encode($array);
        $optionString = preg_replace('/"#(.*?)#"/', '$1', $options);
        
        foreach ( $this->callbacks as $placeHolder => $callback ) {
            $optionString = str_replace("\"{$placeHolder}\"", $callback, $optionString);
        }
        
        return $optionString;
    }
    
    protected function getCallbackPlaceholder( $callback )
    {
        $index = count($this->callbacks);
        $uid = spl_object_hash( $this );
        
        $key = sprintf('%s_%s', $uid, $index);
        
        $this->callbacks[$key] = $callback;
        
        return $key; 
    }
    
    public function getFiles()
    {
        $files = $this->files;
        
        foreach ($this->types as $type) {
            $files = array_merge($files, $type->getFiles());
        }
        
        return $files;
    }
    
    public function getOptionsForSeries($series)
    {
        if ($series instanceOf \Altamira\Series) {
            return $this->options['series'][$series->getTitle()];
        } else if (is_string($series)) {
            return $this->options['series'][$series];
        }
    }
    
    public function getSeriesOption($series, $option)
    {
        if ($series instanceOf \Altamira\Series) {
            return $this->options['series'][$series->getTitle()][$option];
        } else if (is_string($series)) {
            return $this->options['series'][$series][$option];
        }
    }
    
    public function setSeriesOption($series, $name, $value)
    {
        if ($series instanceOf \Altamira\Series) {
            $this->options['series'][$series->getTitle()][$name] = $value;
        } else if (is_string($series)) {
            $this->options['series'][$series][$name] = $value;
        }
        
        return $this;
    }
    
    public function initializeSeries( \Altamira\Series $series )
    {
        $this->options['seriesStorage'][$series->getTitle()] = array();
        $this->series[] = $series;
    }
    
    public function getLibrary()
    {
        if (!$this->library) {
            throw new \Exception("You must set a library when creating a new jsWriter");
        }
        return $this->library;
    }
    
    public function setType( $type, $series = null )
    {
        if ( $series instanceOf \Altamira\Series ) {
            $series = $series->getTitle();
        }
        
        $title = $series ?: 'default';
    
        $className =  $this->typeNamespace . ucwords($type);
        if(class_exists($className)) {
            $this->types[$title] = new $className($this);
        }
        
        return $this;
    }
    
    public function setTypeOption( $name, $option, $series=null )
    {
        if ( $series instanceOf \Altamira\Series ) {
            $series = $series->getTitle();
        }
        
        $title = $series ?: 'default';
        
        if(isset($this->types[$title])) {
            $this->types[$title]->setOption($name, $option);
        }
        
        return $this;
    }
    
    abstract protected function getSeriesOptions(array $options);
    abstract protected function getTypeOptions(array $options);
    abstract protected function generateScript();
}