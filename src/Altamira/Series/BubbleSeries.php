<?php

namespace Altamira\Series;
use Altamira\JsWriter;

use Altamira\Series;

class BubbleSeries extends Series
{
	public function __construct($data, $title = null, JsWriter\JsWriterAbstract $jsWriter)
	{
	    $this->jsWriter = $jsWriter;
	    
		self::$count++;

		if ($this->jsWriter instanceOf JsWriter\JqPlot) {
    		foreach($data as $datum) {
    			if(is_array($datum) && count($datum) == 4) {
    				$this->useTags = true;
    				$this->data[] = array_slice($datum, 0, 3);
    				$this->tags[] = array_pop($datum);
    			}
    		}
		} else {
		    $this->data[] = $data;
		    $this->tags[] = end($data);
		    $this->useTags = true;
		}

		if(isset($title)) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}
	}

}

?>