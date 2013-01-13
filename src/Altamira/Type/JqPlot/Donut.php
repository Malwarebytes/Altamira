<?php
/**
 * Class definition for \Altamira\Type\JqPlot\Donut
 * @author relwell
 */
namespace Altamira\Type\JqPlot;
/**
 * This class will register a series or chart to be rendered as a donut
 * @author relwell
 * @package Type
 * @subpackage JqPlot
 */
class Donut extends Pie
{
    const TYPE = 'donut';
    
    /**
     * These options overwrite default JqPlot JsWriter options
     * @var array
     */
    protected $options = array('seriesDefaults'=>array('rendererOptions'=>array()));
    
    /**
     * Registers options as renderer options for this specific case
     * @see \Altamira\Type\TypeAbstract::setOption()
     * @param string $name
     * @param mixed $value
     * @return \Altamira\Type\JqPlot\Donut
     */
    public function setOption($name, $value)
    {
        $this->options['seriesDefaults']['rendererOptions'][$name] = $value;
        return $this; 
    }
}

?>