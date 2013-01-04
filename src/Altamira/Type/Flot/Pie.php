<?php 

namespace Altamira\Type\Flot;

class Pie extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'pie';
    
    protected $options = array('series' => array('pie' => array('show' => true)));
}