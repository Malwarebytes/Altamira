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
        parent::__construct($array);
    }
    
    /**
     * Render method. Sends to output buffer.
     * @return \Altamira\FilesRenderer provides fluent interface
     */
    public function render()
    {
        echo <<<ENDSCRIPT
<script type="text/javascript" src="{$this->current()}"></script>

ENDSCRIPT;
        
        return $this;
        
    }
    
}