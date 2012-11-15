<?php 

namespace Altamira\ChartDatum;

use Altamira\ChartDatum\TwoDimensionalPoint;

class TwoDimensionalPointFactory
{
    /**
     * Transforms a one-dimensional array to x,y points. Adds one to each X value as array index. Y value is array value.
     * Passing an array will append the results to an existing array.
     * For more freedom over points, use getFromNested
     * @param array $oneDimensionalArray
     * @param array|null $result
     * @return array of \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromYValues( $oneDimensionalArray, $result = array() ) {
        foreach ($oneDimensionalArray as $x => $y ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $x+1, 'y' => $y ) );
        }
        return $result;
    }

    /**
     * Transforms a one-dimensional array to x,y points. Adds one to each Y value as array index. X value is array value.
     * Passing an array will append the results to an existing array.
     * For more freedom over value relationships, use getFromNested
     * @param array $oneDimensionalArray
     * @param array|null $result
     * @return array of \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromXValues( $oneDimensionalArray, $result = array() ) {
        foreach ($oneDimensionalArray as $y => $x ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $x, 'y' => $y+1 ) );
        }
        return $result;
    }
    
    /**
     * Transforms an array of arrays into points using the first two array values of each nested array.
     * Passing an array will append the results to an existing array.
     * @param array $nestedArray
     * @param array $result
     * @return \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromNested( $nestedArray, $result = array() ) {
        foreach ( $nestedArray as $array ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $array[0], 'y' => $array[1] ) );
        }
        return $result;
    }
}