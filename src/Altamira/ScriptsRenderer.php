<?php

namespace Altamira;

class ScriptsRenderer extends \ArrayIterator
{
    
    public function render( $withScript = false )
    {
        
        if ( $withScript ) {
            echo "<script type='text/javascript'>\n";
        }
        
        echo $this->current();
        
        if ( $withScript ) {
            echo "\n</script>\n";
        }
        
        return $this;
        
    }
    
}