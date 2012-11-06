<?php

class ChartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Altamira\ChartIterator::__construct
     * @covers \Altamira\ChartIterator::getLibraries
     * @covers \Altamira\ChartIterator::renderCss
     * @covers \Altamira\ChartIterator::renderLibraries
     * @covers \Altamira\ChartIterator::renderPlugins
     * @covers \Altamira\ChartIterator::renderScripts
     */
    public function testChartIterator()
    {
        $mockConfig = $this->getMock( '\Altamira\Config', array( 'offsetGet' ), array( '../altamira-config.ini' ) );
        
        $junkCharts = array( 'chart1', 'chart2' );
        
        $exception = null;
        try {
            $ci = new \Altamira\ChartIterator( $junkCharts, $mockConfig );
        } catch ( Exception $e ) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
                'UnexpectedValueException',
                $exception,
                '\Altamira\ChartIterator::__construct should test that the array passed to it contains only instances of \Altamira\Chart'
        );
        
        $mockChart1 = $this->getMock( '\Altamira\Chart', array( 'getFiles', 'getScript', 'getLibrary' ), array( 'Mock Chart 1' ) );
        $mockChart2 = $this->getMock( '\Altamira\Chart', array( 'getFiles', 'getScript', 'getLibrary' ), array( 'Mock Chart 2' ) );
        
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file1a.js', 'file1b.js' ) ) )
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file2a.js', 'file2b.js' ) ) )
        ;
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("hey");))();' ) );
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("ho");))();' ) );
        ;
        $mockChart1
            ->expects( $this->once() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart2
            ->expects( $this->once() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( \Altamira\JsWriter\JqPlot::LIBRARY ) )
        ;
        $cssPath = 'css/jqplot.css';
        $mockConfig
            ->expects( $this->at( 0 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.pluginpath' )
            ->will   ( $this->returnValue( 'js/' ) )
        ;
        $mockConfig
            ->expects( $this->at( 1 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'css.jqplotpath' )
            ->will   ( $this->returnValue( $cssPath ) )
        ;
        $mockConfig
            ->expects( $this->at( 2 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.flotpath' )
            ->will   ( $this->returnValue( 'flot.js' ) )
        ;
        $mockConfig
            ->expects( $this->at( 3 ) )
            ->method ( 'offsetGet' )
            ->with   ( 'js.jqplotpath' )
            ->will   ( $this->returnValue( 'jqplot.js' ) )
        ;
        
        $mockCharts = array( $mockChart1, $mockChart2 );
        
        $chartIterator = new \Altamira\ChartIterator( $mockCharts, $mockConfig );
        
        $plugins   = new ReflectionProperty( '\Altamira\ChartIterator', 'plugins' );
        $scripts   = new ReflectionProperty( '\Altamira\ChartIterator', 'scripts' );
        $libraries = new ReflectionProperty( '\Altamira\ChartIterator', 'libraries' );
        
        $plugins->setAccessible( true );
        $scripts->setAccessible( true );
        $libraries->setAccessible( true );
        
        $this->assertInstanceOf(
                '\Altamira\FilesRenderer',
                $plugins->getValue( $chartIterator ),
                '\Altamira\ChartIterator should create an instance of \Altamira\FilesRenderer during construction'
        );
        
        $this->assertInstanceOf(
                '\Altamira\ScriptsRenderer',
                $scripts->getValue( $chartIterator ),
                '\Altamira\ChartIterator should create an instance of \Altamira\ScriptsRenderer during construction'
        );
        
        $this->assertEquals(
                array( \Altamira\JsWriter\Flot::LIBRARY   => true, 
                       \Altamira\JsWriter\JqPlot::LIBRARY => true ),
                $libraries->getValue( $chartIterator ),
                '\Altamira\ChartIterator should unique-keyed hash table of all libraries used by all charts'
        );
        
        $expectedOutputString = "<link rel='stylesheet' type='text/css' href='{$cssPath}'></link>";
        $expectedOutputString .= "<script type='text/javascript' src='flot.js'></script>";
        $expectedOutputString .= "<script type='text/javascript' src='jqplot.js'></script>";
        
        $expectedOutputString .= <<<ENDSTRING
<script type="text/javascript" src="js/file1a.js"></script>
<script type="text/javascript" src="js/file1b.js"></script>
<script type="text/javascript" src="js/file2a.js"></script>
<script type="text/javascript" src="js/file2b.js"></script>
<script type='text/javascript'>
(function(alert("hey");))();(function(alert("ho");))();
</script>

ENDSTRING;
        
        $this->expectOutputString(
                $expectedOutputString,
                '\Altamira\ChartIterator should render libraries, CSS, and plugins'
        );
        
        $chartIterator->renderCss()
                      ->renderLibraries()
                      ->renderPlugins()
                      ->renderScripts();
        
    }
    
    /**
     * @covers \Altamira\Chart::__construct
     * @covers \Altamira\Chart::getJsWriter
     * @covers \Altamira\Chart::getName
     * @covers \Altamira\Chart::getTitle
     * @covers \Altamira\Chart::setTitle
     * @covers \Altamira\Chart::useHighlighting
     * @covers \Altamira\Chart::useZooming
     * @covers \Altamira\Chart::useCursor
     * @covers \Altamira\Chart::useDates
     * @covers \Altamira\Chart::setAxisTicks
     * @covers \Altamira\Chart::setAxisOptions
     * @covers \Altamira\Chart::setSeriesColors
     * @covers \Altamira\Chart::setAxisLabels
     * @covers \Altamira\Chart::setType
     * @covers \Altamira\Chart::setTypeOption
     * @covers \Altamira\Chart::setLegend
     * @covers \Altamira\Chart::setGrid
     * @covers \Altamira\Chart::getFiles
     * @covers \Altamira\Chart::getScript
     * @covers \Altamira\Chart::getJsWriter
     * @covers \Altamira\Chart::getLibrary
     * @covers \Altamira\Chart::getSeries
     * @covers \Altamira\Chart::addSeries
     * @covers \Altamira\Chart::addSingleSeries
     * @covers \Altamira\Chart::createSeries
     * @covers \Altamira\Chart::getDiv
     */
    public function testChart()
    {
        $exception = false;
        try {
            $chart = new \Altamira\Chart('');
        } catch ( Exception $exception ) {}
        
        $this->assertInstanceOf(
                'Exception',
                $exception,
                '\Altamira\Chart should throw an exception if it passed an empty name'
        );
        
        $jqplotChart = new \Altamira\Chart( 'chart 1' );
        $flotChart   = new \Altamira\Chart( 'chart2', \Altamira\JsWriter\Flot::LIBRARY );
        
        $libraryException = false;
        try {
            $crapChart = new \Altamira\Chart( 'chart3', 'notareallibrary' );
        } catch ( Exception $libraryException ) {}
        
        $this->assertInstanceOf(
                'Exception',
                $libraryException,
                'A chart should throw an exception if we don\'t support the library.'
        );
        
        $this->assertInstanceOf(
                '\Altamira\JsWriter\JqPlot',
                $jqplotChart->getJsWriter(),
                'Charts should register a JqPlot JsWriter by default'
        );
        
        $writermethods = array( 
                'useHighlighting', 
                'useZooming', 
                'useCursor', 
                'useDates', 
                'setAxisTicks', 
                'setAxisOptions', 
                'setOption',
                'getOption',
                'setType',
                'setTypeOption',
                'setLegend',
                'setGrid',
                'getType',
                'getFiles',
                'getScript',
                'getLibrary'
        );
                
        
        $mockJqPlotWriter = $this->getMock( '\Altamira\JsWriter\JqPlot', $writermethods, array( $jqplotChart ) );
        $mockFlotWriter   = $this->getMock( '\Altamira\JsWriter\Flot', $writermethods, array( $flotChart ) );
        
        $jsWriterReflection = new ReflectionProperty( '\Altamira\Chart', 'jsWriter' );
        $jsWriterReflection->setAccessible( true );
        $jsWriterReflection->setValue( $jqplotChart, $mockJqPlotWriter );
        $jsWriterReflection->setValue( $flotChart, $mockFlotWriter );
        
        $this->assertEquals(
                'chart_1',
                $jqplotChart->getName(),
                'Name values should be normalized to turn whitespace into underscores'
        );
        
        $flotChart->setTitle( 'This is a flot chart' );
        $this->assertEquals(
                'This is a flot chart',
                $flotChart->getTitle(),
                '\Altamira\Chart::getTitle should return title if set'
        );
        $this->assertEquals(
                'chart_1',
                $jqplotChart->getTitle(),
                '\Altamira\Chart::getTitle should return name if title not set'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'useHighlighting' )
            ->with   ( array( 'size' => 7.5 ) )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->useHighlighting(),
                '\Altamira\Chart::useHighlighting should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'useZooming' )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->useZooming(),
                '\Altamira\Chart::useZooming should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'useCursor' )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->useCursor(),
                '\Altamira\Chart::useCursor should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'useDates' )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->useDates(),
                '\Altamira\Chart::useDates should provide a fluent interface'
        );
        
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setAxisTicks' )
            ->with   ( 'x', array( 'one', 'two', 'three' ) )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setAxisTicks( 'x', array( 'one', 'two', 'three' ) ),
                '\Altamira\Chart::setAxisTicks should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setAxisOptions' )
            ->with   ( 'x', 'max', 10 )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setAxisOptions( 'x', 'max', 10 ),
                '\Altamira\Chart::setAxisOptions should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->at( 0 ) )
            ->method ( 'setOption' )
            ->with   ( 'seriesColors', array( '#333333', '#666666' ) )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setSeriesColors( array( '#333333', '#666666' ) ),
                '\Altamira\Chart::setSeriesColors should provide a fluent interface'
        );
        $mockAxisOptions =  array( 'xaxis' => array( 'min' => 0, 'max' => 10 ), 
                                   'yaxis' => array( 'min' => 0, 'max' => 10 )
                                 );
        $mockJqPlotWriter
            ->expects( $this->at( 0 ) )
            ->method ( 'getOption' )
            ->with   ( 'axes', array() )
            ->will   ( $this->returnValue( $mockAxisOptions ) );
        ;
        $mockAxisOptions['xaxis']['label'] = 'x';
        $mockJqPlotWriter
            ->expects( $this->at( 1 ) )
            ->method ( 'setOption' )
            ->with   ( 'axes', $mockAxisOptions )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setAxisLabel( 'x', 'x' ),
                '\Altamira\Chart::setAxisLabel should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setType' )
            ->with   ( 'Donut' )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setType( 'Donut' ),
                '\Altamira\Chart::setType should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setTypeOption' )
            ->with   ( 'hole', '50px', null )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setTypeOption( 'hole', '50px' ),
                '\Altamira\Chart::setTypeOption should provide a fluent interface'
        );
        $opts = array( 'on'       => 'true', 
                       'location' => 'ne', 
                       'x'        => 0, 
                       'y'        => 0
                     ); 
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setLegend' )
            ->with   ( $opts )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setLegend(),
                '\Altamira\Chart::setLegend should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'setGrid' )
            ->with   ( array( 'on' => true ) )
        ;
        $this->assertEquals(
                $jqplotChart,
                $jqplotChart->setGrid(),
                '\Altamira\Chart::setGrid should provide a fluent interface'
        );
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'getFiles' )
        ;
        $jqplotChart->getFiles();
        $mockJqPlotWriter
            ->expects( $this->once() )
            ->method ( 'getScript' )
        ;
        $jqplotChart->getScript();
        
        $seriesData = \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromXValues( array( 1, 2, 3 ) );
        $series = $jqplotChart->createSeries( $seriesData, 'seriesa', 'Donut' );
        $this->assertInstanceOf(
                '\Altamira\Series',
                $series,
                '\Altamira\Chart::createSeries should return an instance of \Altamira\Series'
        );
        $this->assertEquals(
                'seriesa',
                $series->getTitle(),
                '\Altamira\Chart::createSeries should set the series title'
        );
        $this->assertEquals(
                $seriesData,
                $series->getData(),
                '\Altamira\Chart::createSeries should set the series data'
        );
        
        $this->assertEquals(
                $flotChart,
                $flotChart->addSeries( $series ),
                '\Altamira\Chart::addSeries should provide a fluent interface and support single series' 
        );
        
        $crudArray = array( $series, 'foo' );
        
        $addException = null;
        try {
            $flotChart->addSeries( $crudArray );
        } catch ( Exception $addException ) { }
        
        $this->assertInstanceOf(
                'Exception',
                $addException,
                '\Altamira\Chart::addSeries should throw an exception if passed an array that includes a non-series value.'
        );
        
        $jqplotChart->addSingleSeries( $series );
        
        $this->assertEquals(
                array( $series->getTitle() => $series ),
                $jqplotChart->getSeries(),
                '\Altamira\Chart::getSeries should return an array containing all series that have been added'
        );

        $styleOptions = array( 'width' => '500px', 'height' => '400px' );

        $this->assertEquals(
                \Altamira\ChartRenderer::render( $flotChart, $styleOptions ),
                $flotChart->getDiv(),
                '\Altamira\Chart::getDiv() should invoke ChartRenderer::render() and pass the appropriate height and width style options.'
        );
        
        //@todo test createManySeries
    }
    
}