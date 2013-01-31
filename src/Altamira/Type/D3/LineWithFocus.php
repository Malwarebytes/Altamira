<?php
/**
 * Class definition for \Altamira\Type\D3\LineWithFocus
 */
namespace Altamira\Type\D3;
/**
 * This is how we do zooming in D3
 */
class LineWithFocus extends D3TypeAbstract
{
    /**
     * NVD3 linewithfocus model
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.lineWithFocusChart();\n";
}