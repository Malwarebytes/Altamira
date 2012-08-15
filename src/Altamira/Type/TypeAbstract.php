<?php

namespace Altamira\Type;

abstract class TypeAbstract
{
	protected $pluginFiles = array();
	protected $renderer;
	protected $options = array();
    protected $series;
	
	protected $allowedRendererOptions = array();
	
	public function __construct(\Altamira\JsWriter\JsWriterAbstract $jsWriter)
	{
	    
	    $config = \parse_ini_file(__DIR__.'/../Type/TypeConfig.ini', true);
	    
	    $libConfig = $config[strtolower($jsWriter->getLibrary())];

	    $class = end(explode('\\', strtolower(get_class($this))));
	    
	    foreach ( preg_grep("/$class\./i", array_keys($libConfig)) as $key ) {
	        $attribute = preg_replace("/{$class}\./i", '', $key);
	        $this->{$attribute} = $libConfig[$key];
	    }
	}

	public function getFiles()
	{
		return $this->pluginFiles;
	}

	public function getRenderer()
	{
		if(isset($this->renderer)) {
			return '#' . $this->renderer . '#';
		} else {
			return null;
		}
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function getSeriesOptions()
	{
		return array();
	}

	public function getRendererOptions()
	{
		$opts = array();
		foreach($this->allowedRendererOptions as $opt) {
			if(isset($this->options[$opt]))
				$opts[$opt] = $this->options[$opt];
		}
		return $opts;
	}

	public function getUseTags()
	{
		return false;
	}

	public function setOption($name, $value)
	{
		$this->options[$name] = $value;

		return $this;
	}
}

?>