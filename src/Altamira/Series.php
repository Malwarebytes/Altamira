<?php

namespace Altamira;

use Altamira\JsWriter\JsWriterAbstract;

class Series
{
	static protected $count = 0;
	protected $data = array();
	protected $tags = array();
	protected $useTags = false;
	protected $useLabels = false;

	protected $jsWriter;
	
	protected $title;
	protected $labels= array();
	protected $files = array();

	public function __construct($data, $title = null, JsWriterAbstract $jsWriter)
	{
		self::$count++;

		$tagcount = 0;
		foreach($data as $datum) {
			if(is_array($datum) && count($datum) >= 2) {
				$this->useTags = true;
				$this->data[] = array_shift($datum);
				$this->tags[] = array_shift($datum);
			} else {
				$this->data[] = $datum;
				if(count($this->tags) > 0) {
					$this->tags[] = end($this->tags) + 1;
				} else {
					$this->tags[] = 1;
				}
			}
			$tagcount++;
		}

		if(isset($title)) {
			$this->title = $title;
		} else {
			$this->title = 'Series ' . self::$count;
		}

		$this->jsWriter = $jsWriter;
		$this->jsWriter->initializeSeries($this);
	}

	public function getFiles()
	{
		return $this->files;
	}

	public function setSteps($start, $step)
	{
		$num = $start;
		$this->tags = array();

		foreach($this->data as $item) {
			$this->tags[] = $num;
			$num += $step;
		}
	}

	public function setShadow($opts = array('use'=>true, 
                                            'angle'=>45, 
                                            'offset'=>1.25, 
                                            'depth'=>3, 
                                            'alpha'=>0.1))
	{
	    if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Shadowable ) {
	        $this->jsWriter->setShadow($this->getTitle(), $opts);
	    }
	    
		return $this;
	}

	public function setFill($opts = array('use' => true, 
                                                   'stroke' => false, 
                                                   'color' => null, 
                                                   'alpha' => null
                                                  ))
	{
        if ( $this->jsWriter instanceOf \Altamira\JsWriter\Ability\Fillable ) {
    	    $this->jsWriter->setFill($this->getTitle(), $opts);
        }
	    
		return $this;
	}

	public function getData($tags = false)
	{
		if($tags || $this->useTags) {
			$data = array();
			$tags = $this->tags;
			foreach($this->data as $datum) {
				if(is_array($datum)) {
					$item = $datum;
					$item[] = array_shift($tags);
				} else {
					$item = array($datum, array_shift($tags));
				}
				
				$data[] = $item;
			}
			return $data;
		} else {
			return $this->data;
		}
	}

	public function setTitle($title)
	{
		$this->title = $title;

		return $this;
	}

	public function useLabels($labels = array())
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->useTags = true;
    		$this->useLabels = true;
    		$this->jsWriter->useSeriesLabels($this, $labels);
            $this->jsWriter->setSeriesOption($this, 'pointLabels', array('show' => true, 'edgeTolerance' => 3));
	    }

		return $this;
	}

	// @todo this logic should probably be in the jswriter
	public function setLabelSetting($name, $value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Labelable) {
    		$this->jsWriter->setSeriesLabelSetting($this, $name, $value);
	    }
		
		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setOption($name, $value)
	{
		$this->jsWriter->setSeriesOption($this, $name, $value);

		return $this;
	}
	
	public function getOption($option)
	{
	    return $this->jsWriter->getSeriesOption($this->getTitle(), $option);
	}

	public function getOptions()
	{
        return $this->jsWriter->getOptionsForSeries($this->getTitle());
	}
	
	public function usesLabels()
	{
	    return isset($this->useLabels) && $this->useLabels === true;
	}
	
	public function getUseTags()
	{
	    return $this->useTags;
	}
	
	public function setLineWidth($val)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesLineWidth($this, $val);
	    }
	    return $this;
	}
	
	public function showLine($bool = true)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
    	    $this->jsWriter->setSeriesShowLine($this, $bool);
	    }
	    return $this;
	}
	
	public function showMarker($bool = true)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesShowMarker($this, $bool);
	    }
	    return $this;
	}
	
	public function setMarkerStyle($value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerStyle($this, $value);
	    }
	    return $this;
	}
	
	public function setMarkerSize($value)
	{
	    if ($this->jsWriter instanceOf \Altamira\JsWriter\Ability\Lineable ) {
	        $this->jsWriter->setSeriesMarkerSize($this, $value);
	    }
	    return $this;
	}
	
	public function setType($type)
	{
	    $this->jsWriter->setType($type, $this);
	    
	    return $this;
	}
}
