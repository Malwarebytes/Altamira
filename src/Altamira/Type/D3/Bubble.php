<?php
/**
 * Class definition for \Altamira\Type\D3\Bubble
 */
namespace Altamira\Type\D3;
/**
 * Bubble or scatter plot type
 * @author relwell
 */
class Bubble extends D3TypeAbstract
{
    /**
     * Defintes the scatter plot model
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.scatterChart().showDistX(true).showDistY(true).color(d3.scale.category10().range())\n";
}