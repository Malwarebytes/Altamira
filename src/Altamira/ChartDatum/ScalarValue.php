<?php
/**
 * Class definition for \Altamira\ChartDatum\ScalarValue
 */
namespace Altamira\ChartDatum;
/**
 * Scalar values have a single value, and then a label
 * @author relwell
 */
class ScalarValue extends ChartDatumAbstract
{
    /**
     * Constructor method
     * @param mixed $dimensions
     * @param string $label
     */
    public function __construct( $dimensions, $label = null )
    {
        if ( $label !== null ) {
            $this->setLabel( $label );
        }
        
        if ( is_array( $dimensions ) ) {
            $this['value'] = array_shift( $dimensions );
        } else {
            $this['value'] = $dimensions;
        }
    }
    
    /**
     * Returns associative array
     * @see \Altamira\ChartDatum\ChartDatumAbstract::getRenderData()
     * @return array
     */
    public function getRenderData( $useLabel = false )
    {
        return $this->toArray();
    }
}