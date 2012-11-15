<?php 

namespace Altamira;

class ChartIterator extends \ArrayIterator
{
    /**
     * Stores the plugins in an iterator that knows how to render them
     * @var \Altamira\FilesRenderer
     */
    protected $plugins;
    /**
     * Stores inline script calls in an iterator that knows how to render them
     * @var \Altamira\ScriptsRenderer
     */
    protected $scripts;
    /**
     * Stores global configurations as set in altamira-config.ini
     * @var \Altamira\Config
     */
    protected $config;
    /**
     * The key is the library name; the value is immaterial. This is done to ensure uniqueness.
     * @var array
     */
    protected $libraries;
    
    /**
     * Constructor method
     * @param array $charts array of \Altamira\Chart instances
     * @param \Altamira\Config $config
     * @throws \Exception
     */
    public function __construct( array $charts, \Altamira\Config $config )
    {
        $this->config = $config;
        
        //enforce that this is an array of charts
        $plugins = array();
        $scripts = array();
        
        foreach ($charts as $chart) {
            if (! $chart instanceof Chart ) {
                throw new \UnexpectedValueException("ChartIterator only supports an array of Chart instances.");
            }
            
            // time saver -- if it's a chart, we can use this loop to add files, too
            $plugins = array_merge( $plugins, $chart->getFiles() );
            $scripts[] = $chart->getScript();
            $this->libraries[$chart->getLibrary()] = true;
        }

        $this->plugins = new FilesRenderer( $plugins, $config['js.pluginpath'] );
        $this->scripts = new ScriptsRenderer( $scripts );
        
        
        parent::__construct( $charts );        
    }
    
    
    /**
     * The following render methods are helpers that allow us to group JS easier.
     * We don't handle chart HTML this way since placement and context is a front-end concern.  
     */
    
    /**
     * Echoes out script tags referencing JS files
     * @return \Altamira\ChartIterator provides fluent interface
     */
    public function renderPlugins()
    {
        
        while ( $this->plugins->valid() ) {

            $this->plugins->render()
                          ->next();
            
        }
        
        return $this;
        
    }
    
    /**
     * Returns an array of plugins, if we don't want to echo
     * @return array
     */
    public function getPlugins()
    {
        return (array) $this->plugins;
    }
    
    /**
     * Echoes out inline javascript. Note that we group it all together in a single script tag.
     * @return \Altamira\ChartIterator provides fluent interface
     */
    public function renderScripts()
    {
        echo "<script type='text/javascript'>\n";
        while ( $this->scripts->valid() ) {
            
            $this->scripts->render()
                          ->next();
            
        }
        echo "\n</script>\n";
        
        return $this;
        
    }
    
    /**
     * Returns inline js, in case you don't want to immediately render.
     * @return string
     */
    public function getScripts()
    {
        $retVal = '';
        while ( $this->scripts->valid() ) {
            $retVal .= "<script type='text/javascript'>\n{$this->scripts->get()}\n</script>\n";
            $this->scripts->next();
        }
        
        return $retVal;
    }
    
    /**
     * Provides libraries paths based on config values
     * @return array
     */
    public function getLibraries()
    {
        $libraryToPath = array(
                \Altamira\JsWriter\Flot::LIBRARY    =>    $this->config['js.flotpath'],
                \Altamira\JsWriter\JqPlot::LIBRARY  =>    $this->config['js.jqplotpath']
                );
        $libraryKeys = array_unique( array_keys( $this->libraries ) );
        $libraryPaths = array();
        
        foreach ($libraryKeys as $key) {
            $libraryPaths[] = $libraryToPath[$key];
        }
        
        return $libraryPaths;
    }
    
    /**
     * Echoes library plugins for invoking in the DOM head
     * @return \Altamira\ChartIterator provides fluent interface
     */
    public function renderLibraries()
    {
        foreach ( $this->getLibraries() as $libraryPath ) {
            echo "<script type='text/javascript' src='$libraryPath'></script>";
        }
        
        return $this;
    }
    
    /**
     * Echoes any CSS that is needed for the libraries to render correctly
     * @return \Altamira\ChartIterator provides fluent interface
     */
    public function renderCss()
    {
        foreach ( $this->libraries as $library => $junk ) {
            switch( $library ) {
                case \Altamira\JsWriter\Flot::LIBRARY:
                    break;
                case \Altamira\JsWriter\JqPlot::LIBRARY:
                default:
                    $cssPath = $this->config['css.jqplotpath'];
            }
        
        }
        
        if ( isset( $cssPath ) ) {
            echo "<link rel='stylesheet' type='text/css' href='{$cssPath}'></link>";
        }
        
        return $this;
        
    }
    
    public function getCSSPath()
    {
        $cssPath = '';
        
        foreach ( $this->libraries as $library => $junk ) {
            switch( $library ) {
                case \Altamira\JsWriter\Flot::LIBRARY:
                    break;
                case \Altamira\JsWriter\JqPlot::LIBRARY:
                default:
                    $cssPath = $this->config['css.jqplotpath'];
            }
        }
        
        return $cssPath;
    }
    
}