<?php

namespace Altamira\Type\JqPlot;

class Stacked extends \Altamira\Type\TypeAbstract
{
    const TYPE = 'stacked'; 
    
    protected $options = array(   'stackSeries'     => true,
                                  'seriesDefaults'  => array( 'fill'       => true, 
                                                              'showMarker' => false, 
                                                              'shadow'     => false
                                                             )
                                       );

}

?>