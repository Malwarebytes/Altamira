<?php 
/**
 * Class definition for \Altamira\Type\Flot\Pie
 * @author relwell
 */
namespace Altamira\Type\Flot;
/**
 * This class registers a series of chart as being rendered as a pie.
 * @author relwell
 * @package Type
 * @subpackage Flot
 */
class Pie extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'pie';
    
    /**
     * These options override Flot JsWriter defaults when registered
     * @var unknown_type
     */
    protected $options = array('series' => array('pie' => array('show' => true)));
}