<?php
/**
 * Class definition for \Altamira\ChartDatum\ChartDatumAbstract
 * @author relwell
 *
 */
namespace Altamira\ChartDatum;

use \Altamira\JsWriter\JsWriterAbstract;
use \Altamira\Series;

/**
 * Data abstraction class for points.
 * Provides a common interface for data access between different dimensionalities of chart data
 * @package ChartDatum
 * @author relwell
 */
abstract class ChartDatumAbstract implements \ArrayAccess
{
    /**
     * Contains all information about 
     * @var array
     */
    protected $datumData;
    
    /**
     * Used to determine how we render certain values
     * @var Altamira\JsWriter\JsWriterAbstract
     */
    protected $jsWriter;
    
    /**
     * Used to determine how we render certain values, based on, for instance, type
     * @var Altamira\Series
     */
    protected $series;
    
    /**
     * Constructor method
     * @param array       $dimensions
     * @param string|null $label
     * @throw \InvalidArgumentException
     */
    abstract public function __construct( array $dimensions, $label = null );
    
    /**
     * Used for rendering into json string
     * @param  boolean $useLabel whether to use a label (doesn't always apply)
     * @return array
     */
    abstract public function getRenderData( $useLabel = false );
    
    /**
     * Set the label for this datum
     * @param string $label
     * @return \Altamira\ChartDatumAbstract
     */
    public function setLabel( $label )
    {
        $this['label'] = $label;
        return $this;
    }
    
    /**
     * Sets the JsWriter relative to the datum
     * @param \Altamira\JsWriter\JsWriterAbstract $jsWriter
     * @return \Altamira\ChartDatum\ChartDatumAbstract
     */
    public function setJsWriter( JsWriterAbstract $jsWriter )
    {
        $this->jsWriter = $jsWriter;
        return $this;
    }
    
    /**
     * Registers an instance of \Altamira\Series with this instance 
     * @param \Altamira\Series $series
     */
    public function setSeries( Series $series )
    {
        $this->series = $series;
    }
    
    /**
     * Convenience method for accessing label the same way we set it. 
     * @return string
     */
    public function getLabel()
    {
        return $this['label'];
    }
    
	/**
	 * Returns whether a value exists for the provided offset
     * @see ArrayAccess::offsetExists()
     * @param string $offset
     * @return bool
     */
    public function offsetExists ($offset)
    {
        return isset($this->datumData[$offset]);
    }

	/**
	 * Returns a value for the provided offset
     * @see ArrayAccess::offsetGet()
     * @param string $offset
     * @return mixed value
     */
    public function offsetGet ($offset)
    {
        return isset($this->datumData[$offset]) ? $this->datumData[$offset] : false;
    }

	/**
	 * Sets a value for the provided offset
     * @see ArrayAccess::offsetSet()
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet ($offset, $value)
    {
        $this->datumData[$offset] = $value;
    }

	/**
	 * De-registers the value for the provided offset
     * @see ArrayAccess::offsetUnset()
     * @param string $offset
     */
    public function offsetUnset ($offset)
    {
        unset($this->datumData[$offset]);
    }
    
    /**
     * Allows us to directly json_encode the values set in the datum
     * @return array
     */
    public function toArray()
    {
        return $this->datumData;
    }

}