<?php 

namespace Altamira\ChartDatum;

use Altamira\ChartDatum\Bubble;

class BubbleFactory
{
    
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