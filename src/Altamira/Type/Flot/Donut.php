<?php 
/**
 * Class definition for \Altamira\Type\Flot\Donut
 * @author relwell
 */
namespace Altamira\Type\Flot;

/**
 * This type registers a series of chart to be rendered as a donut.
 * Note that this type doesn't accept nesting like in jqplot
 * For the case of Flot, the donut chart is just a hack on top of a pie chart.
 * We could probably do some additional work to nest values, but this seems edge-casey.
 * I would just use JqPlot for donut charts..
 * @author relwell
 * @package Type
 * @subpackage Flot
 */
class Donut extends Pie
{
    const TYPE = 'donut';
    
    /**
     * These options override Flot JsWriter defaults when registered
     * @var array
     */
    protected $options = array('series' => array('pie' => array('show' => true, 'innerRadius' => 0.5)));
    
}