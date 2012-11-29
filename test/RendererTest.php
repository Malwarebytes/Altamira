<?php

class RendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Altamira\ScriptsRenderer::__construct
     * @covers \Altamira\ScriptsRenderer::render
     * @covers \Altamira\ScriptsRenderer::get
     */
    public function testScriptsRenderer()
    {
        $scriptsRenderer = new \Altamira\ScriptsRenderer( array( 'alert("hi");', 'alert("bye");' ) );
        
        $this->assertEquals(
                $scriptsRenderer,
                $scriptsRenderer->render(),
                '\Altamira\ScriptsRenderer::render() should provide a fluent interface.'
        );
        
        $scriptsRenderer->next();
        
        $this->expectOutputString(
                "alert(\"hi\");<script type='text/javascript'>\nalert(\"bye\");\n</script>\n",
                '\Altamira\ScriptsRenderer should implement ArrayIterator, and calling next() on it should provide the next script.'
                . ' Providing true as the first parameter to the render() method should cause the script to be wrapped in script tags.' 
        );
        
        $this->assertContains(
                "<script type='text/javascript'>",
                $scriptsRenderer->get( true ),
                '\Altamira\ScriptsRenderer::get should include script tags in the return value string if true is passed as the first parameter'
        );
        $scriptsRenderer->render( true );
    }
    
    /**
     * @covers \Altamira\FilesRenderer::__construct
     * @covers \Altamira\FilesRenderer::append
     * @covers \Altamira\FilesRenderer::handleMinify
     * @covers \Altamira\FilesRenderer::render
     */
    public function testFilesRendererNoMin()
    {
        $files = array( 'foo.js', 'bar.min.js' );
        
        $expectedResult = <<<ENDSCRIPT
<script type="text/javascript" src="foo.js"></script>
<script type="text/javascript" src="bar.js"></script>
<script type="text/javascript" src="baz.js"></script>

ENDSCRIPT;
        
        $this->expectOutputString(
                $expectedResult,
                '\Altamira\FilesRenderer should implement ArrayIterator, and calling next() on it should provide the next script.'
                . ' Providing a path should prepend that path to each file.' 
        );
        
        $filesRenderer = new \Altamira\FilesRenderer( $files );
        
        $filesRenderer->append( 'baz.min.js' );
        
        do {
            $filesRenderer->render();
            $filesRenderer->next();
        } while ( $filesRenderer->valid() );
        
    }
    
    /**
     * @covers \Altamira\FilesRenderer::__construct
     * @covers \Altamira\FilesRenderer::handleMinify
     * @covers \Altamira\FilesRenderer::offsetSet
     * @covers \Altamira\FilesRenderer::render
     */
    public function testFilesRendererWithMin()
    {
        $files = array( 'foo.js', 'bar.min.js' );
        
        $expectedResult = <<<ENDSCRIPT
<script type="text/javascript" src="foo.min.js"></script>
<script type="text/javascript" src="bar.min.js"></script>
<script type="text/javascript" src="baz.min.js"></script>

ENDSCRIPT;
        
        $config = \Altamira\Config::getInstance();
        $config['js.minify'] = true;
        
        $this->expectOutputString(
                $expectedResult,
                '\Altamira\FilesRenderer should implement ArrayIterator, and calling next() on it should provide the next script.'
                . ' Providing a path should prepend that path to each file.' 
        );
        
        $filesRenderer = new \Altamira\FilesRenderer( $files );
        
        $filesRenderer[2] = 'baz.js';
        
        do {
            $filesRenderer->render();
            $filesRenderer->next();
        } while ( $filesRenderer->valid() );
    }
    
    /**
     * @covers \Altamira\ChartRenderer\DefaultRenderer::preRender
     * @covers \Altamira\ChartRenderer\DefaultRenderer::postRender
     * @covers \Altamira\ChartRenderer\DefaultRenderer::renderStyle
     */
    public function testDefaultRenderer()
    {
        $mockChart = $this->getMock( '\Altamira\Chart', array( 'getLibrary', 'getName' ), array( 'Mock Chart' ) );
        
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getName' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $styleOptions = array( 'float' => 'left', 'border' => '1px solid #cccccc' );
        
        $expectedStyle = 'float: left; border: 1px solid #cccccc; ';
        $styleRendered = \Altamira\ChartRenderer\DefaultRenderer::renderStyle( $styleOptions );
        
        $this->assertEquals(
                $expectedStyle,
                $styleRendered,
                'DefaultRenderer::renderStyle() should transform key-value pairs into CSS inline style declarations'
        );
        
        $preRendering = \Altamira\ChartRenderer\DefaultRenderer::preRender( $mockChart, $styleOptions );
        
        $expectedPreRendering = <<<ENDDIV
<div class="flot" id="foo" style="{$expectedStyle}">
ENDDIV;
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\DefaultRenderer::preRender() should create an open div with metadata based on the chart and style passed to it.'
        );

        $this->assertEquals(
                '</div>',
                \Altamira\ChartRenderer\DefaultRenderer::postRender( $mockChart, $styleOptions ),
                '\Altamira\ChartRenderer\DefaultRenderer::postRender() should return a closing div tag.'
        );

        $method = new ReflectionMethod( '\Altamira\ChartRenderer', 'getInstance' );
        $method->setAccessible( true );
        $this->assertInstanceOf(
                '\Altamira\ChartRenderer',
                $method->invoke( $this->getMockBuilder( '\Altamira\ChartRenderer' )->disableOriginalConstructor()->getMock() )
        );

    }
    
    /**
     * @covers \Altamira\ChartRenderer\TitleRenderer::preRender
     * @covers \Altamira\ChartRenderer\TitleRenderer::postRender
     * @covers \Altamira\ChartRenderer\TitleRenderer::renderStyle
     */
    public function testTitleRenderer()
    {
        $mockChart = $this->getMock( '\Altamira\Chart', array( 'getTitle' ), array( 'Mock Chart' ) );
        
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $styleOptions = array( 'float' => 'left', 'border' => '1px solid #cccccc' );
        
        $expectedStyle = '';
        $styleRendered = \Altamira\ChartRenderer\TitleRenderer::renderStyle( $styleOptions );
        
        $this->assertEquals(
                $expectedStyle,
                $styleRendered,
                'TitleRenderer::renderStyle() should always return an empty string'
        );
        
        $preRendering = \Altamira\ChartRenderer\TitleRenderer::preRender( $mockChart, $styleOptions );
        
        $expectedPreRendering = <<<ENDDIV
<div class="altamira-chart-title">
    <h3>foo</h3>

ENDDIV;
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\TitleRenderer::preRender() should create an open div wrapping h3 tags with the chart title by default.'
        );
        
        $styleOptions['titleTag'] = 'h1';
        
        $expectedPreRendering = <<<ENDDIV
<div class="altamira-chart-title">
    <h1>foo</h1>

ENDDIV;

        $preRendering = \Altamira\ChartRenderer\TitleRenderer::preRender( $mockChart, $styleOptions );
        
        $this->assertEquals(
                $expectedPreRendering,
                $preRendering,
                '\Altamira\ChartRenderer\TitleRenderer::preRender() should support configurable title tags, based on style options.'
        );
        
        $this->assertEquals(
                '</div>',
                \Altamira\ChartRenderer\TitleRenderer::postRender( $mockChart, $styleOptions ),
                '\Altamira\ChartRenderer\TitleRenderer::postRender() should return a closing div tag.'
        );
    }
    
    /**
     * @covers \Altamira\ChartRenderer::__construct
     * @covers \Altamira\ChartRenderer::getInstance
     * @covers \Altamira\ChartRenderer::pushRenderer
     * @covers \Altamira\ChartRenderer::render
     * @covers \Altamira\ChartRenderer::reset
     * @covers \Altamira\ChartRenderer::unshiftRenderer
     */
    public function testChartRenderer()
    {
        $method = new ReflectionMethod( '\Altamira\ChartRenderer', '__construct' );
        
        $this->assertTrue(
                $method->isProtected(),
                '\Altamira\ChartRenderer should implement singleton design pattern'
        );
        
        $styleOptions = array( 'float' => 'left', 'border' => '1px solid #cccccc' );
        
        $mockChart = $this->getMock( '\Altamira\Chart', array( 'getLibrary', 'getName', 'getTitle' ), array( 'Mock Chart' ) );
        
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getLibrary' )
            ->will   ( $this->returnValue( 'flot' ) )
        ;
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getName' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        $mockChart
            ->expects( $this->any() )
            ->method ( 'getTitle' )
            ->will   ( $this->returnValue( 'foo' ) )
        ;
        
        $expectedStyle = 'float: left; border: 1px solid #cccccc; ';
        
        $defaultOutput = \Altamira\ChartRenderer::render( $mockChart, $styleOptions );
        
        $expectedOutput = <<<ENDOUTPUT
<div class="flot" id="foo" style="{$expectedStyle}"></div>
ENDOUTPUT;
        
        $this->assertEquals(
                $expectedOutput,
                $defaultOutput,
                '\Altamira\ChartRenderer::render() should render the chart using only \Altamira\ChartRenderer\DefaultRenderer by default' 
        );
        
        $getInstanceMethod = new ReflectionMethod( '\Altamira\ChartRenderer', 'getInstance' );
        $getInstanceMethod->setAccessible( true );
        $instance = $getInstanceMethod->invoke( null );
        
        $this->assertEquals(
                $instance,
                \Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\TitleRenderer' ),
                '\Altamira\ChartRenderer::pushRenderer should provide a fluent interface'
        );
        
        $exception = null;
        try {
            \Altamira\ChartRenderer::pushRenderer( '\Altamira\Chart' );
        } catch ( Exception $exception ) {}
        $this->assertInstanceOf(
                'UnexpectedValueException',
                $exception
        );
        
        $exception = null;
        try {
            \Altamira\ChartRenderer::unshiftRenderer( '\Altamira\Chart' );
        } catch ( Exception $exception ) {}
        $this->assertInstanceOf(
                'UnexpectedValueException',
                $exception
        );
        
        $rendererChain = new ReflectionProperty( '\Altamira\ChartRenderer', 'rendererChain' );
        $rendererChain->setAccessible( 'true' );
        
        $this->assertEquals(
                array( '\Altamira\ChartRenderer\DefaultRenderer', '\Altamira\ChartRenderer\TitleRenderer' ),
                $rendererChain->getValue( $instance ),
                '\Altamira\ChartRenderer::pushRenderer should add the renderer to the end of the renderer chain'
        );
        
        $fullExpectedOutput = <<<ENDOUTPUT
<div class="altamira-chart-title">
    <h3>foo</h3>
<div class="flot" id="foo" style="{$expectedStyle}"></div></div>
ENDOUTPUT;
        
        $this->assertEquals(
                $fullExpectedOutput,
                \Altamira\ChartRenderer::render( $mockChart, $styleOptions ),
                '\Alamira\ChartRenderer::render() should take a nested approach to parsing, '
                . 'prerendering from the top of the stack to the bottom, and postrendering from the bottom to the top.'
        ); 
        
        \Altamira\ChartRenderer::reset();
        
        $this->assertEmpty(
                $rendererChain->getValue( $instance ),
                '\Altamira\ChartRenderer::reset() should empty the renderer chain'
        );
        
        \Altamira\ChartRenderer::unshiftRenderer( '\Altamira\ChartRenderer\DefaultRenderer');
        \Altamira\ChartRenderer::unshiftRenderer( '\Altamira\ChartRenderer\TitleRenderer');
        
        $this->assertEquals(
                array( '\Altamira\ChartRenderer\TitleRenderer', '\Altamira\ChartRenderer\DefaultRenderer' ),
                $rendererChain->getValue( $instance ),
                '\Altamira\ChartRenderer::pushRenderer should add the renderer to the beginning of the renderer chain'
        );

        $method = new ReflectionMethod( '\Altamira\ChartRenderer', 'getInstance' );
        $method->setAccessible( true );
	$property = new ReflectionProperty( '\Altamira\ChartRenderer', 'instance' );
        $mockChartRenderer = $this->getMockBuilder( '\Altamira\ChartRenderer' )
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $property->setAccessible( true );
        $property->setValue( null );
        $this->assertInstanceOf(
                '\Altamira\ChartRenderer',
                $method->invoke( $mockChartRenderer )
        );

	$property = new ReflectionProperty( '\Altamira\ChartRenderer', 'rendererChain' );
        $property->setAccessible( true );
        $property->setValue( array() );

        \Altamira\ChartRenderer::render( $mockChart );

        $this->assertNotEmpty(
                $property->getValue(),
                '\Altamira\ChartRenderer should push a default renderer by default if it has no renderers set'
        );

    }
    
}