<?php

class ChartTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        \Altamira\Config::setConfigFile( __DIR__ .'/altamira-config.test.ini' );
    }

    /**
     * @covers \Altamira\ChartIterator::__construct
     * @covers \Altamira\ChartIterator::getLibraries
     * @covers \Altamira\ChartIterator::renderCss
     * @covers \Altamira\ChartIterator::renderLibraries
     * @covers \Altamira\ChartIterator::renderPlugins
     * @covers \Altamira\ChartIterator::renderScripts
     * @covers \Altamira\ChartIterator::getPlugins
     * @covers \Altamira\ChartIterator::getScripts
     * @covers \Altamira\ChartIterator::getCSSPath
     */
    public function testChartIterator()
    {
        $junkCharts = array( 'chart1', 'chart2' );
        
        $exception = null;
        try {
            $ci = new \Altamira\ChartIterator( $junkCharts );
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
            ->expects( $this->any() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file1a.js', 'file1b.js' ) ) )
        ;
        $mockChart2
            ->expects( $this->any() )
            ->method ( 'getFiles' )
            ->will   ( $this->returnValue( array( 'file2a.js', 'file2b.js' ) ) )
        ;
        $mockChart1
            ->expects( $this->any() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("hey");))();' ) );
        ;
        $mockChart2
            ->expects( $this->any() )
            ->method ( 'getScript' )
            ->will   ( $this->returnValue( '(function(alert("ho");))();' ) );
        ;
        $mockChart1
            ->expects( $this->any() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart2
            ->expects( $this->any() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( \Altamira\JsWriter\JqPlot::LIBRARY ) )
        ;
        $cssPath = 'css/jquery.jqplot.css';
        
        $mockCharts = array( $mockChart1, $mockChart2 );
        
        $chartIterator = new \Altamira\ChartIterator( $mockCharts );
        
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
        $expectedOutputString .= "<script type='text/javascript' src='jquery.flot.js'></script>";
        $expectedOutputString .= "<script type='text/javascript' src='jquery.jqplot.js'></script>";
        
        $expectedOutputString .= <<<ENDSTRING
<script type="text/javascript" src="file1a.js"></script>
<script type="text/javascript" src="file1b.js"></script>
<script type="text/javascript" src="file2a.js"></script>
<script type="text/javascript" src="file2b.js"></script>
<script type='text/javascript'>
(function(alert("hey");))();(function(alert("ho");))();
</script>

ENDSTRING;
        
        $this->expectOutputString(
                $expectedOutputString,
                '\Altamira\ChartIterator should render libraries, CSS, and plugins'
        );

        $plugins = new ReflectionProperty( '\Altamira\ChartIterator', 'plugins' );
        $plugins->setAccessible( true );

        $this->assertEquals(
                (array) $plugins->getValue( $chartIterator ),
                $chartIterator->getPlugins()
        );
        
        $this->assertEquals(
                $cssPath,
                $chartIterator->getCSSPath()
        );

        $this->assertEquals(
                $chartIterator,
                $chartIterator->renderCss()
        );
        $this->assertEquals(
                $chartIterator,
                $chartIterator->renderLibraries()
        );
        $this->assertEquals(
                $chartIterator,
                $chartIterator->renderPlugins()
        );
        $this->assertEquals(
                $chartIterator,
                $chartIterator->renderScripts()
        );
        
        $chartIterator2 =  new \Altamira\ChartIterator( $mockCharts );
        $this->assertEquals(
                "<script type='text/javascript'>\n(function(alert(\"hey\");))();\n</script>\n<script type='text/javascript'>\n(function(alert(\"ho\");))();\n</script>\n",
                $chartIterator2->getScripts()
        );
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
     * @covers \Altamira\Chart::setAxisLabel
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
     * @covers \Altamira\Chart::createManySeries
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
        
        $jqplotChart->addSeries( $series );
        $seriesArray = $jqplotChart->getSeries();
        $this->assertEquals(
                array( $series->getTitle() => $series ),
                $seriesArray
        );
        
        $this->assertArrayHasKey(
                $series->getTitle(),
                $seriesArray
        );
        
        $this->assertEquals(
                $jqplotChart->getLibrary(),
                $mockJqPlotWriter->getLibrary()
        );
        
        $styleOptions = array( 'width' => '100px', 'height' => '200px' );
        $this->assertEquals(
                \Altamira\ChartRenderer::render( $jqplotChart, $styleOptions ),
                $jqplotChart->getDiv( 100, 200 )
        );
    }
    
    /**
     * @covers \Altamira\Chart::addSeries
     */
    public function testChartAddSeriesAsArray()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'addSingleSeries' ) )
                           ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $mockChart
            ->expects   ( $this->once() )
            ->method    ( 'addSingleSeries' )
            ->with      ( $mockSeries ) 
        ;
        
        try {
            $mockChart->addSeries( array( $mockSeries, 'foo' ) );
        } catch ( \UnexpectedValueException $e ) { }
        
        $this->assertInstanceOf(
                '\UnexpectedValueException',
                $e,
                '\Altamira\Chart should throw an unexpected value exception if a non-chart has been passed to addSeries'
        );
    }

    /**
     * @covers \Altamira\Chart::addSeries
     */
    public function testChartAddSeriesAsSingleton()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'addSingleSeries' ) )
                           ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $mockChart
            ->expects   ( $this->once() )
            ->method    ( 'addSingleSeries' )
            ->with      ( $mockSeries ) 
        ;
        
        $mockChart->addSeries( $mockSeries );
    }
 
    /**
     * @covers \Altamira\Chart::createManySeries
     */
    public function testCreateManySeriesDefault()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'createSeries' ) )
                           ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $dataset  = array( array( 1, 2 ), array( 3, 4 ) );
        $title    = 'title';
        $type     = 'Bar';
        $factory  = array( '\Altamira\ChartDatum\TwoDimensionalPointFactory', 'getFromNested' );
        
        $expectedPoints = array();
        \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromNested( array( $dataset[0] ), $expectedPoints );
        \Altamira\ChartDatum\TwoDimensionalPointFactory::getFromNested( array( $dataset[1] ), $expectedPoints );

        $mockChart
            ->expects    ( $this->once() )
            ->method     ( 'createSeries' )
            ->with       ( $expectedPoints, $title, $type )
            ->will       ( $this->returnValue( $mockSeries ) )
        ;
        
        $this->assertEquals(
                $mockSeries,
                $mockChart->createManySeries( $dataset, $factory, $title, $type )
        );
    }
    
    /**
     * @covers \Altamira\Chart::createManySeries
     */
    public function testCreateManySeriesFlot()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'createSeries' ) )
                           ->getMock();
        
        $mockSeriesA = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $mockSeriesB = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $mockJsWriter = $this->getMockBuilder( '\Altamira\JsWriter\Flot' )
                             ->disableOriginalConstructor()
                             ->setMethods( array( 'getType' ) )
                             ->getMock();
        
        $dataset  = array( array( 1, 2 ), array( 3, 4 ) );
        $title    = 'title';
        $type     = 'Bar';
        $factory  = array( '\Altamira\ChartDatum\TwoDimensionalPointFactory', 'getFromNested' );
        
        
        $refl = new ReflectionProperty( '\Altamira\Chart', 'jsWriter' );
        $refl->setAccessible( true );
        $refl->setValue( $mockChart, $mockJsWriter );
        
        $mockChart
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'createSeries' )
            ->will       ( $this->returnValue( $mockSeriesA ) )
        ;
        $mockChart
            ->expects    ( $this->at( 1 ) )
            ->method     ( 'createSeries' )
            ->will       ( $this->returnValue( $mockSeriesB ) )
        ;
        
        $this->assertEquals(
                array( $mockSeriesA, $mockSeriesB ),
                $mockChart->createManySeries( $dataset, $factory, $title, $type )
        );
    }
    
    
    /**
     * @covers \Altamira\Chart::createManySeries
     */
    public function testCreateManySeriesException()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'createSeries' ) )
                           ->getMock();
        
        $mockSeries = $this->getMockBuilder( '\Altamira\Series' )
                           ->disableOriginalConstructor()
                           ->getMock();
        
        $mockJsWriter = $this->getMockBuilder( '\Altamira\JsWriter\Flot' )
                             ->disableOriginalConstructor()
                             ->setMethods( array( 'getType' ) )
                             ->getMock();
        
        $mockJsWriter
            ->expects    ( $this->once() )
            ->method     ( 'getType' )
            ->will       ( $this->returnValue( 'Donut' ) )
        ;
        
        $refl = new ReflectionProperty( '\Altamira\Chart', 'series' );
        $refl->setAccessible( true );
        $refl->setValue( $mockChart, array( $mockSeries ) );
        
        $refl = new ReflectionProperty( '\Altamira\Chart', 'jsWriter' );
        $refl->setAccessible( true );
        $refl->setValue( $mockChart, $mockJsWriter );
        
        $dataset  = array( array( 1, 2 ), array( 3, 4 ) );
        $title    = 'title';
        $type     = 'Bar';
        $factory  = array( '\Altamira\ChartDatum\TwoDimensionalPointFactory', 'getFromNested' );
        
        try {
            $mockChart->createManySeries( $dataset, $factory, $title, $type );
        } catch ( Exception $e ) { }
        
        $this->assertInstanceOf(
                'Exception', 
                $e,
                '\Altamira\Chart::createManySeries should throw an exception if we are trying to use it with a donut flot'
        );
    }
    
    /**
     * @covers \Altamira\Chart::hideTitle
     * @covers \Altamira\Chart::titleHidden
     */
    public function testChartHidesTitle()
    {
        $mockChart = $this->getMockBuilder( '\Altamira\Chart' )
                          ->disableOriginalConstructor()
                          ->setMethods( array( 'foo' ) )
                          ->getMock();
        
        $reflHidden = new ReflectionProperty( '\Altamira\Chart', 'titleHidden' );
        $reflHidden->setAccessible( true );
        
        $this->assertFalse(
                $reflHidden->getValue( $mockChart ),
                '\Altamira\Chart::$titleHidden should default to false'
        );
        $this->assertFalse(
                $mockChart->titleHidden(),
                '\Altamira\Chart::titleHidden should return the value of \Altamira\Chart::$titleHidden'
        );
        $this->assertEquals(
                $mockChart,
                $mockChart->hideTitle()
        );
        $this->assertTrue(
                $reflHidden->getValue( $mockChart ),
                '\Altamira\Chart::$titleHidden should be set to true by \Altamira\Chart::hideTitle'
        );
        $this->assertTrue(
                $mockChart->titleHidden(),
                '\Altamira\Chart::titleHidden should return the value of \Altamira\Chart::$titleHidden'
        );
    }
}