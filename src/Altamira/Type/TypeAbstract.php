<?php
/**
 * Class definition for \Altamira\Type\TypeAbstract
 * @author relwell
 */
namespace Altamira\Type;
/**
 * Provides a common interface for registering types with charts or series
 * Implementations vary based on library.
 * Types encapsulate logic required to register special rendering cases for series or charts.
 * Registering a type with a series or a chart should cause that series or chart to render as such.
 * @author relwell
 * @package Type
 */
abstract class TypeAbstract
{
    /**
     * Lists any files that need to be linked in the head for this type to work
     * @var array
     */
	protected $pluginFiles = array();
	
	/**
	 * Identifies a specific renderer file (in JqPlot, renderers and pluginfiles are different)
	 * @var string
	 */
	protected $renderer;
	
	/**
	 * Used to configure how this type is rendered -- usually these options mean nothing outside of this type
	 * @var array
	 */
	protected $options = array();
	
	/**
	 * Identifies whether this type is specific to a given series or not
	 * @var \Altamira\Series
	 */
    protected $series;
	
    /**
     * Stored in TypeConfig.ini and configured on construct, 
     * allows us to whitelist specific options that work only for this type
     * @var array
     */
	protected $allowedRendererOptions = array();
	
	/**
	 * Constructor method
	 * @param \Altamira\JsWriter\JsWriterAbstract $jsWriter
	 */
	public function __construct( \Altamira\JsWriter\JsWriterAbstract $jsWriter )
	{
	    $this->configure( $jsWriter );
	}
	
	/**
	 * configures instance upon construct
	 * @param \Altamira\JsWriter\JsWriterAbstract $jsWriter
	 */
	protected function configure( \Altamira\JsWriter\JsWriterAbstract $jsWriter )
	{
	    $confInstance = \Altamira\Config::getInstance();
	    $file = $confInstance['altamira.root'] . $confInstance['altamira.typeconfigpath'];
	    $config = \parse_ini_file( $file, true );
	    
	    $libConfig = $config[strtolower( $jsWriter->getLibrary() )];
	    $type = static::TYPE;
        $typeAttributes = preg_grep( "/$type\./i", array_keys( $libConfig ) );
	    foreach ( $typeAttributes as $key ) {
	        $attribute = preg_replace( "/{$type}\./i", '', $key );
	        $this->{$attribute} = $libConfig[$key];
	    }
	}

	/**
	 * Returns any files registered for the type
	 * @return multitype:
	 */
	public function getFiles()
	{
		return $this->pluginFiles;
	}

	/**
	 * Returns the name of the renderer wrapped in hashes for parsing later
	 * @return string|NULL
	 */
	public function getRenderer()
	{
		if( isset( $this->renderer ) ) {
			return '#' . $this->renderer . '#';
		}
	}

	/**
	 * Accessor method for options array
	 * @return multitype:
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * Returns options as they specifically pertain to a series
	 * @return multitype:
	 */
	public function getSeriesOptions()
	{
		return isset( $this->options['series' ]) ? $this->options['series'] : array();
	}

	/**
	 * Return options specific to the renderer
	 * @return multitype:multitype:
	 */
	public function getRendererOptions()
	{
		$opts = array();
		foreach( $this->allowedRendererOptions as $opt ) {
			if( isset( $this->options[$opt] ) )
				$opts[$opt] = $this->options[$opt];
		}
		return $opts;
	}

	/**
	 * Sets the value of an option
	 * @param string $name
	 * @param string $value
	 * @return \Altamira\Type\TypeAbstract
	 */
	public function setOption($name, $value)
	{
		$this->options[$name] = $value;

		return $this;
	}
	
	/**
	 * Sets multiple options in an associative array
	 * @param array $options
	 * @return \Altamira\Type\TypeAbstract
	 */
	public function setOptions( $options = array() )
	{
	    // while more process-intensive, allows us to do some overriding later on
	    foreach ( $options as $key => $val )
	    {
	        $this->setOption( $key, $val );
	    }
	    
	    return $this;
	}
	
	/**
	 * returns the name of this instance
	 * @return string
	 */
	public function getName()
	{
	    $classname =  get_class( $this );
	    $exploded = explode( '\\', $classname );
	    return array_pop( $exploded );
	}
}