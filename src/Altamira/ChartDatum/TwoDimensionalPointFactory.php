<?php 
/**
 * Class definition for \Altamira\ChartDatum\TwoDimensionalPointFactory
 * @author relwell
 */
namespace Altamira\ChartDatum;

use Altamira\ChartDatum\TwoDimensionalPoint;
/**
 * Responsible for creating instances of \Altamira\ChartDatum\TwoDimensionalPoint
 * @author relwell
 * @package ChartDatum
 */
class TwoDimensionalPointFactory
{
    /**
     * Transforms a one-dimensional array to x,y points. Adds one to each X value as array index. Y value is array value.
     * Passing an array will append the results to an existing array.
     * In other words, providing the following array:
     * <code>array( 5, 10, 15, 20, 25 )</code>
     * will yield the following coordinates (as {x,y}):
     * <code>[{1,5}, {2,10}, {3,15}, {4,20}, {5,25}]</code>
     * For more freedom over points, use getFromNested.
     * @param array $oneDimensionalArray
     * @param array|null $result
     * @return array of \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromYValues( $oneDimensionalArray, &$result = array() ) {
        foreach ($oneDimensionalArray as $x => $y ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $x+1, 'y' => $y ) );
        }
        return $result;
    }

    /**
     * Transforms a one-dimensional array to x,y points. Adds one to each Y value as array index. X value is array value.
     * Passing an array will append the results to an existing array.
     * In other words, providing the following array:
     * <code>array( 5, 10, 15, 20, 25 )</code>
     * will yield the following coordinates (as {x,y}):
     * <code>[{5,1}, {10,2}, {15,3}, {20,4}, {25,5}]</code>
     * For more freedom over value relationships, use getFromNested.
     * @param array $oneDimensionalArray
     * @param array|null $result
     * @return array of \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromXValues( $oneDimensionalArray, &$result = array() ) {
        foreach ($oneDimensionalArray as $y => $x ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $x, 'y' => $y+1 ) );
        }
        return $result;
    }
    
    /**
     * Transforms an array of arrays into points using the first two array values of each nested array.
     * Passing an array will append the results to an existing array.
     * Therefore, you providing the following array:
     * <code>array( array( 10, 8 ), array( 11, 24 ), array( 0.3341, 1551351235 ) )</code>
     * will yield the following two-dimensional points (as {x,y}):
     * <code>[{10, 8}, {11, 24}, {0.3341, 1551351235}]</code>
     * @param array $nestedArray
     * @param array $result
     * @return \Altamira\ChartDatum\TwoDimensionalPoint
     */
    public static function getFromNested( $nestedArray, &$result = array() ) {
        foreach ( $nestedArray as $array ) 
        {
            $result[] = new TwoDimensionalPoint( array('x' => $array[0], 'y' => $array[1] ) );
        }
        return $result;
    }
}