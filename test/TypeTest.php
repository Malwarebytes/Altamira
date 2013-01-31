<?php

class TypeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @covers \Altamira\Type\TypeAbstract::configure
     * @covers \Altamira\Type\TypeAbstract::__construct
     * @covers \Altamira\Type\TypeAbstract::getFiles
     */
    public function testAbstractConfigure()
    {
        $mockJqPlot = $this->getMockBuilder( '\Altamira\JsWriter\JqPlot' )
                           ->disableOriginalConstructor()
                           ->setMethods( array( 'getLibrary' ) )
                           ->getMock();
        
        $mockJqPlot
            ->expects    ( $this->any() )
            ->method     ( 'getLibrary' )
            ->will       ( $this->returnValue( \Altamira\JsWriter\JqPlot::LIBRARY ) )
        ;
        
        $bar = $this->getMock( '\Altamira\Type\JqPlot\Bar', array( 'foo' ), array( $mockJqPlot ) );

        $config = new ReflectionMethod( '\Altamira\Type\TypeAbstract', 'configure' );
        $config->setAccessible( true );
        $config->invoke( $bar, $mockJqPlot );
        
        $pluginFiles = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'pluginFiles' );
        $pluginFiles->setAccessible( true );
        
        $this->assertEquals(
                $pluginFiles->getValue( $bar ),
                $bar->getFiles()
        );
    }
    
    /**
     * @covers \Altamira\Type\TypeAbstract::getRenderer
     * @covers \Altamira\Type\TypeAbstract::getOptions
     * @covers \Altamira\Type\TypeAbstract::getRenderer
     * @covers \Altamira\Type\TypeAbstract::getSeriesOptions
     * @covers \Altamira\Type\TypeAbstract::getRendererOptions
     */
    public function testAbstractAccessors()
    {
        $abstract = $this->getMockBuilder( '\Altamira\Type\TypeAbstract' )
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass();
        
        $options = array( 'foo' => 'bar' );
        $optionsVal = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'options' );
        $optionsVal->setAccessible( true );
        $optionsVal->setValue( $abstract, $options );
        
        $this->assertEquals(
                $options,
                $abstract->getOptions(),
                '\Altamira\Type\TypeAbstract::getOptions should simply return the options property'
        );
        
        $this->assertNull(
                $abstract->getRenderer(),
                'A type without a renderer set should not wrap null in hashes; it should just return null'
        );
        
        $renderer = 'jqplot.renderer.js';
        $rendererVal = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'renderer' );
        $rendererVal->setAccessible( true );
        $rendererVal->setValue( $abstract, $renderer );
        
        $this->assertEquals(
                '#'.$renderer.'#',
                $abstract->getRenderer(),
                '\Altamira\Type\TypeAbstract should return the renderer wrapped in hashes for later parsing'
        );
        $this->assertEquals(
                array(),
                $abstract->getSeriesOptions(),
                '\Altamira\Type\TypeAbstract::getSeriesOptions should return an empty array if there are no series options'
        );
        
        $seriesOptions = array( 'baz' => 'qux' );
        $options['series'] = $seriesOptions;
        $options['rendereroption'] = 'whatever';
        $optionsVal->setValue( $abstract, $options );
        
        $this->assertEquals(
                $seriesOptions,
                $abstract->getSeriesOptions(),
                '\Altamira\Type\TypeAbstract::getSeriesOptions should return an empty array if there are no series options'
        );
        
        $allowedRendererOptions = array( 'rendereroption' );
        $allowedRefl = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'allowedRendererOptions' );
        $allowedRefl->setAccessible( true );
        $allowedRefl->setValue( $abstract, $allowedRendererOptions );
        
        $this->assertEquals(
                array( 'rendereroption' => 'whatever' ),
                $abstract->getRendererOptions(),
                'Altamira\Type\TypeAbstract::getRendererOptions should only return those options allowed by the provided renderer'
        );
    }
    
    /**
     * @covers \Altamira\Type\TypeAbstract::setOption
     */
    public function testAbstractSetOption()
    {
        $abstract = $this->getMockBuilder( '\Altamira\Type\TypeAbstract' )
                         ->disableOriginalConstructor()
                         ->getMockForAbstractClass();
        
        $this->assertEquals(
                $abstract,
                $abstract->setOption( 'foo', 'bar' )
        );
        $this->assertEquals(
                array( 'foo' => 'bar' ),
                $abstract->getOptions()
        );
    }
    
    /**
     * @covers \Altamira\Type\TypeAbstract::setOptions
     */
    public function testAbstractSetOptions()
    {
        $abstract = $this->getMockBuilder( '\Altamira\Type\TypeAbstract' )
                         ->disableOriginalConstructor()
                         ->setMethods( array( 'setOption' ) )
                         ->getMockForAbstractClass();
        
        $abstract
            ->expects    ( $this->at( 0 ) )
            ->method     ( 'setOption' )
            ->with       ( 'foo', 'bar' )
        ;
        $this->assertEquals(
                $abstract,
                $abstract->setOptions( array( 'foo' => 'bar' ) )
        );
    }
    
    /**
     * @covers \Altamira\Type\JqPlot\Bar::setOption
     */
    public function testJqPlotBarSetOption()
    {
        $bar = $this->getMockBuilder( '\Altamira\Type\JqPlot\Bar' )
                    ->disableOriginalConstructor()
                    ->setMethods( array( 'foo' ) )
                    ->getMock();
        
        $this->assertEquals(
                $bar,
                $bar->setOption( 'horizontal', true )
        );
        
        $optionsRefl = new ReflectionProperty( '\Altamira\Type\TypeAbstract', 'options' );
        $optionsRefl->setAccessible( true );
        $options = $optionsRefl->getValue( $bar );
        
        $this->assertEquals(
                'horizontal',
                $options['barDirection'],
                'A directional key should set the bar direction'
        );
    }
    
    public function testJqPlotBarGetOptions()
    {
        $bar = $this->getMockBuilder( '\Altamira\Type\JqPlot\Bar' )
                    ->disableOriginalConstructor()
                    ->setMethods( array( 'foo' ) )
                    ->getMock();
        
        $ticks = array( 'one', 'two', 'three' );
        $colors = array( '#cccccc', '#333333' );
        $options = array(
                'ticks' => $ticks,
                'min' => 10,
                'horizontal' => true,
                'stackSeries' => true,
                'seriesColors' => $colors
                );
        
        $optionsRefl = new ReflectionProperty( '\Altamira\Type\JqPlot\Bar', 'options' );
        $optionsRefl->setAccessible( true );
        $optionsRefl->setValue( $bar, $options );
        
        $renderer = 'foo.renderer.js';
        $axisRend = new ReflectionProperty( '\Altamira\Type\JqPlot\Bar', 'axisRenderer' );
        $axisRend->setAccessible( true );
        $axisRend->setValue( $bar, $renderer );
        
        $output = $bar->getOptions();
        
        $this->assertEquals(
                array( 'min' => 10 ),
                $output['axes']['xaxis']
        );
        $this->assertEquals(
                array( 'renderer' => "#{$renderer}#", 'ticks' => $ticks ), 
                $output['axes']['yaxis']
        );
        $this->assertTrue(
                $output['stackSeries']
        );
        $this->assertEquals(
                $colors,
                $output['seriesColors']
        );
    }
    
    public function testJqPlotBarGetOptionsFlipped()
    {
        $bar = $this->getMockBuilder( '\Altamira\Type\JqPlot\Bar' )
                    ->disableOriginalConstructor()
                    ->setMethods( array( 'foo' ) )
                    ->getMock();
        
        $ticks = array( 'one', 'two', 'three' );
        $colors = array( '#cccccc', '#333333' );
        $options = array(
                'ticks' => $ticks,
                'min' => 10,
                'stackSeries' => true,
                'seriesColors' => $colors
                );
        
        $optionsRefl = new ReflectionProperty( '\Altamira\Type\JqPlot\Bar', 'options' );
        $optionsRefl->setAccessible( true );
        $optionsRefl->setValue( $bar, $options );
        
        $renderer = 'foo.renderer.js';
        $axisRend = new ReflectionProperty( '\Altamira\Type\JqPlot\Bar', 'axisRenderer' );
        $axisRend->setAccessible( true );
        $axisRend->setValue( $bar, $renderer );
        
        $output = $bar->getOptions();
        
        $this->assertEquals(
                array( 'min' => 10 ),
                $output['axes']['yaxis']
        );
        $this->assertEquals(
                array( 'renderer' => "#{$renderer}#", 'ticks' => $ticks ), 
                $output['axes']['xaxis']
        );
        $this->assertTrue(
                $output['stackSeries']
        );
        $this->assertEquals(
                $colors,
                $output['seriesColors']
        );
    }
    
    /**
     * @covers \Altamira\Type\D3\D3TypeAbstract::getChart
     */
    public function testD3TypeAbstract()
    {
        $type = $this->getMockBuilder( '\Altamira\Type\D3\D3TypeAbstract' )
                     ->disableOriginalConstructor()
                     ->getMockForAbstractClass();
        
        try {
            $type->getChart();
        } catch ( Exception $e ) { }
        
        $this->assertInstanceOf(
                'Exception',
                $e
        );
        
        $cd = new ReflectionProperty( '\Altamira\Type\D3\D3TypeAbstract', 'chartDirective' );
        $cd->setAccessible( true );
        $cd->setValue( $type, 'foo' );
        
        $this->assertEquals(
                'foo',
                $type->getChart()
        );
    }
    
    /**
     * @covers \Altamira\Type\D3\Bar::getChart
     */
    public function testD3BarGetChart()
    {
        $type = $this->getMockBuilder( '\Altamira\Type\D3\Bar' )
                     ->disableOriginalConstructor()
                     ->setMethods( array( 'foo' ) )
                     ->getMock();
        
        $modelRefl = new ReflectionProperty( '\Altamira\Type\D3\Bar', 'chartModel' );
        $modelRefl->setAccessible( true );
        
        $dirRefl = new ReflectionProperty( '\Altamira\Type\D3\Bar', 'chartDirective' );
        $dirRefl->setAccessible( true );
        
        $this->assertEquals(
                str_replace( '#model#', $modelRefl->getValue( $type ), $dirRefl->getValue( $type ) ),
                $type->getChart()
        ); 
    }
    
    /**
     * @covers \Altamira\Type\D3\Bar::setOption
     */
    public function testD3BarSetOption()
    {
        $type = $this->getMockBuilder( '\Altamira\Type\D3\Bar' )
                     ->disableOriginalConstructor()
                     ->setMethods( array( 'foo' ) )
                     ->getMock();
        
        $modelRefl = new ReflectionProperty( '\Altamira\Type\D3\Bar', 'chartModel' );
        $modelRefl->setAccessible( true );
        
        $dirRefl = new ReflectionProperty( '\Altamira\Type\D3\Bar', 'chartDirective' );
        $dirRefl->setAccessible( true );
        
        $type->setOption( 'who', 'cares' ); // really -- who cares?
        
        $type->setOption( 'horizontal', true );
        
        $this->assertEquals(
                'multiBarHorizontalChart',
                $modelRefl->getValue( $type )
        );
        
        $type->setOption( 'stackSeries', true );
        
        $this->assertEquals(
                'multiBarChart',
                 $modelRefl->getValue( $type )
        );
        $this->assertContains(
                '.stacked(true)',
                $dirRefl->getValue( $type )
        ); 
    }
}