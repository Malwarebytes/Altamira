<?php 

namespace Altamira\Type\Flot;

// note that this type doesn't accept nesting like in jqplot

class Donut extends Pie
{
    
    protected $options = array('series' => array('pie' => array('show' => true, 'innerRadius' => 0.5)));
    
    
    
}