<?php
/**
 * Class definition for \Altamira\Type\D3\Donut
 */
namespace Altamira\Type\D3;
/**
 * Donut is almost the same as pie
 * @author relwell
 */
class Donut extends D3TypeAbstract
{
    /**
     * NVD3 pie model with donut activated
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.pieChart().x(function(d) { return d.label }).y(function(d) { return d.value }).donut(true);\n";
}