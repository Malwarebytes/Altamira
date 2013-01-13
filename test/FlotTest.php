<?php

class FlotTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->flot = $this->getMockBuilder( '\Altamira\JsWriter\Flot' )->disableOriginalConstructor();
        
        $this->options = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'options' );
        $this->options->setAccessible( true );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::prepOpts
     */
    public function testFlotPrepOpts()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )->disableOriginalConstructor()->getMock();
        $mockFlot = new \Altamira\JsWriter\Flot( $mockChart  );
        
        $opts = array();
        
        $mockFlot->prepOpts( $opts );
        
        $this->assertEquals(
                array( 'points' => array('show'=>true), 'lines' => array('show'=>true) ),
                $opts
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setAxisTicks
     */
    public function testFlotSetAxisTicks()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setAxisTicks( 'x', array( 1, 2, 3, 'foo', '5' ) )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesMarkerSize
     */
    public function testFlotSetSeriesMarkerSize()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'points', 'radius', 4 )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setSeriesMarkerSize( 'footitle', 8 )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesMarkerStyle
     */
    public function testFlotSetSeriesMarkerStyle()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();

        // "filled" should be removed
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'points', 'symbol', 'diamond' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $mockFlot->setSeriesMarkerStyle( 'footitle', 'filleddiamond');
        
        $files = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'files' );
        $files->setAccessible( true );
        
        $this->assertContains(
                'jquery.flot.symbol.js',
                $files->getValue( $mockFlot ),
                'Flot::setSeriesMarkerStyle should register the symbol plugin'
        );
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'points', 'symbol', 'diamond' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setSeriesMarkerStyle( 'footitle', 'diamond')
        );
        
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesShowMarker
     */
    public function testFlotSetSeriesShowMarker()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'points', 'show', true )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setSeriesShowMarker( 'footitle', true )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesShowLine
     */
    public function testFlotSetSeriesShowLine()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'lines', 'show', true )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setSeriesShowLine( 'footitle', true )
        );
    }
    

    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesLineWidth
     */
    public function testFlotSetSeriesLineWidth()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'footitle', 'lines', 'linewidth', 10 )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setSeriesLineWidth( 'footitle', 10 )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setSeriesLabelSetting
     */
    public function testSetSeriesLabelSetting()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $labelsettingRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'labelSettings' );
        $labelsettingRefl->setAccessible( true );
        
        $mockFlot->setSeriesLabelSetting( 'foo', 'bar', 'baz' );
        
        $this->assertContains(
                'baz',
                $labelsettingRefl->getValue( $mockFlot )
        );
        $this->assertArrayHasKey(
                'bar',
                $labelsettingRefl->getValue( $mockFlot )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::useSeriesLabels
     */
    public function testUseSeriesLabel() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $labelArray = array( 'this', 'is', 'my', 'label', 'array' );
        
        $uselabelRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'useLabels' );
        $uselabelRefl->setAccessible( true );
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'foo', 'pointLabels', 'edgeTolerance', 3 )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->useSeriesLabels( 'foo', $labelArray )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setShadow
     */
    public function testSetShadowWithoutUse() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->never() )
            ->method     ( 'setNestedOptVal' )
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setShadow( 'foo', array() )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setShadow
     */
    public function testSetShadowWithUse() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->once() )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'foo', 'shadowSize', 3 )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setShadow( 'foo' )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setFill
     */
    public function testSetFillWithoutUse() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->never() )
            ->method     ( 'setNestedOptVal' )
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setFill( 'foo', array() )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setFill
     */
    public function testSetFillWithUse() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'foo', 'line', 'fill', true )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'foo', 'line', 'fillColor', '#333333' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setFill( 'foo', array( 'use' => true, 'color' => '#333333' ) )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setLegend
     */
    public function testSetLegend() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'legend', 'show', true )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'legend', 'position', 'ne' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 2 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'legend', 'backgroundColor', '#333' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 3 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'legend', 'margin', array( 0, 0 ) )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setLegend( array( 'on' => true, 'location' => 'ne', 'backgroundColor' => '#333', 'x' => 0, 'y' => 0 ) )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::setGrid
     */
    public function testSetGrid() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'grid', 'show', true )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'grid', 'backgroundColor', '#333' )
            ->will       ( $this->returnValue( $mockFlot ) ) 
        ;

        $this->assertEquals(
                $mockFlot,
                $mockFlot->setGrid( array( 'on' => true, 'backgroundColor' => '#333', 'not-stored' => 'wont-happen' ) )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::useZooming
     */
    public function testUseZooming() {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'selection', 'mode', 'xy' ) 
        ;
        $this->assertEquals(
                $mockFlot,
                $mockFlot->useZooming()
        );
        
        $filesRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'files' );
        $filesRefl->setAccessible( true );
        
        $this->assertContains(
                'jquery.flot.selection.js',
                $filesRefl->getValue( $mockFlot ),
                '\Altamira\JsWriter\Flot::useZooming should add the selection plugin upon invocation'
        );
        
        $zoomingRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'zooming' );
        $zoomingRefl->setAccessible( true );
        
        $this->assertTrue(
                $zoomingRefl->getValue( $mockFlot ),
                '\Altamira\JsWriter\Flot::useZooming should set the zooming property to true'
        );
    }
    
    /**
     * covers \Altamira\JsWriter\Flot::useDates
     */
    public function testUseDates()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'xaxis', 'mode', 'time' ) 
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'xaxis', 'timeformat', '%d-%b-%y' ) 
        ;
        $this->assertEquals(
                $mockFlot,
                $mockFlot->useDates()
        );
        
        $dateAxesRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'dateAxes' );
        $dateAxesRefl->setAccessible( true );
        $dateAxes = $dateAxesRefl->getValue( $mockFlot );
        $this->assertTrue(
                $dateAxes['x'],
                '\Altamira\JsWriter\Flot::useDates should set the provided axis key to true in the dateAxes property'
        ); 
        
        $filesRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'files' );
        $filesRefl->setAccessible( true );
        
        $this->assertContains(
                'jquery.flot.time.js',
                $filesRefl->getValue( $mockFlot ),
                '\Altamira\JsWriter\Flot::useDates should add the time plugin upon invocation'
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::useCursor
     */
    public function testUseCursor()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'cursor', array( 'show' => true, 'showTooltip' => true ) )
            ->will       ( $this->returnValue( $mockFlot ) )
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->useCursor()
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::useHighlighting
     */    
    public function testUseHighlighting()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'grid', 'hoverable', true )
            ->will       ( $this->returnValue( $mockFlot ) )
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'grid', 'autoHighlight', true )
            ->will       ( $this->returnValue( $mockFlot ) )
        ;
        
        $this->assertEquals(
                $mockFlot,
                $mockFlot->useHighlighting()
        );
        
        $highlightingRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'highlighting' );
        $highlightingRefl->setAccessible( true );
        
        $this->assertTrue(
                $highlightingRefl->getValue( $mockFlot ),
                '\Altamira\JsWriter\Flot::useHighlighting should set the highlighting property to true'
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::unsetOpt
     */
    public function testUnsetOpt()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $unsetOpt = new ReflectionMethod( 'Altamira\JsWriter\Flot', 'unsetOpt' );
        $unsetOpt->setAccessible( true );
        
        $testOpts = array( 'foo' => array( 'bar' => array( 'baz' => array( 'qux' => true ) ) ) );
        
        $unsetOpt->invoke( $mockFlot, &$testOpts, 'foo.bar.baz.qux' );
        
        $this->assertFalse(
                array_key_exists( 'qux', $testOpts['foo']['bar']['baz'] )
        );
    }

    /**
     * @covers \Altamira\JsWriter\Flot::setOpt
     */
    public function testSetOpt()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $setOpt = new ReflectionMethod( 'Altamira\JsWriter\Flot', 'setOpt' );
        $setOpt->setAccessible( true );
        
        $testOpts = array();
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $testOpts, array( 'foo', 'bar', 'baz', 'qux' ) )
        ;
        
        $setOpt->invoke( $mockFlot, &$testOpts, 'foo.bar.baz', 'qux' );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getOptVal
     */
    public function testGetOptVal()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $getOptVal = new ReflectionMethod( 'Altamira\JsWriter\Flot', 'getOptVal' );
        $getOptVal->setAccessible( true );
        
        $testOpts = array( 'foo' => array( 'bar' => array( 'baz' => 'qux' ) ) );
        
        $this->assertEquals(
                'qux',
                $getOptVal->invoke( $mockFlot, &$testOpts, 'foo.bar.baz' )
        );
        $this->assertNull(
                $getOptVal->invoke( $mockFlot, &$testOpts, 'foo.bar.buzz' )
        );
        $this->assertNull(
                $getOptVal->invoke( $mockFlot, &$testOpts, '' )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getOptionsJs
     */
    public function testGetOptionsJs()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal', 'setOpt' ,'unsetOpt', 'makeJSArray', 'getOptVal' ) )->getMock();
        
        
        $options = array( 'seriesColors' => array( '#333', '#ccc' ) );
        
        $this->options->setValue( $mockFlot, $options );
        
        $incr = 0;
        
        $mapperRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'optsMapper' );
        $mapperRefl->setAccessible( true );
        foreach ( $mapperRefl->getValue( $mockFlot ) as $key => $val )
        {
            if ( $key == 'seriesColors' ) {
                continue;
            }
            
            $mockFlot
                ->expects    ( $this->at( $incr++ ) )
                ->method     ( 'getOptVal' )
                ->with       ( $this->options->getValue( $mockFlot ), $key )
                ->will       ( $this->returnValue( null ) )
            ;
        }
        
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'getOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesColors' )
            ->will       ( $this->returnValue( array( '#333', '#ccc' ) ) )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'setOpt' )
            ->with       ( $this->options->getValue( $mockFlot ), 'colors', array( '#333', '#ccc' ) )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'unsetOpt' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesColors' )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'getOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'pie', 'show' )
            ->will       ( $this->returnValue( null ) )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage', 'pie', 'show', false )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'unsetOpt' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesStorage' )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'unsetOpt' )
            ->with       ( $this->options->getValue( $mockFlot ), 'seriesDefault' )
        ;
        $mockFlot
            ->expects    ( $this->at( $incr++ ) )
            ->method     ( 'makeJSArray' )
            ->with       ( $this->options->getValue( $mockFlot ) )
        ;
        
        $reflGet = new ReflectionMethod( '\Altamira\JsWriter\Flot', 'getOptionsJs' );
        $reflGet->setAccessible( true );
        $reflGet->invoke( $mockFlot );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::initializeSeries
     */
    public function testInitializeSeries()
    {
        $mockFlot = $this->flot->setMethods( array( 'getSeriesTitle' ) )->getMock();
        
        $mockFlot
            ->expects    ( $this->any() )
            ->method     ( 'getSeriesTitle' )
            ->with       ( 'foo' )
            ->will       ( $this->returnValue( 'foo' ) )
        ;
        $optionsrefl = new ReflectionProperty( 'Altamira\JsWriter\Flot', 'options' );
        $optionsrefl->setAccessible( true );
        $this->assertEquals(
                $mockFlot,
                $mockFlot->initializeSeries( 'foo' )
        );
        $options = $optionsrefl->getValue( $mockFlot );
        $this->assertArrayHasKey(
                'foo',
                $options['seriesStorage']
        );
        $this->assertArrayHasKey(
                'label',
                $options['seriesStorage']['foo']
        );
        $this->assertEquals(
                'foo',
                $options['seriesStorage']['foo']['label']
        );
    }
    
    /**
     * @covers Altamira\JsWriter\Flot::setAxisOptions
     */
    public function testSetAxisOptionsNative()
    {
        $mockFlot = $this->flot->setMethods( array( 'setNestedOptVal' ) )->getMock();
        
        $optionsrefl = new ReflectionProperty( 'Altamira\JsWriter\Flot', 'options' );
        $optionsrefl->setAccessible( true );
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setNestedOptVal' )
            ->with       ( $optionsrefl->getValue( $mockFlot ), 'xaxis', 'min', 10 )
        ;
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setAxisOptions( 'x', 'min', 10 )
        );
    }
    
    /**
     * @covers Altamira\JsWriter\Flot::setAxisOptions
     */
    public function testSetAxisOptionsMapped()
    {
        $mockFlot = $this->flot->setMethods( array( 'setOpt' ) )->getMock();
        
        $optionsrefl = new ReflectionProperty( 'Altamira\JsWriter\Flot', 'options' );
        $optionsrefl->setAccessible( true );
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setOpt' )
            ->with       ( $optionsrefl->getValue( $mockFlot ), 'xaxis.tickSize', 10 )
        ;
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setAxisOptions( 'x', 'tickInterval', 10 )
        );
    }
    
    /**
     * @covers Altamira\JsWriter\Flot::setAxisOptions
     */
    public function testSetAxisOptionsFormat()
    {
        $mockFlot = $this->flot->setMethods( array( 'getCallbackPlaceholder' ) )->getMock();
        
        $optionsrefl = new ReflectionProperty( 'Altamira\JsWriter\Flot', 'options' );
        $optionsrefl->setAccessible( true );
        
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getCallbackPlaceholder' )
            ->with       ( 'function(val, axis){return "foo".replace(/%d/, val);}' )
            ->will       ( $this->returnValue( '#foo#' ) )
        ;
        $this->assertEquals(
                $mockFlot,
                $mockFlot->setAxisOptions( 'x', 'formatString', 'foo' )
        );
        
        $options = $optionsrefl->getValue( $mockFlot );
        
        $this->assertContains(
                '#foo#',
                $options['xaxis']['tickFormatter']
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getExtraFunctionCalls
     */
    public function testGetExtraFunctionCallsZooming()
    {
        $mockFlot = $this->flot->setMethods( array( 'getCallbackPlaceholder' ) )->getMock();
        
        $zooming = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'zooming' );
        $zooming->setAccessible( true );
        $zooming->setValue( $mockFlot, true );
        
        $data = '{1, 2, 3, 4, 5}';
        $options = '{option:true}';
        
        $result = $mockFlot->getExtraFunctionCalls( $data, $options );
        
        $this->assertContains(
                sprintf( \Altamira\JsWriter\Flot::ZOOMING_FUNCTION, $data, $options, $data, $options ),
                $result
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getExtraFunctionCalls
     */
    public function testGetExtraFunctionCallsLabels()
    {
        $mockFlot = $this->flot->setMethods( array( 'getCallbackPlaceholder' ) )->getMock();
        
        $labels = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'useLabels' );
        $labels->setAccessible( true );
        $labels->setValue( $mockFlot, true );
        
        $labelList = array( 'brosef', 'stalin', 'annie', 'liebrowitz' );
        $pointLabels = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'pointLabels' );
        $pointLabels->setAccessible( true );
        $pointLabels->setValue( $mockFlot, $labelList );
        
        $labelSetting= new ReflectionProperty( '\Altamira\JsWriter\Flot', 'labelSettings' );
        $labelSetting->setAccessible( true );
        $labelSetting->setValue( $mockFlot, array( 'location' => 'ne', 'xpadding' => 5, 'ypadding' => 6 ) );
        
        $data = '{1, 2, 3, 4, 5}';
        $options = '{option:true}';
        
        $result = $mockFlot->getExtraFunctionCalls( $data, $options );
        
        $this->assertContains(
                sprintf( \Altamira\JsWriter\Flot::LABELS_FUNCTION, json_encode( $labelList ), '+15', '-5', '-15', '-6' ),
                $result
        );
        
        $labelSetting->setValue( $mockFlot, array( 'location' => 'sw', 'xpadding' => 5, 'ypadding' => 6 ) );
        
        $result = $mockFlot->getExtraFunctionCalls( $data, $options );
        
        $this->assertContains(
                sprintf( \Altamira\JsWriter\Flot::LABELS_FUNCTION, json_encode( $labelList ), '-15', '-5', '+15', '-6' ),
                $result
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getExtraFunctionCalls
     */
    public function testGetExtraFunctionCallsHighlighting()
    {
        $mockFlot = $this->flot->setMethods( array( 'getCallbackPlaceholder' ) )->getMock();
        
        $zooming = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'highlighting' );
        $zooming->setAccessible( true );
        $zooming->setValue( $mockFlot, true );
        
        $data = '{1, 2, 3, 4, 5}';
        $options = '{option:true}';
        
        $result = $mockFlot->getExtraFunctionCalls( $data, $options );
        
        $this->assertContains(
                sprintf( \Altamira\JsWriter\Flot::HIGHLIGHTING_FUNCTION, "x + ',' + y" ),
                $result
        );
        
        $dateAxes = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'dateAxes' );
        $dateAxes->setAccessible( true );
        $dateAxes->setValue( $mockFlot, array( 'x' => true ) );
        
        $result = $mockFlot->getExtraFunctionCalls( $data, $options );
        
        $this->assertContains(
                sprintf( \Altamira\JsWriter\Flot::HIGHLIGHTING_FUNCTION, "(new Date(parseInt(x))).toLocaleDateString() + ',' + y"),
                $result
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getScript
     */
    public function testGetScriptWorks()
    {
        $mockFlot = $this->flot->setMethods( array( 'makeJSArray', 'prepOpts', 'getOptionsJs', 'getExtraFunctionCalls' ) )->getMock();
        
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'getSeries', 'getName' ) )
                          ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getData', 'getTitle' ) )
                           ->getMock();
        
        $mockDatum = $this->getMockBuilder( '\Altamira\ChartDatum\Bubble' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'offsetGet', 'offsetSet', 'getLabel', 'getRenderData' ) )
                          ->getMock();
        
        $mockType = $this->getMockBuilder( '\Altamira\Type\Flot\Bubble' )
                         ->disableOriginalConstructor()
                         ->getMock();
        
        $chartRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'chart' );
        $chartRefl->setAccessible( true );
        $chartRefl->setValue( $mockFlot, $mockChart );
        
        $useLabelsRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'useLabels' );
        $useLabelsRefl->setAccessible( true );
        $useLabelsRefl->setValue( $mockFlot, true );
        
        $dateAxesRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'dateAxes' );
        $dateAxesRefl->setAccessible( true );
        $dateAxesRefl->setValue( $mockFlot, array( 'x' => true, 'y' => null ) );
        
        $typesRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'types' );
        $typesRefl->setAccessible( true );
        $typesRefl->setValue( $mockFlot, array( 'default' => $mockType ) );
        
        $ts = \DateTime::createFroMFormat( 'm/d/Y', '6/5/2012' );
        $stamp = $ts->getTimestamp() * 1000;
        $mockLabel = "my label yo";
        $formattedDatum = array( $stamp, 10, 15 );
        $mockSeriesTitle = 'mockSeriesTitle';
        $mockOptions = array( 
                'seriesStorage' => array(
                        $mockSeriesTitle => array(
                                'foo' => 'bar'
                                )
                        )
                );
        
        $optionsRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'options' );
        $optionsRefl->setAccessible( true );
        $optionsRefl->setValue( $mockFlot, $mockOptions );
        
        $mockChart
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getName' )
            ->will       ( $this->returnValue( 'mychart' ) )
        ;
        $mockChart
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'getSeries' )
            ->will       ( $this->returnValue( array( $mockSeriesTitle => $mockSeries ) ) )
        ;
        $mockSeries
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getData' )
            ->will       ( $this->returnValue( array( $mockDatum ) ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'offsetGet' )
            ->with       ( 'x' )
            ->will       ( $this->returnValue( '6/5/2012' ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'offsetSet' )
            ->with       ( 'x' )
            ->will       ( $this->returnValue( $stamp ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 2 ) )
            ->method     ( 'offsetGet' )
            ->with       ( 'x' )
            ->will       ( $this->returnValue( $stamp ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 3 ) )
            ->method     ( 'offsetGet' )
            ->with       ( 'y' )
            ->will       ( $this->returnValue( 10 ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 4 ) )
            ->method     ( 'getLabel' )
            ->will       ( $this->returnValue( $mockLabel ) )
        ;
        $mockDatum
            ->expects    ( $this->at( 5 ) )
            ->method     ( 'getRenderData' )
            ->will       ( $this->returnValue( $formattedDatum ) )
        ;
        $mockFlot
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'makeJSArray' )
            ->with       ( array( $formattedDatum ) )
            ->will       ( $this->returnValue( json_encode( array( $formattedDatum ) ) ) )
        ;
        $mockSeries
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getTitle' )
            ->will       ( $this->returnValue( '"this" is a title' ) )
        ;
        $mockFlot
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'prepOpts' )
        ;
        $mockFlot
            ->expects    ( $this->at( 2 ) )
            ->method     ( 'getOptionsJs' )
            ->will       ( $this->returnValue( null ) )
        ;
        $mockFlot
            ->expects    ( $this->at( 3 ) )
            ->method     ( 'getExtraFunctionCalls' )
            ->will       ( $this->returnValue( array() ) )
        ;
        
        $outputString = $mockFlot->getScript();
        
        $pointLabelsRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'pointLabels' );
        $pointLabelsRefl->setAccessible( true );
        $pointLabels = $pointLabelsRefl->getValue( $mockFlot );
        
        $this->assertArrayHasKey(
                $stamp.',10',
                $pointLabels
        );
        $this->assertEquals(
                $pointLabels[$stamp.',10'],
                $mockLabel
        );
        $this->assertEquals(
                sprintf( \Altamira\JsWriter\Flot::SCRIPT_OUTPUT, 'mychart', '[{data: [['.$stamp.',10,15]], label: "","foo":"bar"}]', ', {}', '' ),
                $outputString
        );
                
                
    }
    
    /**
     * @covers \Altamira\JsWriter\Flot::getScript
     */
    public function testGetScriptBadDatum()
    {
        $mockFlot = $this->flot->setMethods( array( 'makeJSArray', 'prepOpts' ) )->getMock();
        
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'getSeries', 'getName' ) )
                          ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getData', 'getTitle' ) )
                           ->getMock();
        
        $mockDatum = $this->getMockBuilder( '\Altamira\ChartDatum\Bubble' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'offsetGet', 'getLabel', 'getRenderData' ) )
                          ->getMock();
        
        $mockType = $this->getMockBuilder( '\Altamira\Type\Flot\Bubble' )
                         ->disableOriginalConstructor()
                         ->getMock();
        
        $chartRefl = new ReflectionProperty( '\Altamira\JsWriter\Flot', 'chart' );
        $chartRefl->setAccessible( true );
        $chartRefl->setValue( $mockFlot, $mockChart );
        
        $mockChart
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getName' )
            ->will       ( $this->returnValue( 'mychart' ) )
        ;
        $mockChart
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'getSeries' )
            ->will       ( $this->returnValue( array( 'mockSeriesTitle' => $mockSeries ) ) )
        ;
        $mockSeries
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'getData' )
            ->will       ( $this->returnValue( array( array( 1, 2 ) ) ) )
        ;
        
        try {
            $outputString = $mockFlot->getScript();
        } catch ( Exception $e ) { }
        
        $this->assertInstanceOf(
                'Exception',
                $e
        );
    }
}