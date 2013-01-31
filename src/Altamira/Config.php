<?php 
/**
 * Class definition for \Altamira\Config
 * @author relwell
 *
 */
namespace Altamira;

/**
 * Configuration class for global state and environmental dependencies
 * @codeCoverageIgnore
 */
class Config implements \ArrayAccess
{
    /**
     * Contains the configuration values stored within the config
     * @var array
     */
    protected $config = array();
    
    /**
     * Singleton
     * @var \Altamira\Config
     */
    protected static $instance;
    
    /**
     * Location of the config file
     * @var string
     */
    protected static $file;
    
    /**
     * Used to instantiate the actual config from the ini file
     * @param unknown_type $file
     */
    protected function __construct( $file = null )
    {
        if ( !empty( $file ) && file_exists( $file ) ) {
            $this->config = parse_ini_file( $file, true );
        }
        
        $this['altamira.root'] = __DIR__ . '/';
    }
    
    /**
     * Singleton method
     * @return \Altamira\Config
     */
    public static function getInstance()
    {
        if ( self::$instance === null ) {
            self::$instance = new Config( self::$file );
        }
        
        return self::$instance;
    }
    
    /**
     * Provided a library, returns a path for plugins
     * @param string $library
     * @return string
     */
    public function getPluginPath( $library )
    {
        switch ( $library ) {
            case \Altamira\JsWriter\Flot::LIBRARY:
                return $this['js.flotpluginpath'];
            case \Altamira\JsWriter\JqPlot::LIBRARY:
                return $this['js.jqplotpluginpath'];
            case \Altamira\JsWriter\D3::LIBRARY:
                return $this['js.d3pluginpath'];
        }
    }
    
    /**
     * Determines whether or not we should minify all JS 
     * files in the FilesRenderer. Controlled by config value 'js.minify'.
     */
    public static function minifyJs()
    {
        $instance = self::getInstance();
        return isset( $instance['js.minify'] ) && $instance['js.minify'];
    }
    
    /**
     * Accessible path to config file
     * @param unknown_type $file
     */
    public static function setConfigFile( $file )
    {
        self::$file = $file;
    }
    
	/**
	 * Determine if there is a value for this key in the config
     * @see ArrayAccess::offsetExists()
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset)
    {
        return isset( $this->config[$offset] );
    }

	/**
	 * Retrieve the config value for the provided key
     * @see ArrayAccess::offsetGet()
     * @param string $offset
     * @return mixed|null
     */
    public function offsetGet ($offset)
    {
        return $this->offsetExists($offset) ? $this->config[$offset] : null;
    }

	/**
	 * Stores the value for the given key
     * @see ArrayAccess::offsetSet()
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet ($offset, $value)
    {
        $this->config[$offset] = $value;
    }

	/**
	 * Removes the provided key-value pair from the config array
     * @see ArrayAccess::offsetUnset()
     * @param string $offset
     */
    public function offsetUnset ($offset)
    {
        unset($this->config[$offset]);
    }
}