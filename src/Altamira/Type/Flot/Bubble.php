<?php 
/**
 * Class definition for \Altamira\Type\Flot\Bubble
 * @author relwell
 */
namespace Altamira\Type\Flot;

/**
 * Registers a series or chart for being rendered as a bubble
 * Requires https://raw.github.com/ubermajestix/flot-plugins/master/jquery.flot.bubble.js
 * @author relwell
 * @package Type
 * @subpackage Flot
 */
class Bubble extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'bubble';
    
    /**
     * These options override the Flot jsWriter defaults when registered
     * @var array
     */
    protected $options = array('series' => array('bubble' => true, 'lines'=>array('show'=>true), 'points'=>array('show'=>true)));
}