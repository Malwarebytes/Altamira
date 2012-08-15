<?php

namespace Altamira\Type\Flot;

class Bar extends \Altamira\Type\TypeAbstract
{

    protected $options = array('series'=>array('lines'    =>    array('show' => false),
                                                 'bars'     =>    array('show' => true),
                                                 'points'   =>    array('show' => false)
                              ));

	public function getUseTags()
	{
		if(isset($this->options['horizontal']) && $this->options['horizontal'])
			return true;

		return false;
	}
	
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
	        default:
	            parent::setOption($name, $value);
	    }
	    
	    return $this;
	}
}

?>