<?php 

namespace Altamira;

class Config implements \ArrayAccess
{
    /**
     * 
     * @var array
     */
    private $config;
    
    public function __construct( $file )
    {
        $this->config = parse_ini_file( $file, true );
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