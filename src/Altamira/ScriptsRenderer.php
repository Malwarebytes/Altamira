<?php
/**
 * Class definition for \Altamira\ScriptsRenderer
 * @author relwell
 *
 */
namespace Altamira;

/**
 * Used for rendering inline JavaScript
 */
class ScriptsRenderer extends \ArrayIterator
{
    /**
     * Renders the JS stored herein. Passing true to $withScript wraps it in its own <script> tags.
     * @param bool $withScript
     * @return \Altamira\ScriptsRenderer provides for fluent interface
     */
    public function render( $withScript = false )
    {
       echo $this->get( $withScript );
        
        return $this;
        
    }
    
    /**
     * Returns the current script value.
     * @param boolean $withScript
     * @return Ambigous <string, mixed>
     */
    public function get( $withScript = false )
    {
        $retVal = '';
        
        if ( $withScript ) {
            $retVal .= "<script type='text/javascript'>\n";
        }
        
        $retVal .= $this->current();
        
        if ( $withScript ) {
            $retVal .= "\n</script>\n";
        }
        
        return $retVal;
        
    }
    
}