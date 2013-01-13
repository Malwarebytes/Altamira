<?php

class JsWriterTest extends PHPUnit_Framework_TestCase
{
    
    /**
     * @covers \Altamira\JsWriter\JsWriterAbstract::__construct
     * @covers \Altamira\JsWriter\JsWriterAbstract::initializeSeries
     * @covers \Altamira\JsWriter\JsWriterAbstract::setSeriesOption
     * @covers \Altamira\JsWriter\JsWriterAbstract::setOption
     * @covers \Altamira\JsWriter\JsWriterAbstract::getOption
     * @covers \Altamira\JsWriter\JsWriterAbstract::getSeriesOption
     * @covers \Altamira\JsWriter\JsWriterAbstract::getOptionsForSeries
     * @covers \Altamira\JsWriter\JsWriterAbstract::getLibrary
     * @covers \Altamira\JsWriter\JsWriterAbstract::setType
     * @covers \Altamira\JsWriter\JsWriterAbstract::getType
     * @covers \Altamira\JsWriter\JsWriterAbstract::getFiles
     * @covers \Altamira\JsWriter\JsWriterAbstract::getCallbackPlaceholder
     * @covers \Altamira\JsWriter\JsWriterAbstract::makeJSArray
     * @covers \Altamira\JsWriter\JsWriterAbstract::getScript
     * @covers \Altamira\JsWriter\JsWriterAbstract::getSeriesTitle
     */
    public function testParentMethods()
    {
        // automatically constructs jswriter, so we'll leverage that
        $chart = new \Altamira\Chart( 'foo', \Altamira\JsWriter\JqPlot::LIBRARY );
        $jsWriter = $chart->getJsWriter();
        
        $chartProperty = new ReflectionProperty( '\Altamira\JsWriter\JqPlot', 'chart' );
        $chartProperty->setAccessible( true );
        
        $this->assertEquals(
                $chart,
                $chartProperty->getValue( $jsWriter ),
                '\Altamira\JsWriter\JsWriterAbstract::__construct should store the chart value set during construction'
        );
        $seriesTitle = 'testTitle';
        $mockSeries = $this->getMock( '\Altamira\Series', array( 'getTitle' ), array( array(), $seriesTitle, $jsWriter ) );
        
        $mockSeries
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( $seriesTitle ) )
        ;
        $this->assertEquals(
                $jsWriter,
                $jsWriter->initializeSeries( $mockSeries ),
                '\Altamira\JsWriter\JsWriterAbstract::initializeSeries should provide a fluent interface'
        );
        
        $optionProperty = new ReflectionProperty( '\Altamira\JsWriter\JqPlot', 'options' );
        $optionProperty->setAccessible( true );
        $options = $optionProperty->getValue( $jsWriter );
        
        $this->assertArrayHasKey(
                $seriesTitle,
                $options['seriesStorage'],
                '\Altamira\JsWriter\JsWriterAbstract::initializeSeries should initialize an empty array in the jswriter\'s series storage within the options attribute'
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesOption( $seriesTitle, 'foo', 'bar' ),
                '\Altamira\JsWriter\JsWriterAbstract::setSeriesOption should provide a fluent interface'
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setOption( 'baz', 'qux' ),
                '\Altamira\JsWriter\JsWriterAbstract::setOption should provide a fluent interface'
        );
        
        $options = $optionProperty->getValue( $jsWriter );
        
        $this->assertEquals(
                'bar',
                $options['seriesStorage'][$seriesTitle]['foo'],
                '\Altamira\JsWriter\JsWriterAbstract::setSeriesOption should set a value in series storage for the provided title and key'
        );
        $this->assertEquals(
                'qux',
                $options['baz'],
                '\Altamira\JsWriter\JsWriterAbstract::setOption should set a global option in the option attribute array'
        );
        
        $this->assertEquals(
                $options['baz'],
                $jsWriter->getOption('baz')
        );
        $this->assertEquals(
                'abcdefg',
                $jsWriter->getOption( 'nonexistentkey', 'abcdefg' ),
                '\Altamira\JsWriter\JsWriterAbstract::getOption should support a variable default value'
        );
        $this->assertEquals(
                'bar',
                $jsWriter->getSeriesOption( $seriesTitle, 'foo' )
        );
        $this->assertEquals(
                array(),
                $jsWriter->getSeriesOption( $seriesTitle, 'nonexistentoption', array() ),
                '\Altamira\JsWriter\JsWriterAbstract::getSeriesOption should support a variable default value'
        );
        
        $exception = false;
        try {
            $jsWriter->getOptionsForSeries( 'this series dont exist' );
        } catch ( Exception $exception ) {}
        
        $this->assertInstanceOf(
                '\Exception',
                $exception,
                '\Altamira\JsWriter\JsWriterAbstract::getOptionsForSeries should throw an exception when asking for options for an unregistered series'
        );
        $this->assertEquals(
                $options['seriesStorage'][$seriesTitle],
                $jsWriter->getOptionsForSeries( $seriesTitle )
        );
        $this->assertEquals(
                $options['seriesStorage'][$seriesTitle],
                $jsWriter->getOptionsForSeries( $mockSeries )
        );
        $this->assertEquals(
                \Altamira\JsWriter\JqPlot::LIBRARY,
                $jsWriter->getLibrary()
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setType( 'Bar' ),
                '\Altamira\JsWriter\JsWriterAbstract::setType should provide fluent interface'
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setType( 'Bar', array(), $seriesTitle ),
                '\Altamira\JsWriter\JsWriterAbstract::setType should provide fluent interface'
        );
        
        $jsWriter->setType( 'Stacked', array(), $seriesTitle );
        
        try {
            $jsWriter->setType( 'asdfjakdfajf' );
        } catch ( Exception $typeException ) { }
        $this->assertInstanceOf(
                'Exception',
                $typeException
        );
        
        $typesProperty = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'types' );
        $typesProperty->setAccessible( true );
        $types = $typesProperty->getValue( $jsWriter );
        
        $this->assertInstanceOf(
                '\Altamira\Type\JqPlot\Bar', 
                $types['default']
        );
        $this->assertInstanceOf(
                '\Altamira\Type\JqPlot\Stacked', 
                $types[$seriesTitle]
        );
        $this->assertEquals(
                $types[$seriesTitle],
                $jsWriter->getType( $seriesTitle )
        );
        $this->assertEquals(
                $types['default'],
                $jsWriter->getType()
        );
        $this->assertNull(
                $jsWriter->getType( 'series that does not exist' )
        );

        $filesAttr = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'files' );
        $filesAttr->setAccessible( true );
        $filesAttr->setValue( $jsWriter, array( 'examplefile' ) );
        
        $this->assertEquals(
                array_merge( $filesAttr->getValue( $jsWriter ), $types['default']->getFiles(), $types[$seriesTitle]->getFiles() ),
                $jsWriter->getFiles()
        );
        
        $callbackMethod = new ReflectionMethod( '\Altamira\JsWriter\JsWriterAbstract', 'getCallbackPlaceholder' );
        $callback = '(function(){alert("hi");})()';
        $callbacks = new ReflectionProperty( '\Altamira\JsWriter\JsWriterAbstract', 'callbacks' );
        $callbacks->setAccessible( true );
        $callbackMethod->setAccessible( true );
        $signature = spl_object_hash( $jsWriter ) . '_0';
        
        $this->assertEquals(
                $signature,
                $callbackMethod->invoke( $jsWriter, $callback )
        ); 
        
        $callbackArray = $callbacks->getValue( $jsWriter );
        $this->assertEquals(
                $callback,
                $callbackArray[$signature]
        );
        
        $jsvals = array( 'foo' => $signature,
                         'bar' => '#$.jqplot.DateAxisRenderer#',
                         'baz' => 'qux'
                       );
        
        $expectedJson = <<<JSON
{"foo":(function(){alert("hi");})(),"bar":$.jqplot.DateAxisRenderer,"baz":"qux"}
JSON;
        
        $reflMake = new ReflectionMethod( '\Altamira\JsWriter\JqPlot', 'makeJSArray' );
        $reflMake->setAccessible( true );
        $this->assertEquals(
                $expectedJson,
                $reflMake->invoke( $jsWriter, $jsvals),
                '\Altamira\JsWriter\JsWriterAbstract::makeJSArray should json-encode an array, replacing callbacks and properly evaluating values wrapped in hashes'
        );
        $this->assertNotEmpty(
                $jsWriter->getScript()
        );
        $this->assertNotEmpty(
            $jsWriter->getFiles()
        );
        
        $getSeriesTitle = new ReflectionMethod( '\Altamira\JsWriter\JsWriterAbstract', 'getSeriesTitle' );
        $getSeriesTitle->setAccessible( true );
        
        $this->assertEquals(
                $seriesTitle,
                $getSeriesTitle->invoke( $jsWriter, $mockSeries ),
                '\Altamira\JsWriter\JsWriterAbstract::getSeriesTitle should return the title string of a series, if passed to it'
        );
        $this->assertEquals(
                $seriesTitle,
                $getSeriesTitle->invoke( $jsWriter, $seriesTitle ),
                '\Altamira\JsWriter\JsWriterAbstract::getSeriesTitle should return a string if passed to it'
        );
        
    }
    
    /**
     * @covers \Altamira\JsWriter\JqPlot::setAxisTicks
     * @covers \Altamira\JsWriter\JqPlot::setSeriesMarkerSize
     * @covers \Altamira\JsWriter\JqPlot::setSeriesMarkerStyle
     * @covers \Altamira\JsWriter\JqPlot::setSeriesShowMarker
     * @covers \Altamira\JsWriter\JqPlot::setSeriesShowLine
     * @covers \Altamira\JsWriter\JqPlot::setSeriesLineWidth
     * @covers \Altamira\JsWriter\JqPlot::setSeriesLabelSetting
     * @covers \Altamira\JsWriter\JqPlot::useSeriesLabels
     * @covers \Altamira\JsWriter\JqPlot::setSeriesOption
     * @covers \Altamira\JsWriter\JqPlot::setSeriesLabelSetting
     * @covers \Altamira\JsWriter\JqPlot::getOptionsJS
     * @covers \Altamira\JsWriter\JqPlot::setType
     * @covers \Altamira\JsWriter\JqPlot::useDates
     * @covers \Altamira\JsWriter\JqPlot::setAxisOptions
     * @covers \Altamira\JsWriter\JqPlot::setLegend
     * @covers \Altamira\JsWriter\JqPlot::setFill
     * @covers \Altamira\JsWriter\JqPlot::setGrid
     * @covers \Altamira\JsWriter\JqPlot::setShadow
     * @covers \Altamira\JsWriter\JqPlot::useCursor
     * @covers \Altamira\JsWriter\JqPlot::useZooming
     * @covers \Altamira\JsWriter\JqPlot::useHighlighting
     * @covers \Altamira\JsWriter\JqPlot::getScript
     */
    public function testJqPlot() 
    {
        $chart = new \Altamira\Chart( 'foo', \Altamira\JsWriter\JqPlot::LIBRARY );
        $jsWriter = $chart->getJsWriter();
        
        $seriesTitle = 'testTitle';
        
        $mockSeries = $this->getMock( '\Altamira\Series', array( 'getTitle', 'getData' ), array( array(), $seriesTitle, $jsWriter ) );
        
        $chart->addSeries( $mockSeries );
        
        $mockDatum = $this->getMock( '\Altamira\ChartDatum\TwoDimensionalPoint', array(), array( array( 'x' => 1, 'y' => 2 ) ) );
        
        $mockSeries
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( $seriesTitle ) )
        ;
        $mockSeries
            ->expects( $this->any() )
            ->method ( 'getData' )
            ->will   ( $this->returnValue( array( $mockDatum ) ) )
        ;
        $this->assertEquals(
                $jsWriter,
                $jsWriter->initializeSeries( $mockSeries ),
                '\Altamira\JsWriter\JsWriterAbstract::initializeSeries should provide a fluent interface'
        );
        
        $labels = array( 'label1', 'label2' );
        $ticks = array( 'foo', 'bar', 'baz' );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setAxisTicks( 'x', $ticks )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesMarkerSize( $seriesTitle, 20 )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesMarkerStyle( $seriesTitle, 'diamond' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesShowMarker( $seriesTitle, true )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesShowLine( $seriesTitle, true )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesLineWidth( $seriesTitle, 10 )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesLabelSetting( $seriesTitle, 'location', 'nw' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesLabelSetting( $seriesTitle, 'xpadding', '10' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesLabelSetting( $seriesTitle, 'notavalidlabelsetting', '10' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->useSeriesLabels( $seriesTitle, $labels )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setSeriesOption( $seriesTitle, 'foo', 'bar' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->useDates( 'y' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setAxisOptions( 'x', 'numberTicks', 20 )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setAxisOptions( 'x', 'formatString', '%d' )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setLegend( array('on'       => 'true', 
                                           'location' => 'ne', 
                                           'x'        => 5, 
                                           'y'        => 10 ) )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setFill( $seriesTitle, array( 'use' => true, 'color' => '#333', 'stroke' => true, 'alpha' => '0.6' ) )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setGrid( array( 'on' => true, 'color' => '#333', 'background' => '#fff' ) )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setShadow( $seriesTitle )
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->useCursor()
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->useZooming()
        );
        $this->assertEquals(
                $jsWriter,
                $jsWriter->useHighlighting( array( 'size' => 10 ) )
        );
        
        
        $optionAttr = new ReflectionProperty( '\Altamira\JsWriter\JqPlot', 'options' );
        $optionAttr->setAccessible( true );
        $options = $optionAttr->getValue( $jsWriter );
        
        $this->assertEquals(
                $ticks,
                $options['axes']['xaxis']['ticks']
        );
        $this->assertEquals(
                20,
                $options['seriesStorage'][$seriesTitle]['markerOptions']['size']
        );
        $this->assertEquals(
                'diamond',
                $options['seriesStorage'][$seriesTitle]['markerOptions']['style']
        );
        $this->assertTrue(
                $options['seriesStorage'][$seriesTitle]['showMarker']
        );
        $this->assertTrue(
                $options['seriesStorage'][$seriesTitle]['showLine']
        );
        $this->assertEquals(
                10,
                $options['seriesStorage'][$seriesTitle]['lineWidth']
        );
        $this->assertEquals(
                'nw',
                $options['seriesStorage'][$seriesTitle]['pointLabels']['location']
        );
        $this->assertEquals(
                10,
                $options['seriesStorage'][$seriesTitle]['pointLabels']['xpadding']
        );
        $this->assertEquals(
                true,
                $options['seriesStorage'][$seriesTitle]['pointLabels']['show']
        );
        $this->assertEquals(
                3,
                $options['seriesStorage'][$seriesTitle]['pointLabels']['edgeTolerance']
        );
        $this->assertArrayNotHasKey(
                'notavalidlabelsetting',
                $options['seriesStorage'][$seriesTitle]['pointLabels']
        );
        $this->assertEquals(
                'bar',
                $options['seriesStorage'][$seriesTitle]['foo']
        );
        $this->assertArrayHasKey(
                'formatString',
                $options['axes']['xaxis']['tickOptions']
        );
        $this->assertArrayHasKey(
                'numberTicks',
                $options['axes']['xaxis']
        );
        $this->assertArrayHasKey(
                'legend',
                $options
        );
        $this->assertArrayHasKey(
                'cursor',
                $options
        );
        $this->assertArrayHasKey(
                'show',
                $options['cursor']
        );
        $this->assertArrayHasKey(
                'zoom',
                $options['cursor']
        );
        
        $this->assertArrayHasKey(
                'location',
                $options['legend']
        );
        $this->assertArrayHasKey(
                'location',
                $options['legend']
        );
        $this->assertArrayHasKey(
                'fill',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'fillAndStroke',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'fillColor',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'fillAlpha',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'shadow',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'shadowAngle',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'shadowOffset',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'shadowDepth',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'shadowAlpha',
                $options['seriesStorage'][$seriesTitle]
        );
        $this->assertArrayHasKey(
                'drawGridLines',
                $options['grid']
        );
        $this->assertArrayHasKey(
                'gridLineColor',
                $options['grid']
        );
        $this->assertArrayHasKey(
                'background',
                $options['grid']
        );
        $this->assertArrayHasKey(
                'highlighter',
                $options
        );
        $this->assertArrayHasKey(
                'sizeAdjust',
                $options['highlighter']
        );
        
        $this->assertContains(
                'jqplot.dateAxisRenderer.js',
                $jsWriter->getFiles(),
                '\Altamira\JsWriter\JqPlot::useDates should add the date axis renderer to the files array'
        );
        $this->assertEquals(
                '#$.jqplot.DateAxisRenderer#',
                $options['axes']['yaxis']['renderer']
        );
        
        $reflGet = new ReflectionMethod( '\Altamira\JsWriter\JqPlot', 'getOptionsJs' );
        $reflGet->setAccessible( true );
        $optionsJs = $reflGet->invoke( $jsWriter );
        
        $modelOptions = $options;
        $modelOptions['series'] = $modelOptions['seriesStorage'];
        unset( $modelOptions['seriesStorage'] );
        
        $options = new ReflectionProperty( '\Altamira\JsWriter\JqPlot', 'options' );
        $options->setAccessible( true );
        $optionsResult = $options->getValue( $jsWriter );
        
        $jsWriter->setLegend( array( 'on' => true, 'location' => 'outside' ) );
        $options = $optionAttr->getValue( $jsWriter );
        $this->assertArrayHasKey(
                'placement',
                $options['legend']
        );
        
        $jsWriter->setLegend( array( 'on' => false ) );
        $options = $optionAttr->getValue( $jsWriter );
        $this->assertArrayNotHasKey(
                'legend',
                $options
        );
        
        $output = $jsWriter->getScript();
        
        
        $this->assertContains(
                'plot = $.jqplot(',
                $output
        );
        
        $this->assertContains(
                'plot_'.$chart->getName().'_1',
                $output
        );
        
        
        $this->assertEquals(
                $jsWriter,
                $jsWriter->setType( 'Bar', array( 'horizontal' => true ) )
        );
    }
    
    /**
     * @covers \Altamira\JsWriter\JqPlot::getOptionsJS
     */
    public function testJqPlotJsArrayHidesTitleWhenRequired()
    {
        $chart = new \Altamira\Chart( 'foo', \Altamira\JsWriter\JqPlot::LIBRARY );
        $chart->setTitle( "I am a title" );
        
        $reflGet = new ReflectionMethod( '\Altamira\JsWriter\JqPlot', 'getOptionsJs' );
        $reflGet->setAccessible( true );
        
        $origJs = json_decode( $reflGet->invoke( $chart->getJsWriter() ), true );
        $this->assertArrayHasKey(
                'title',
                $origJs,
                '\Altamira\JsWriter\JqPlot::getOptionsJS should encode a value for the chart title by default'
        );
        
        $newJs = json_decode( $reflGet->invoke( $chart->hideTitle()->getJsWriter() ), true );
        $this->assertArrayNotHasKey(
                'title',
                $newJs,
                '\Altamira\JsWriter\JqPlot::getOptionsJS should not encode a value for the chart title if the chart is hiding its title'
        );
    }
}