<?php
/**
 * Class definition for \Altamira\ChartDatum\ScalarValueFactory
 * @author relwell
 */
namespace Altamira\ChartDatum;
use Altamira\ChartDatum\ScalarValue;
/**
 * Provides static methods for instantiating scalar value instances from an array
 * @author relwell
 *
 */
class ScalarValueFactory
{
    /**
     * Generates instances of \Altamira\ChartDatum\ScalarValue based on a flat array.
     * The array format should be as follows:
     * <code>array( 1, 3, 5, 7, 9, 42 )</code>
     * @param array $values
     * @param array $results allows us to append values to an existing result array
     * @return array
     */
    public static function getFromScalarArray( array $values, array $results = array() )
    {
        foreach ( $values as $value )
        {
            $results[] = new ScalarValue( $value );
        }
        return $results;
    }
    
    /**
     * Generates instances of \Altamira\ChartDatum\ScalarValue based on a nested array.
     * The array format should consist of value and point label:
     * <code>array( array( 1, 'golf clubs' ), array( 5, 'golf shoes' ), array( 18, 'holes' ), array( 32, 'strokes' ) )</code>
     * @param array $nested
     * @param array $results allows us to append to an existing array
     * @return array
     */
    public static function getFromNestedArray( array $nested, array $results = array() )
    {
        foreach ( $nested as $nesting )
        {
            $results[] = new ScalarValue( $nesting[0], $nesting[1] );
        }
        return $results;
    }
    
    /**
     * Generates instances of \Altamira\ChartDatum\ScalarValue based on an associative array.
     * The array should be keyed by label, valued by scalar value:
     * <code>array( 'golf clubs' => 1, 'golf shoes' => 5, 'holes' => 18, 'strokes' => 32 )</code>
     * @param array $assoc
     * @param array $results
     * @return array
     */
    public static function getFromAssociativeArray( array $assoc, array $results = array() )
    {
        foreach ( $assoc as $label => $value )
        {
            $results[] = new ScalarValue( $value, $label );
        }
        return $results;
    }
}