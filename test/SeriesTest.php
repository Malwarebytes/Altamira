<?

use Altamira\Series;
use Altamira\ChartDatum\TwoDimensionalPointFactory;

class SeriesTest extends PHPUnit_Framework_TestCase
{
    protected $data;
    protected $mockJqPlotWriter;
    protected $mockFlotWriter;
    protected $flotSeries;
    protected $jqPlotSeries;
    
    public function setUp()
    {
        $data = array();
        for ( $i = 1; $i <= 10; $i++ )
        {
            $data[] = $i;
        } 
        
        $jsWriterMethods = array(
                'initializeSeries', 
                'setShadow', 
                'setFill', 
                'useSeriesLabels', 
                'setSeriesLabelSetting', 
                'setSeriesOption', 
                'getSeriesOption', 
                'getOptionsForSeries', 
                'setSeriesLineWidth', 
                'setSeriesShowLine', 
                'setSeriesShowMarker', 
                'setSeriesMarkerStyle', 
                'setSeriesMarkerSize', 
                'setType'
        ); 
        
        $mockChart                 = $this->getMock( '\Altamira\Chart', array(), array( 'mockChart' ) );
        $this->data                = TwoDimensionalPointFactory::getFromXValues( $data );
        $this->mockJqPlotWriter    = $this->getMock( '\Altamira\JsWriter\JqPlot', $jsWriterMethods, array( $mockChart ) );
        $this->mockFlotWriter      = $this->getMock( '\Altamira\JsWriter\JqPlot', $jsWriterMethods, array( $mockChart ) );
        $this->jqPlotSeries        = new Series( $this->data, 'jqPlot', $this->mockJqPlotWriter );
        $this->flotSeries          = new Series( $this->data, 'Flot', $this->mockFlotWriter );
        
    }
    
    /**
     * @covers \Altamira\Series::__construct
     * @covers \Altamira\Series::getTitle
     * @covers \Altamira\Series::getData
     * @covers \Altamira\Series::setTitle
     */
    public function testConstruct()
    {
        $exception = false;
        try {
            $crapSeries = new Series( array(1, 2, 3), 'Foo', $this->mockJqPlotWriter );
        } catch (Exception $e) {
            $exception = $e;
        }
        
        $this->assertInstanceOf(
                '\UnexpectedValueException', 
                $exception,
                'A series should throw an exception if it is passed data that is not formatted into ChartDatumAbstract'
        );
        
        $this   ->mockJqPlotWriter
                ->expects          ( $this->once() )
                ->method           ( 'initializeSeries' );
        
        $series = new Series( $this->data, 'Foo', $this->mockJqPlotWriter );
        
        $this->assertEquals(
                $this->data,
                $series->getData(),
                'A series should return the data that has been passed to it during instantiation.'
        );
        
        $datumJsWriterProperty  = new ReflectionProperty('\Altamira\ChartDatum\ChartDatumAbstract', 'jsWriter');
        $datumSeriesProperty    = new ReflectionProperty('\Altamira\ChartDatum\ChartDatumAbstract', 'series');
        $seriesJsWriterProperty = new ReflectionProperty('\Altamira\Series', 'jsWriter');
        $datumJsWriterProperty->setAccessible( true );
        $datumSeriesProperty->setAccessible( true );
        $seriesJsWriterProperty->setAccessible( true );
        
        foreach( $series->getData() as $datum ) {
            $this->assertEquals(
                    $this->mockJqPlotWriter,
                    $datumJsWriterProperty->getValue( $datum ),
                    'A series should inject its JsWriter into each datum.'
            );
            $this->assertEquals(
                    $series,
                    $datumSeriesProperty->getValue( $datum ),
                    'A series should inject itself into each datum.'
            );
        }
        
        $this->assertEquals(
                'Foo',
                $series->getTitle(),
                'Series title should be set during construct, and accessible via Series::getTitle'
        );
        
        $this->assertEquals(
                $this->mockJqPlotWriter,
                $seriesJsWriterProperty->getValue( $series ),
                'A series should store the JsWriter passed to it in the JsWriter property.'
        );
        
        $series->setTitle( 'Bar' );
        
        $this->assertEquals(
                'Bar',
                $series->getTitle(),
                '\Altamira\Series::setTitle() should set at title that can be retrieved with \Altamira\Series::getTitle()'
        ); 
        
    }
    
    /**
     * @covers \Altamira\Series::setShadow
     * @covers \Altamira\Series::setFill
     * @covers \Altamira\Series::setLabelSetting
     * @covers \Altamira\Series::setOption
     * @covers \Altamira\Series::getOption
     * @covers \Altamira\Series::getOptions
     * @covers \Altamira\Series::setLineWidth
     * @covers \Altamira\Series::showLine
     * @covers \Altamira\Series::showMarker
     * @covers \Altamira\Series::setMarkerStyle
     * @covers \Altamira\Series::setMarkerSize
     * @covers \Altamira\Series::setType
     */
    public function testSettersAndGetters()
    {
        $setShadowDefaultVals = array( 'use'    =>    true, 
                                       'angle'  =>    45, 
                                       'offset' =>    1.25, 
                                       'depth'  =>    3, 
                                       'alpha'  =>    0.1 );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setShadow' )
                ->with              ( $this->jqPlotSeries->getTitle(), $setShadowDefaultVals )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setShadow' )
               ->with               ( $this->flotSeries->getTitle(), $setShadowDefaultVals )
        ;
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setShadow(),
                'Series::setShadow() should provide a fluent interface'
        );
        $this->jqPlotSeries->setShadow();

        $setFillDefaults =  array( 'use'    => true, 
                                   'stroke' => false, 
                                   'color'  => null, 
                                   'alpha'  => null );

        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setFill' )
                ->with              ( $this->jqPlotSeries->getTitle(), $setFillDefaults )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setFill' )
               ->with               ( $this->flotSeries->getTitle(), $setFillDefaults )
        ;
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setFill(),
                'Series::setFill() should provide a fluent interface'
        );  
        $this->jqPlotSeries->setFill();
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesLabelSetting' )
                ->with              ( $this->jqPlotSeries->getTitle(), 'foo', 'bar' )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesLabelSetting' )
               ->with               ( $this->flotSeries->getTitle(), 'foo', 'bar' )
        ;
        
        $this->assertEquals(
                $this->jqPlotSeries,
                $this->jqPlotSeries->setLabelSetting( 'foo', 'bar' ),
                '\Altamira\Series::setLabelSetting() should provide fluent interface'
        );
        
        $this->flotSeries->setLabelSetting( 'foo', 'bar' );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesOption' )
                ->with              ( $this->jqPlotSeries->getTitle(), 'foo', 'bar' )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesOption' )
               ->with               ( $this->flotSeries->getTitle(), 'foo', 'bar' )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setOption( 'foo', 'bar' ),
                '\Altamira\Series::setOption() should provide fluent interface'
        );
        $this->jqPlotSeries->setOption( 'foo', 'bar' );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'getSeriesOption' )
                ->with              ( $this->jqPlotSeries->getTitle(), 'foo' )
                ->will              ( $this->returnValue( 'bar' ) )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'getSeriesOption' )
               ->with               ( $this->flotSeries->getTitle(), 'foo' )
               ->will               ( $this->returnValue( 'bar' ) )
        ;
        
        $this->assertEquals(
                'bar',
                $this->jqPlotSeries->getOption( 'foo' ),
                '\Altamira\Series::getOption() should return the value for an option that has been set'
        );
        $this->assertEquals(
                'bar',
                $this->flotSeries->getOption( 'foo' ),
                '\Altamira\Series::getOption() should return the value for an option that has been set'
        );
        
        $mockOptions = array( 'foo' => 'bar' );
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'getOptionsForSeries' )
                ->with              ( $this->jqPlotSeries->getTitle() )
                ->will              ( $this->returnValue( $mockOptions ) )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'getOptionsForSeries' )
               ->with               ( $this->flotSeries->getTitle() )
               ->will               ( $this->returnValue( $mockOptions ) )
        ;
        
        $this->assertEquals(
                $mockOptions,
                $this->flotSeries->getOptions(),
                '\Altamira\Series::getOptions() should return the appropriately keyed value for \Altamira\JsWriter\JsWriterAbstract::getOptionsForSeries()'
        );
        $this->assertEquals(
                $mockOptions,
                $this->jqPlotSeries->getOptions(),
                '\Altamira\Series::getOptions() should return the appropriately keyed value for \Altamira\JsWriter\JsWriterAbstract::getOptionsForSeries()'
        );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesLineWidth' )
                ->with              ( $this->jqPlotSeries->getTitle(), 1 )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesLineWidth' )
               ->with               ( $this->flotSeries->getTitle(), 1 )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setLineWidth( 1 ),
                '\Altamira\Series::setLineWidth should provide fluent interface'
        );
        $this->jqPlotSeries->setLineWidth( 1 );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesShowLine' )
                ->with              ( $this->jqPlotSeries->getTitle(), true )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesShowLine' )
               ->with               ( $this->flotSeries->getTitle(), true )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->showLine( true ),
                '\Altamira\Series::showLine should provide fluent interface'
        );
        $this->jqPlotSeries->showLine( 1 );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesShowMarker' )
                ->with              ( $this->jqPlotSeries->getTitle(), true )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesShowMarker' )
               ->with               ( $this->flotSeries->getTitle(), true )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->showMarker( true ),
                '\Altamira\Series::showMarker should provide fluent interface'
        );
        $this->jqPlotSeries->showMarker( 1 );        
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesMarkerStyle' )
                ->with              ( $this->jqPlotSeries->getTitle(), 'diamond' )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesMarkerStyle' )
               ->with               ( $this->flotSeries->getTitle(), 'diamond' )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setMarkerStyle( 'diamond' ),
                '\Altamira\Series::setMarkerStyle should provide fluent interface'
        );
        $this->jqPlotSeries->setMarkerStyle( 'diamond' );   
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setSeriesMarkerSize' )
                ->with              ( $this->jqPlotSeries->getTitle(), '5px' )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setSeriesMarkerSize' )
               ->with               ( $this->flotSeries->getTitle(), '5px' )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setMarkerSize( '5px' ),
                '\Altamira\Series::setMarkerSize should provide fluent interface'
        );
        $this->jqPlotSeries->setMarkerSize( '5px' );   
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'setType' )
                ->with              ( 'Bubble', $this->jqPlotSeries->getTitle() )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'setType' )
               ->with               ( 'Bubble', $this->flotSeries->getTitle() )
        ;
        
        $this->assertEquals(
                $this->flotSeries,
                $this->flotSeries->setType( 'Bubble' ),
                '\Altamira\Series::setType should provide fluent interface'
        );
        $this->jqPlotSeries->setType( 'Bubble' );
        
    }
    
    /**
     * @covers \Altamira\Series::useLabels
     */
    public function testUseLabels()
    {
        $labels = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g' );
        
        $this   ->mockJqPlotWriter
                ->expects           ( $this->once() )
                ->method            ( 'useSeriesLabels' )
                ->with              ( $this->jqPlotSeries->getTitle(), $labels )
        ;
        $this  ->mockFlotWriter
               ->expects            ( $this->once() )
               ->method             ( 'useSeriesLabels' )
               ->with               ( $this->flotSeries->getTitle(), $labels )
        ;
        
        $this->assertEquals(
                $this->jqPlotSeries,
                $this->jqPlotSeries->useLabels( $labels ),
                '\Altamira\Series::useLabels() should provide a fluent interface'
        );
        $this->flotSeries->useLabels( $labels );
    }
    
}