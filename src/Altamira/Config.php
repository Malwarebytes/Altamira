<?php 

namespace Altamira;

/**
 * @codeCoverageIgnore
 */
class Config implements \ArrayAccess
{
    /**
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
    
	/* (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists ($offset)
    {
        return isset( $this->config[$offset] );
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet ($offset)
    {
        return $this->offsetExists($offset) ? $this->config[$offset] : null;
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet ($offset, $value)
    {
        $this->config[$offset] = $value;
    }

	/* (non-PHPdoc)
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset ($offset)
    {
        unset($this->config[$offset]);
    }

    
    
}