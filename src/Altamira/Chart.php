<?php
/**
 * Class definition for \Altamira\Chart
 * @author relwell
 *
 */
namespace Altamira;

/**
 * This class encapsulates all behavior around charts.
 * It's responsible for keeping track data and configurations 
 * and transmitting the appropriate information to its JsWriter.
 * Each chart has its own JsWriter instance.
 * @author relwell
 */
class Chart
{
    /**
     * Used as a unique identifier in code.
     * Will be post-processed, but should be treated using variable naming conventions. 
     * @var string
     */
	protected $name;

	/**
	 * These are the types set for a chart. Keys include series names as well as 'default'.
	 * @todo create a class that wraps this so that we don't chance naming conflicts
	 * @var array
	 */
	protected $types = array();
	
	/**
	 * The plaintext title of the chart, for rendering purposes. Can differ from unique identifier name.
	 * @var array
	 */
	protected $title;
	
	/**
	 * Contains all of the series to be plotted in this chart
	 * @var array
	 */
	protected $series = array();
	/**
	 * Series labels. Should be in the same order as the series they label.
	 * @var array
	 */
	protected $labels = array();
	/**
	 * The JsWriter that is responsible for rendering this chart on the client side.
	 * @var \Altamira\JsWriter\JsWriterAbstract
	 */
	protected $jsWriter;
	
	/**
	 * Determines whether the title is hidden
	 * @var bool
	 */
	protected $titleHidden = false;

	/**
	 * Constructor method. Registers the identifier name and initializes the JsWriter based on the library.
	 * @param string $name
	 * @param string $library
	 */
	public function __construct($name, $library = \Altamira\JsWriter\JqPlot::LIBRARY )
	{
	    if ( empty( $name ) ) {
	        throw new \Exception( 'Please provide a name for this chart.' );
	    }
	    
		$this->name = preg_replace( '/\s+/', '_', $name );

	    $className = '\\Altamira\\JsWriter\\'.ucfirst( $library );
    
	    if ( class_exists( $className ) ) {
	        $this->jsWriter = new $className( $this );
	    } else {
	        throw new \Exception( "No JsWriter by name of {$className}" );
	    }
	}
	
	/**
	 * Accessor for name attribute
	 * @return string
	 */
	public function getName()
	{
	    return $this->name;
	}

	/**
	 * Sets a text title for the chart, for rendering
	 * @param string $title
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setTitle( $title )
	{
	    $this->title = $title;
		$this->jsWriter->setOption( 'title', $title );

		return $this;
	}
	
	/**
	 * Accessor method. Will back off to name attribute if title doesn't exist.
	 * @return string
	 */
	public function getTitle()
	{
	    return $this->title ?: $this->name;
	}
	
	/**
	 * Interface to jsWriter option setting.
	 * @param array $opts
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function useHighlighting( array $opts = array( 'size' => 7.5 ) )
	{
	    if ( $this->jsWriter instanceof \Altamira\JsWriter\Ability\Highlightable ) {
    	    $this->jsWriter->useHighlighting( $opts );
	    }

		return $this;
	}

	/**
	 * Interface to jsWriter option setting
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function useZooming()
	{
	    $this->jsWriter->useZooming();
	    
		return $this;
	}

	/**
	 * Interface to jsWriter option setting
	 * @throws \BadMethodCallException
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function useCursor()
	{
	    //@codeCoverageIgnoreStart
	    if (! $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Cursorable ) {
            throw new \BadMethodCallException( "JsWriter cannot use cursor" );
	    }
	    //@codeCoverageIgnoreEnd
	    
	    $this->jsWriter->useCursor();
	    
	    return $this;
	}

	/**
	 * Interface to jsWriter option
	 * @param string $axis
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function useDates( $axis = 'x' )
	{
	    if ( $this->jsWriter instanceof \Altamira\JsWriter\Ability\Datable ) {
            $this->jsWriter->useDates($axis);
	    }

		return $this;
	}

	/**
	 * Sets axis tick values for this chart in the jsWriter
	 * @param string $axis x or y
	 * @param array $ticks the tick labels
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setAxisTicks( $axis, array $ticks )
	{
	    $this->jsWriter->setAxisTicks( $axis, $ticks );

		return $this;
	}

	/**
	 * Generic interface to any kind of axis option
	 * @param string $axis
	 * @param string $name
	 * @param mixed  $value
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setAxisOptions( $axis, $name, $value )
	{
		$this->jsWriter->setAxisOptions( $axis, $name, $value );

		return $this;
	}

	/**
	 * Sets the colors to be used for each series, in order
	 * @param array $colors
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setSeriesColors( array $colors )
	{
		$this->jsWriter->setOption( 'seriesColors', $colors );
		return $this;
	}

	/**
	 * Sets the label on this chart for a given axis
	 * @param string $axis x, y, z
	 * @param string $label the label
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setAxisLabel( $axis, $label )
	{
	    if ( in_array( $axis, array( 'x', 'y', 'z' ) ) ) {
	        $originalAxisOptions = $this->jsWriter->getOption( 'axes', array() );
	        $desiredAxisOptions = array( "{$axis}axis" => array( 'label' => $label ) );
	        $this->jsWriter->setOption( 'axes', array_merge_recursive( $originalAxisOptions, $desiredAxisOptions ) );
	    } 

		return $this;
	}

	/**
	 * Sets chart's type in the JS writer for default or for a given series.
	 * @param string $type
	 * @param string $seriesTitle
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setType( $type, $seriesTitle = null )
	{
	    $this->jsWriter->setType( $type, $seriesTitle );

		return $this;
	}

	/**
	 * Sets an option for type-based rendering within this chart by default or for a series
	 * @param string $name the option name
	 * @param string $option the option value
	 * @param string $seriesTitle
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setTypeOption( $name, $option, $seriesTitle = null)
	{
	    $this->jsWriter->setTypeOption( $name, $option, $seriesTitle );
	    
		return $this;
	}

	/**
	 * Sets legend options within the jsWriter
	 * @param array $opts
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function setLegend( array $opts = array('on' => 'true', 
                                                   'location' => 'ne', 
                                                   'x' => 0, 
                                                   'y' => 0) )
	{
		$this->jsWriter->setLegend( $opts );

		return $this;
	}

	/**
	 * Sets grid options for griddable JsWriter instances
	 * @param array $opts
	 * @throws \BadMethodCallException
	 * @return \Altamira\Chart
	 */
	public function setGrid( array $opts = array( 'on' => true ) )
	{
	    //@codeCoverageIgnoreStart
	    if (! $this->jsWriter instanceOf JsWriter\Ability\Griddable ) {
	        throw new \BadMethodCallException( "JsWriter not Griddable");
	    }
	    //@codeCoverageIgnoreEnd
	    
	    $this->jsWriter->setGrid( $opts );
	    
		return $this;
	}
	
	/**
	 * Instantiates a series based on the data provided
	 * @param array  $data array must consist of ChartDatumAbstract instances
	 * @param string|null $title
	 * @param string|null $type
	 * @return \Altamira\Series
	 */
	public function createSeries( $data, $title = null, $type = null )
	{
        return new Series( $data, $title, $this->jsWriter );
	}
	
	/**
	 * Used to address the different concepts of what constitutes a series in different JsWriters
	 * @param array $dataSet
	 * @param array $factorySettings the factory and the method call in an array
	 * @param string|null $title
	 * @param string|null $type
	 * @throws \Exception
	 * @return multitype:\Altamira\Series |\Altamira\Series
	 */
	public function createManySeries(array $dataSet, array $factorySettings , $title = null, $type = null)
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Flot ) {
	        if (   !empty( $this->series )
	                && $this->jsWriter->getType() == 'Donut' ) {
	            throw new \Exception("Flot doesn't allow donut charts with multiple series");
	        }
	        $seriesArray = array();
	        foreach ( $dataSet as $data ) 
	        {
	            $seriesArray[] = $this->createSeries( call_user_func( $factorySettings, array( $data ) ), $data[0], $type );
	        }
	        return $seriesArray;
	    } else {
	        return $this->createSeries( call_user_func( $factorySettings, $dataSet ), $title, $type );
	    }
	}
	
	/**
	 * Lets you add one or more series using the same method call
	 * @param \Altamira\Series|array $seriesOrArray
	 * @throws \UnexpectedValueException
	 * @return \Altamira\Chart provides fluent interface
	 */
	public function addSeries( $seriesOrArray )
	{
	    if (! is_array( $seriesOrArray ) ) {
	        $seriesOrArray = array( $seriesOrArray );
	    }
	    
	    if ( is_array( $seriesOrArray ) ) {
	        foreach ( $seriesOrArray as $series ) 
	        {
	            if (! $series instanceof Series ) {
	                throw new \UnexpectedValueException( '\Altamira\Chart::addSeries expects a single series or an array of series instances' );
	            } 
	            $this->addSingleSeries( $series );
	        }
	    }
	    
	    return $this;
	}

	/**
	 * Logic behind adding a series.
	 * @param Series $series
	 * @return \Altamira\Chart
	 */
	public function addSingleSeries(Series $series)
	{
		$this->series[$series->getTitle()] = $series;

		return $this;
	}

	/**
	 * Wrapper responsible for rendering the div for a given chart.
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function getDiv($width = 500, $height = 400)
	{
	    $styleOptions = array('width'    =>    $width.'px', 
	                          'height'   =>    $height.'px'
	                         );
	    
	    return ChartRenderer::render( $this, $styleOptions );
	}

	/**
	 * Returns the files a jsWriter needs to work
	 * @return array
	 */
	public function getFiles()
	{
		return $this->jsWriter->getFiles();
	}

	/**
	 * Returns any inline scripts stored by the jsWriter during configuration
	 * @return array
	 */
	public function getScript()
	{
	    return $this->jsWriter->getScript();
	}
	
	/**
	 * Returns the jsWriter
	 * @return \Altamira\JsWriter\JsWriterAbstract
	 */
	public function getJsWriter()
	{	    
	    return $this->jsWriter;
	}

	/**
	 * Returns the library key for the jswriter
	 * @return string
	 */
	public function getLibrary()
	{
	    return $this->jsWriter->getLibrary();
	}
	
	/**
	 * Returns an array of series instances
	 * @return array
	 */
	public function getSeries()
	{
	    return $this->series;
	}
	
	/**
	 * Returns whether or not the chart title should be hidden
	 * Note that showing the title is the default behavior.
	 * @return bool
	 */
	public function titleHidden()
	{
        return $this->titleHidden;
	}
	
	/**
	 * Sets the variable responsible for hiding the title
	 * Note that showing the title is the default behavior
	 */
	public function hideTitle()
	{
		$this->titleHidden = true;
		return $this;
	}
}
