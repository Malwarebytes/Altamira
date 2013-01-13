<?php
/**
 * Class definition for \Altamira\Type\Flot\Bar
 * @author relwell
 */
namespace Altamira\Type\Flot;
/**
 * Registers the provided series (or entire chart) as a bar series in Flot
 * @author relwell
 * @package Type
 * @subpackage Flot
 */
class Bar extends \Altamira\Type\TypeAbstract
{
    
    const TYPE = 'bar';

    /**
     * These options override default Flot options.
     * @var array
     */
    protected $options = array('series'=>array('lines'    =>    array('show' => false),
                                                 'bars'     =>    array('show' => true),
                                                 'points'   =>    array('show' => false)
                              ));

    /**
     * Abstracts out option setting and registers the required options based on what's provided.
     * @see \Altamira\Type\TypeAbstract::setOption()
     * @param string $name
     * @param mixed $value
     * @return \Altamira\Type\Flot\Bar
     */
	public function setOption($name, $value)
	{
	    switch ($name) {
	        case 'horizontal':
	            $this->options['bars']['horizontal'] = $value;
                break;
	        case 'stackSeries':
	            $this->pluginFiles[] = 'jquery.flot.stack.js';
	            $this->options['series']['stack'] = true;
	            break; 
	        case 'fillColor':
	            $this->options['series']['bars']['fillColor']['colors'] = $value;
	            break;
	        default:
	            parent::setOption($name, $value);
	    }
	    
	    return $this;
	}
}

?>