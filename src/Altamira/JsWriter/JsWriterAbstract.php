<?php

namespace Altamira\JsWriter;

abstract class JsWriterAbstract
{
    /**
     * The chart instance this JsWriter interacts with. Each JsWriter is responsible for a single chart.
     * @var \Altamira\Chart
     */
    protected $chart;
    
    /**
     * Global and chart-specific options. Stored here to make it easier to json-encode.
     * @var array
     */
    protected $options = array( 'seriesStorage' => array() );
    
    /**
     * Stores files that are required to properly render the registered chart
     * @var array
     */
    protected $files = array();
    
    /**
     * Stores JavaScript callbacks required to render the registered chart
     * @var array
     */
    protected $callbacks = array();
    
    /**
     * Registry for types that may require different rendering. 
     * A type registered as 'default' will work for all series that don't have a series registered. 
     * @var array
     */
    protected $types = array();
    
    /**
     * String name of JsWriter library. Required when writing a new class.
     * @var string
     */
    protected $library;
    
    /**
     * The namespace where type classes are stored for the concrete class
     * @var string
     */
    protected $typeNamespace;
    
    /**
     * A flag for whether to use labels in this JsWriter.
     * Doesn't matter much unless you implement Labelable
     * @var boolean
     */
    protected $useLabels = false;
    
    /**
     * Constructor method. Requires the chart to be rendered. Every chart has its own JsWriter instance.
     * @param \Altamira\Chart $chart
     */
    public function __construct( \Altamira\Chart $chart )
    {
        $this->chart = $chart;
    }
    
    /**
     * JSON-encoding with some additional treatments
     * -- anything wrapped in hashes will be treated as bare in js (not wrapped in quotes, for example)
     * -- callbacks are introduced where they are stored after json-encoding, so that they are evaluated
     * @param array $array
     * @return string
     */
    public function makeJSArray( $array )
    {
        $optionString = preg_replace('/"#([^#":]*)#"/U', '$1', json_encode( $array ) );
        
        foreach ( $this->callbacks as $placeHolder => $callback ) {
            $optionString = str_replace("\"{$placeHolder}\"", $callback, $optionString);
        }

        return $optionString;
    }
    
    /**
     * Registers a callback with the JsWriter and returns a key referencing it. Used for find/replace during JSification
     * @param unknown_type $callback
     * @return string
     */
    protected function getCallbackPlaceholder( $callback )
    {
        $index = count( $this->callbacks );
        $uid = spl_object_hash( $this );
        $key = sprintf( '%s_%s', $uid, $index );

        $this->callbacks[$key] = $callback;

        return $key;
    }
    
    /**
     * Returns an array of files required to render the chart 
     * Includes the default files, as well as any files registered by type
     * It's important to note that some methods in concrete classes will add additional files to this instance
     * @return array
     */
    public function getFiles()
    {
        $files = $this->files;

        foreach ($this->types as $type) {
            $files = array_merge($files, $type->getFiles());
        }
        
        $path = \Altamira\Config::getInstance()->getPluginPath( $this->getLibrary() );
        
        array_walk( $files, function( &$val ) use ( $path ) { $val = $path . $val; } ); 

        return $files;
    }
    
    /**
     * Returns the options for a series
     * @param \Altamira\Series|string $series
     * @throws \Exception
     * @return mixed
     */
    public function getOptionsForSeries( $series )
    {
        $seriesTitle = $this->getSeriesTitle( $series );
        if (! isset( $this->options['seriesStorage'][$seriesTitle] ) ) {
            throw new \Exception( 'Series not registered with JsWriter' );
        } 
        return $this->options['seriesStorage'][$seriesTitle];
    }
    
    /**
     * supports accessing series values by instance or title
     * @param string|\Altamira\Series $series
     * @return string
     */
    protected function getSeriesTitle( $series )
    {
        return ( $series instanceof \Altamira\Series ) ? $series->getTitle() : $series;
    }
    
    /**
     * Returns an option for a specific series. Accepts title or instance.
     * @param \Altamira\Series|string $series
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public function getSeriesOption( $series, $option, $default = null )
    {
        $seriesTitle = $this->getSeriesTitle( $series ); 

        return ( isset( $this->options['seriesStorage'][$seriesTitle] ) && isset( $this->options['seriesStorage'][$seriesTitle][$option] ) )
            ?  $this->options['seriesStorage'][$seriesTitle][$option]
            :  $default;
    }
    
    /**
     * Set a series-specific value for rendering JS
     * @param string|\Altamira\Series $series
     * @param string $name
     * @param mixed $value
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    public function setSeriesOption( $series, $name, $value )
    {
        $this->setNestedOptVal( $this->options, 'seriesStorage', $this->getSeriesTitle( $series ), $name, $value );
        return $this;
    }
    
    /**
     * Sets a global option for JS rendering
     * @param string $key
     * @param mixed $value
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    public function setOption( $key, $value )
    {
        $this->options[$key] = $value;
        
        return $this;
    }
    
    /**
     * Returns global options stored in the JsWriter instance. Allows for variable default value.
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption( $key, $default = null )
    {
        return isset( $this->options[$key] ) ? $this->options[$key] : $default;
    }
    
    /**
     * Initializes the storage location using the series title as a key
     * @param \Altamira\Series|string $series
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    public function initializeSeries( $series )
    {
        $this->options['seriesStorage'][$this->getSeriesTitle( $series )] = array();
        return $this;
    }
    
    /**
     * This is used to differentiate jsWriters of different kinds using a string name
     * @return string
     */
    public function getLibrary()
    {
        return static::LIBRARY;
    }
    
    /**
     * Returns an type that has not yet been registered. 
     * @param \Altamira\Type\TypeAbstract $type
     * @param array $options
     */
    public function setType( $type, $options = array(), $series = 'default' )
    {
        $options = $options ?: array(); // i shouldn't have to do this
        
        $className =  $this->typeNamespace . ucwords( $type );
     
        if( class_exists( $className ) ) {
            $type = new $className( $this );
        } else {
            throw new \Exception( "Type {$type} does not exist" );
        }
        
        $type->setOptions( $options );
        
        $series = $this->getSeriesTitle( $series );
        $this->types[$series] = $type;
        if ( isset( $this->options['seriesStorage'][$series] ) ) {
            $this->options['seriesStorage'][$series] = array_merge_recursive( $this->options['seriesStorage'][$series], $type->getSeriesOptions() );
            if ( $renderer = $type->getRenderer() ) {
                $this->options['seriesStorage'][$series]['renderer'] = $renderer;
            }
        }
        $this->options = array_merge_recursive( $this->options, $type->getOptions() );
        return $this;
    }
    
    /**
     * Returns the type instance for the provided key
     * @param string $key
     * @return multitype:|NULL
     */
    public function getType( $series = null )
    {
        $series = $series ?: 'default';
        $seriesTitle = $this->getSeriesTitle( $series );
        
        return isset( $this->types[$seriesTitle] ) ? $this->types[$seriesTitle] : null;
    }
    
    /**
     * Allows you to set discretely infinite nesting without notices 
     * by creating an empty array for key values that don't already exist
     * @param array $options
     * @param $_ ... 
     * @return \Altamira\JsWriter\JsWriterAbstract
     */
    protected function setNestedOptVal( array &$options )
    {
        //@codeCoverageIgnoreStart
        $args = func_get_args();
        
        if ( count( $args ) == 2 && is_array( $args[1] ) ) {
            $args = $args[1];
        } else if ( count( $args ) < 3 ) {
            throw new \BadMethodCallException( '\Altamira\JsWriterAbstract::setNestedOptVal requires at least three arguments' );
        } else {
            array_shift( $args );
        }
        
        do {
            $arg = array_shift( $args );
            
            if (! isset( $options[$arg] ) ) {
                $options[$arg] = array();
            }
            $options = &$options[$arg];
            
        } while ( count( $args ) > 2 );
        
        $options[array_shift( $args )] = array_shift( $args );
        
        return $this;
        //@codeCoverageIgnoreEnd
    }
    
    /**
     * Allows you to get the value for discretely infinite nesting without notices 
     * by returning null without a warning if it doesn't exist
     * @param array $options
     * @param $_ ... 
     * @return mixed
     */
    protected function getNestedOptVal( array $options )
    {
        //@codeCoverageIgnoreStart
        $args = func_get_args();
        
        if ( count( $args ) == 2 && is_array( $args[1] ) ) {
            $args = $args[1];
        } else if ( count( $args ) < 3 ) {
            throw new \BadMethodCallException( '\Altamira\JsWriterAbstract::getNestedOptVal requires at least three arguments' );
        } else {
            array_shift( $args );
        }
        
        do {
            $arg = array_shift( $args );
            
            if (! isset( $options[$arg] ) ) {
                return null;
            }
            $options = &$options[$arg];
            
        } while ( count( $args ) > 1 );
        
        $finalArg = array_shift( $args );
        return isset( $options[$finalArg] ) ? $options[$finalArg] : null;
        //@codeCoverageIgnoreEnd
    }

    /**
     * Responsible for generating JavaScript
     */
    abstract public function getScript();
}