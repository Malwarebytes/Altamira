<?php

class ChartDatumTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Altamira\ChartDatum\TwoDimensionalPoint::__construct
     * @covers \Altamira\ChartDatum\TwoDimensionalPoint::getRenderData
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::setLabel
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::setJsWriter
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::getLabel
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::setSeries
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::offsetExists
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::offsetSet
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::offsetGet
     * @covers \Altamira\ChartDatum\ChartDatumAbstract::offsetUnset
     */
    public function testTwoDimensionalPointAndAbstract()
    {
        $label = 'foo';
        
        $dimensions = array( 'x' => 0 );
        $exception = null;
        try {
            new \Altamira\ChartDatum\TwoDimensionalPoint( $dimensions, $label );
        } catch ( Exception $exception ) {}
        $this->assertInstanceOf(
                'Exception',
                $exception
        );
        
        $dimensions = array( 'x' => 1, 'y' => 0 );
        $point = new \Altamira\ChartDatum\TwoDimensionalPoint( $dimensions, $label );
        
        $this->assertEquals(
                $dimensions['x'],
                $point['x'],
                'The dimension array should pass its x value to the point class during construction'
        );
        
        $this->assertTrue(
                isset( $point['x'] )
        );
        
        $this->assertEquals(
                $dimensions['y'],
                $point['y'],
                'The dimension array should pass its y value to the point class during construction'
        );
        $this->assertEquals(
                $label,
                $point->getLabel(),
                '\Altamira\ChartDatum\ChartDatumAbstract::setLabel should be retrievable using getLabel()'
        );
        
        $mockChart = $this->getMock( '\Altamira\Chart', array(), array( \Altamira\JsWriter\Flot::LIBRARY ) );
        
        $mockJsWriter = $this->getMock( '\Altamira\JsWriter\Flot', array( 'getType' ), array( $mockChart ) );
        
        $this->assertEquals(
                $point,
                $point->setJsWriter( $mockJsWriter ),
                '\Altamira\ChartDatum\ChartDatumAbstract::setJsWriter should provide a fluent interface'
        );
        
        $mockSeries = $this->getMock( '\Altamira\Series', array( 'getTitle' ), array( array( $point ), 'foo', $mockJsWriter ) );
        
        $mockSeries
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $mockDonutType = $this->getMock( '\Altamira\Type\Flot\Donut', array(), array( $mockJsWriter ) );
        
        $point->setSeries( $mockSeries );
        
        $mockJsWriter
            ->expects( $this->at( 0 ) )
            ->method ( 'getType' )
            ->with   ( 'foo' )
            ->will   ( $this->returnValue( null ) )
        ;
        $mockJsWriter
            ->expects( $this->at( 1 ) )
            ->method ( 'getType' )
            ->with   ( 'foo' )
            ->will   ( $this->returnValue( $mockDonutType ) )
        ;
        $mockJsWriter
            ->expects( $this->at( 2 ) )
            ->method ( 'getType' )
            ->with   ( 'foo' )
            ->will   ( $this->returnValue( null ) )
        ;
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'] ),
                $point->getRenderData(),
                '\Altamira\TwoDimensionalPoint::getRenderData should return array(x, y) by default'
        );
        $this->assertEquals(
                array( 1, $dimensions['y'] ),
                $point->getRenderData(),
                '\Altamira\TwoDimensionalPoint::getRenderData should return array(1, y) if we have a donut flot sitch'
        );
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'], $label ),
                $point->getRenderData( true ),
                '\Altamira\TwoDimensionalPoint::getRenderData should return array(x, y, label) when passed true as its first parameter'
        );
        
        unset( $point['x'] );
        $this->assertEmpty(
                $point['x']
        );
        
    }
    
    /**
     * @covers \Altamira\ChartDatum\Bubble::__construct
     * @covers \Altamira\ChartDatum\Bubble::getRenderData
     */
    public function testBubble()
    {
        $dimensions = array( 'x' => 10, 'y' => 20 );
        $label = 'bubble';
        $exception = null;
        try {
            $bubble = new \Altamira\ChartDatum\Bubble( $dimensions, $label );
        } catch ( \InvalidArgumentException $exception ) {}
        
        $this->assertInstanceOf(
                '\InvalidArgumentException',
                $exception,
                '\Altamira\ChartDatum\Bubble should throw an exception if the proper aray keys are not set'
        );
        
        $dimensions['radius'] = 15;
        
        $bubble = new \Altamira\ChartDatum\Bubble( $dimensions, $label );
        
        $this->assertEquals(
                $dimensions['x'],
                $bubble['x'],
                'The dimension array should pass its x value to the bubble class during construction'
        );
        $this->assertEquals(
                $dimensions['y'],
                $bubble['y'],
                'The dimension array should pass its y value to the bubble class during construction'
        );
        $this->assertEquals(
                $dimensions['radius'],
                $bubble['radius'],
                'The dimension array should pass its radius value to the bubble class during construction'
        );
        
        $mockChart        = $this->getMock( '\Altamira\Chart', array(), array( \Altamira\JsWriter\Flot::LIBRARY ) );
        $mockFlotJsWriter = $this->getMock( '\Altamira\JsWriter\Flot', array(), array( $mockChart ) );
        $mockSeries       = $this->getMock( '\Altamira\Series', array( 'getTitle' ), array( array( $bubble ), 'foo', $mockFlotJsWriter ) );
        $bubble->setSeries( $mockSeries );
        
        
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'], $dimensions['radius'] * 10 ),
                $bubble->getRenderData(),
                'Default renderdata should return x, y, radius * 10 for bubble using flot'
        );
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'], $dimensions['radius'] * 10, $label ),
                $bubble->getRenderData( true ),
                'Default renderdata should return x, y, radius * 10, and label for bubble with true passed using flot'
        );
        
        $mockJqPlotJsWriter = $this->getMock( '\Altamira\JsWriter\JqPlot', array(), array( $mockChart ) );
        $bubble->setJsWriter( $mockJqPlotJsWriter );
        
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'], $dimensions['radius'] ),
                $bubble->getRenderData(),
                'Default renderdata should return x, y, radius for bubble'
        );
        $this->assertEquals(
                array( $dimensions['x'], $dimensions['y'], $dimensions['radius'], $label ),
                $bubble->getRenderData( true ),
                'Default renderdata should return x, y, radius, and label for bubble with true passed'
        );
    }
    
    /**
     * @covers \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromYValues
     * @covers \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromXValues
     * @covers \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromNested
     * @covers \Altamira\ChartDatum\BubbleFactory::getBubbleDatumFromTupleSet
     */
    public function testFactories()
    {
        $values = array( 0 => 1, 1 => 2, 2 => 3 );
        
        $allValues    = \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromYValues( $values );
        $yvaluePoints = $allValues;
        $allValues    = \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromXValues( $values, $allValues );
        $xvaluePoints = array_slice($allValues, 0, count( $yvaluePoints ) );
        
        foreach ( $values as $key => $value ) {
            $chartDatumX = array_shift( $xvaluePoints );
            $chartDatumY = array_shift( $yvaluePoints );
            
            $this->assertInstanceOf(
                    '\Altamira\ChartDatum\TwoDimensionalPoint',
                    $chartDatumY,
                    '\Altamira\ChartDatum\TwoDimensionalPointFactory::getFromYValues should return an array of chartdatum instances when not provided an array to append to' 
            );
            $this->assertEquals(
                    $value,
                    $chartDatumY['y']
            );
            $this->assertEquals(
                    $key+1,
                    $chartDatumY['x'],
                    '\Altamira\ChartDatum\TwoDimensionalPointFactory::getFromYValues should set the X value as one greater than the source data\'s array index'
            );
            $this->assertEquals(
                    $key+1,
                    $chartDatumX['y'],
                    '\Altamira\ChartDatum\TwoDimensionalPointFactory::getFromXValues should set the X value as one greater than the source data\'s array index'
            );
            $this->assertEquals(
                    $value,
                    $chartDatumX['x']
            );
        }
        
        $nestedData = array( array( 0, 5 ),
                             array( 1, 1 ),
                             array( 1, 2 )
                            );
        
        $nestedPoints = \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromNested( $nestedData );
        
        foreach ( $nestedPoints as $nestedPoint ) {
            $nestedVal = array_shift( $nestedData );
            $this->assertEquals(
                    $nestedVal[0],
                    $nestedPoint['x']
            );
            $this->assertEquals(
                    $nestedVal[1],
                    $nestedPoint['y']
            );
        }
        
        $tupleSet = array( array( 'foo', 2, 3, 4 ),
                           array( 'bar', 3, 4, 5 )
                         );
        
        $bubblePoints = \Altamira\ChartDatum\BubbleFactory::getBubbleDatumFromTupleSet( $tupleSet );
        
        foreach( $bubblePoints as $bubble ) {
            $tuple = array_shift( $tupleSet );
            $this->assertEquals(
                    $tuple[0],
                    $bubble->getLabel()
            );
            $this->assertEquals(
                    $tuple[1],
                    $bubble['x']
            );
            $this->assertEquals(
                    $tuple[2],
                    $bubble['y']
            );
            $this->assertEquals(
                    $tuple[3],
                    $bubble['radius']
            );
        }
        
    }
    
}