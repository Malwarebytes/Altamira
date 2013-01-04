<?php 

namespace Altamira\Type\Flot;

//requires https://raw.github.com/ubermajestix/flot-plugins/master/jquery.flot.bubble.js
class Bubble extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'bubble';
    
    protected $options = array('series' => array('bubble' => true, 'lines'=>array('show'=>true), 'points'=>array('show'=>true)));
}