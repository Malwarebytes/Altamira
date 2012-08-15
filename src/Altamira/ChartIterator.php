<?php 

namespace Altamira;

class ChartIterator extends \ArrayIterator
{
    
    protected $plugins;
    protected $scripts;
    
    public function __construct( $array )
    {
        //enforce that this is an array of charts
        $plugins = array();
        $scripts = array();
        
        foreach ($array as $item) {
            if (! $item instanceOf Chart ) {
                throw new \Exception("ChartIterator only supports an array of Chart instances.");
            }
            
            // time saver -- if it's a chart, we can use this loop to add files, too
            $plugins = array_merge($plugins, $item->getFiles());
            $scripts[] = $item->getScript();
            $this->libraries[$item->getLibrary()] = true;
        }

        // yo dawg...
        $this->plugins = new FilesRenderer($plugins, 'js/plugins/');
        $this->scripts = new ScriptsRenderer($scripts);
        
        
        parent::__construct($array);        
    }
    
    
    /**
     * The following render methods are helpers that allow us to group JS easier.
     * We don't handle chart HTML this way since placement and context is a front-end concern.  
     */
    
    
    public function renderPlugins()
    {
        
        while ( $this->plugins->valid() ) {

            $this->plugins->render()
                          ->next();
            
        }
        
        return $this;
        
    }
    
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
    
    public function renderLibraries()
    {
        foreach ($this->libraries as $library=>$junk) {
            
            switch($library) {
                case 'flot':
                    $libraryPath = 'js/jquery.flot.js';
                    break;
                case 'jqPlot':
                default:
                    $libraryPath = 'js/jquery.jqplot.js';
            }
            
        }
        
        echo "<script type='text/javascript' src='$libraryPath'></script>";
        
        return $this;
    }
    
    public function renderCss()
    {
        foreach ($this->libraries as $library=>$junk) {
            switch($library) {
                case 'flot':
                    break;
                case 'jqPlot':
                default:
                    $cssPath = 'css/jqplot.css';
            }
        
        }
        
        if (isset($cssPath)) {
            echo "<link rel='stylesheet' type='text/css' href='{$cssPath}'></link>";
        }
        
        return $this;
        
    }
    
}