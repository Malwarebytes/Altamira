<?php

namespace Altamira;

/**
 * Used for rendering JavaScript files
 */
class FilesRenderer extends \ArrayIterator
{
    /**
     * Constructor method. Providing a path prepends that path to all files.
     * @param array  $array
     * @param string $path
     */
    public function __construct( array $array )
    {
        $mutatedVals = array();
        foreach ( $array as $file ) {
            $mutatedVals[] = $this->handleMinify( $file );
        }

        parent::__construct( $mutatedVals );
    }
    
    /**
     * Render method. Sends to output buffer.
     * @return \Altamira\FilesRenderer provides fluent interface
     */
    public function render()
    {
        echo "<script type=\"text/javascript\" src=\"{$this->current()}\"></script>\n";
        
        return $this;
        
    }    

    public function append( $val )
    {
        return parent::append( $this->handleMinify( $val ) );
    }

    public function offsetSet( $offset, $val )
    {
        return parent::offsetSet( $offset, $this->handleMinify( $val ) );
    }

    protected function handleMinify( $val )
    {
        return \Altamira\Config::minifyJs()
               ? preg_replace( '/.(min.)?js/', '.min.js', $val )
               : preg_replace( '/.(min.)?js/', '.js', $val );
    }
}