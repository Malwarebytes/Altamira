<?php
/**
 * Class definition for \Altamira\Type\JqPlot\Bar
 * @author relwell
 *
 */
namespace Altamira\Type\JqPlot;
/**
 * This class registers a series or an entire chart for rendering as a bar
 * @author relwell
 * @package Type
 * @subpackage Flot
 */
class Bar extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'bar';
    
    /**
     * Only used for this specific bar type -- fulfilled via config
     */
    protected $axisRenderer = null;

    /**
     * This provides a limited set of options based on how it has been configured
     * @TODO this really looks like it should be refactored, but it's pretty opaque and legacy at this point
     * @see \Altamira\Type\TypeAbstract::getOptions()
     * @return array
     */
	public function getOptions()
	{
		$opts = array();

		$first = array();
		$second = array();
		
        if ( $this->axisRenderer ) {
		    $first['renderer'] = '#' . $this->axisRenderer . '#';
        }
        
		if( isset( $this->options['ticks'] ) ) {
			$first['ticks'] = $this->options['ticks'];
		}
		
		$second['min'] = isset( $this->options['min'] ) ? $this->options['min'] : 0;

		if( isset( $this->options['horizontal'] ) && $this->options['horizontal'] ) {
			$opts['xaxis'] = $second;
			$opts['yaxis'] = $first;
		} else {
			$opts['xaxis'] = $first;
			$opts['yaxis'] = $second;
		}

		$opts = array( 'axes' => $opts );

		if( isset( $this->options['stackSeries'] ) ) {
			$opts['stackSeries'] = $this->options['stackSeries'];
		}

		if( isset( $this->options['seriesColors'] ) ) {
			$opts['seriesColors'] = $this->options['seriesColors'];
		}

		return $opts;
	}

	/**
	 * Allows us to configure bar direction for renderer
	 * (non-PHPdoc)
	 * @see \Altamira\Type\TypeAbstract::setOption()
	 * @param string $name
	 * @param mixed $value
	 * @return \Altamira\JsWriter\JqPlot\Bar
	 */
	public function setOption( $name, $value )
	{
	    if ( in_array( $name, array( 'horizontal', 'vertical' ) ) ) {
	        $this->options['barDirection'] = $name;
	    }
	    return parent::setOption( $name, $value );
	}
}