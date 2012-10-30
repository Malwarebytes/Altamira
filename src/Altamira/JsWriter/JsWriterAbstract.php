<?php 

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    protected $chart;
    
    protected $options = array('seriesStorage'=>array());
    protected $files = array();
    protected $callbacks = array();
    protected $seriesLabels = array();
    protected $types = array();
    protected $library;
    protected $typeNamespace;
    protected $useLabels = false;
    
    public function __construct(\Altamira\Chart $chart)
    {
        $this->chart = $chart;
    }
    
    public function getScript()
    {
        $this->options = $this->getTypeOptions( $this->getSeriesOptions( $this->options ) );
        
        return $this->generateScript();
    }
    
    public function makeJSArray($array)
    {
        $optionString = preg_replace('/"#([^#":]*)#"/U', '$1', json_encode($array) );
        
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
            return $this->options['seriesStorage'][$series->getTitle()];
        } else if (is_string($series)) {
            return $this->options['seriesStorage'][$series];
        }
    }
    
    public function getSeriesOption($series, $option)
    {
        if ($series instanceOf \Altamira\Series) {
            return isset($this->options['seriesStorage'][$series->getTitle()]) && isset($this->options['seriesStorage'][$series->getTitle()][$option]) 
                    ? $this->options['seriesStorage'][$series->getTitle()][$option]
                    : null;
        } else if (is_string($series)) {
            return $this->options['seriesStorage'][$series][$option];
        }
    }
    
    public function setSeriesOption( $seriesTitle, $name, $value)
    {
        $this->options['seriesStorage'][$seriesTitle][$name] = $value;
        
        return $this;
    }
    
    public function setOption( $key, $value )
    {
        $this->options[$key] = $value;
        
        return $this;
    }
    
    public function getOption( $key, $default = null )
    {
        return isset( $this->options[$key] ) ? $this->options[$key] : $default;
    }
    
    public function initializeSeries( \Altamira\Series $series )
    {
        $this->options['seriesStorage'][$series->getTitle()] = array();
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
    
    public function getType( $key = 'default' )
    {
        if ( isset( $this->types[$key] ) ) {
            return $this->types[$key];
        } else if ( isset( $this->types['default'] ) ) {
            return $this->types['default'];
        } 
        return null;
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