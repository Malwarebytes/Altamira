<?php
/**
 * Class definition for \Altamira\Series
 * @author relwell
 */
namespace Altamira;

use Altamira\JsWriter\JsWriterAbstract;
use Altamira\ChartDatum\ChartDatumAbstract;

/**
 * This class represents a series of ChartDatum instances that can be plotted on a chart.
 */
class Series
{
    /**
     * Counter used when a series title is not provided.
     * @var int
     */
	static protected $count = 0;
	
	/**
	 * An array of ChartDatumAbstract children
	 * @var array of \Altamira\ChartDatum\ChartDatumAbstract
	 */
	protected $data = array();
	
	/**
	 * The JsWriter instance responsible for rendering this series
	 * @var \Altamira\JsWriter\JsWriterAbstract
	 */
	protected $jsWriter;
	
	/**
	 * The title of the series, used for labeling
	 * @var string
	 */
	protected $title;
	
	/**
	 * The labels for each datum, tracked by array index against the data array
	 * @var array of strings
	 */
	protected $labels = array();

	/**
	 * Constructor method
	 * @param array            $data an array of ChartDatumAbstract instances
	 * @param string           $title the desired title of the series (used to label a series)
	 * @param JsWriterAbstract $jsWriter the jswriter, dependency-injected for rendering
	 * @throws \UnexpectedValueException
	 */
	public function __construct($data, $title = null, JsWriterAbstract $jsWriter)
	{
		self::$count++;

		$tagcount = 0;
		foreach($data as $datum) {
            if (! $datum instanceof ChartDatumAbstract ) {
                throw new \UnexpectedValueException( "The data array must consist of instances inheriting from ChartDatumAbstract" );
            }
            $datum->setJsWriter  ( $jsWriter )
                  ->setSeries    ( $this) ;
		}
		$this->data = $data;

		if( isset( $title ) ) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}

		$this->jsWriter = $jsWriter;
		$this->jsWriter->initializeSeries( $this->title );
	}

	/**
	 * Sets the shadow for this series. Refer to default options.
	 * @param array $opts refer to the defaults for the kind of options 
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setShadow( $opts = array( 'use'    =>    true, 
                                              'angle'  =>    45, 
                                              'offset' =>    1.25, 
                                              'depth'  =>    3, 
                                              'alpha'  =>    0.1 ) )
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Shadowable ) {
	        $this->jsWriter->setShadow( $this->getTitle(), $opts );
	    }
	    
		return $this;
	}

	/**
	 * Sets fill option for JSWriter for this series
	 * @param array $opts see constructor for keys
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setFill( $opts = array( 'use'    => true, 
                                            'stroke' => false, 
                                            'color'  => null, 
                                            'alpha'  => null ) )
	{
        if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Fillable ) {
    	    $this->jsWriter->setFill($this, $opts);
        }
	    
		return $this;
	}

	/**
	 * Returns the array of ChartDatumAbstract instances set during construction
	 * @return array of ChartDatumAbstract instances
	 */
	public function getData()
	{
	    return $this->data;
	}

	/**
	 * Sets a string value for the title of the chart.
	 * We can supress the display of a title value with hideTitle()
	 * @param string $title
	 * @return \Altamira\Series
	 */
	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	/**
	 * Set the labels to be used for this series in the JsWriter
	 * @param array $labels all strings
	 * @return \Altamira\Series provides fluent interface
	 */
	public function useLabels( $labels = array() )
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->jsWriter->useSeriesLabels( $this->getTitle(), $labels);
	    }
	    
	    for ( $i = 0; $i < count( $labels ) && $i < count( $this->data ); $i++ ) {
	        $this->data[$i]->setLabel( $labels[$i] );
	    }

		return $this;
	}

	/**
	 * Set additional metadata around labels, such as margin and position
	 * @param string $name
	 * @param string $value
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setLabelSetting( $name, $value )
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->jsWriter->setSeriesLabelSetting($this->getTitle(), $name, $value);
	    }
		
		return $this;
	}

	/**
	 * Returns the title set during construciton 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets an option for this specific series within the JsWriter
	 * @param string $name
	 * @param string $value
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setOption( $name, $value )
	{
		$this->jsWriter->setSeriesOption( $this->getTitle(), $name, $value );

		return $this;
	}
	
	/**
	 * Returns data stored in JsWriter for a given option for this series
	 * @param  string $option
	 * @return string|null
	 */
	public function getOption( $option )
	{
	    return $this->jsWriter->getSeriesOption( $this->getTitle(), $option );
	}

	/**
	 * Gets an array of options that have been set for this series
	 * @return array of options
	 */
	public function getOptions()
	{
        return $this->jsWriter->getOptionsForSeries( $this->getTitle() );
	}
	
	/**
	 * Sets the line width for the series
	 * @param string|int $val
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setLineWidth( $val )
	{
	    if ( $this->jsWriter instanceof \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesLineWidth( $this->getTitle(), $val );
	    }
	    return $this;
	}
	
	/**
	 * Sets whether to show a line for this series
	 * @param bool $bool
	 * @return \Altamira\Series provides fluent interface
	 */
	public function showLine( $bool = true )
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
    	    $this->jsWriter->setSeriesShowLine( $this->getTitle(), $bool );
	    }
	    return $this;
	}
	
	/**
	 * Sets whether to show markers for this series in the JS Writer
	 * @param bool $bool
	 * @return \Altamira\Series provides fluent interface
	 */
	public function showMarker( $bool = true )
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesShowMarker( $this->getTitle(), $bool );
	    }
	    return $this;
	}
	
	/**
	 * Sets the kind of marker tos how for this series in the JS writer
	 * @param unknown_type $value
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setMarkerStyle($value)
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerStyle( $this->getTitle(), $value );
	    }
	    return $this;
	}
	
	/**
	 * Sets the size of the marker
	 * @param string|int $value
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setMarkerSize($value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerSize($this->getTitle(), $value);
	    }
	    return $this;
	}
	
	/**
	 * Sets the rendering type for this series
	 * @param string $type
	 * @return \Altamira\Series provides fluent interface
	 */
	public function setType( $type )
	{
	    $this->jsWriter->setType( $type, $this->getTitle() );
	    
	    return $this;
	}
	
	/**
	 * Useful for D3, which relies on specific values for data
	 * @param string $key
	 * @param mixed $value
	 * @return \Altamira\Series provides fluent interface
	 */
	public function mapToData( $key, $value )
	{
	    foreach ( $this->getData() as $datum ) {
	        $datum[$key] = $value;
	    }
	    return $this;
	}
}
