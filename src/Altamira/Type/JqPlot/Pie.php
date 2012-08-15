<?php

namespace Altamira\Type\JqPlot;

class Pie extends \Altamira\Type\TypeAbstract
{
	protected $allowedRendererOptions = array();

	public function getUseTags()
	{
		return true;
	}
}

?>