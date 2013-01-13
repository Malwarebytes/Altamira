<?php 
/**
 * Class definition for \Altamira\ChartDatum\TwoDimensionalPoint
 * @author relwell
 */
namespace Altamira\ChartDatum;

/**
 * Class for abstracting chart data with an X, a Y, and a label.
 * @author relwell
 * @package ChartDatum
 */
class TwoDimensionalPoint extends ChartDatumAbstract
{
    /**
     * Constructor method
     * @param array       $dimensions with x and y keys
     * @param string|null $label
     * @throws \InvalidArgumentException
     */
    public function __construct( array $dimensions, $label = null ) 
    { 
        if ( $label !== null ) {
            $this->setLabel( $label );
        }
        
        if (! ( isset( $dimensions['x'] ) && isset( $dimensions['y'] ) ) ) {
            throw new \InvalidArgumentException( 'Altamira\ChartDatum\TwoDimensionalPoint requires array keys for x and y values in argument 1.' );
        }
        
        $this['x'] = $dimensions['x'];
        $this['y'] = $dimensions['y'];
    }
    
    /**
     * Provides the data prepared for json encoding
     * @see Altamira\ChartDatum.ChartDatumAbstract::getRenderData
     * @param bool $useLabel whether to include the label for this point
     * @return array 
     */
    public function getRenderData( $useLabel = false )
    {
        if ( ( $this->jsWriter->getType( $this->series->getTitle() ) instanceof \Altamira\Type\Flot\Donut ) ) {
            $value = array( 1, $this['y'] );
        } else {
            $value = array($this['x'], $this['y']);
            if ( $useLabel ) {
                $value[] = $this->getLabel();
            }
        }
        return $value;
    }
    
}