<?php 
/**
 * Class definition for \Altamira\ChartDatum\BubbleFactory
 * @author relwell
 */
namespace Altamira\ChartDatum;

use Altamira\ChartDatum\Bubble;
/**
 * Responsible for creating instances of \Altamira\ChartDatum\Bubble
 * @package ChartDatum
 * @author relwell
 */
class BubbleFactory
{
    /**
     * Returns an array of results provided an array of arrays of four values -- label, x, y, and radius.
     * This means that an array should be formatted as follows:
     * <code>array( 'this is my label', 10, 20, 15 )</code>
     * Where the label is "this is my label", the point at the center of the bubble is located at {10, 20}, and the radius of the bubble is 15.
     * @param array $tupleSet
     * @param array $results provided if you have an existing array of ChartDatum instances you want to append to
     * @return array of \Altamira\ChartDatum\Bubble
     */
    public static function getBubbleDatumFromTupleSet( array $tupleSet, $results = array() ) 
    {
        foreach ( $tupleSet as $tuple )
        {
            list( $label, $x, $y, $radius ) = $tuple;
            $mapped = array('x'=>$x, 'y'=>$y, 'radius'=>$radius);
            $results[] = new Bubble($mapped, $label);
        }
        return $results;
    }
    
}