<?php
/**
 * Class definition for \Altamira\Type\D3\Pie
 */
namespace Altamira\Type\D3;
/**
 * Class responsible for generating pie charts
 */
class Pie extends D3TypeAbstract
{
    const TYPE = 'Pie';
    
    /**
     * NVD3 pie model
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.pieChart().x(function(d) { return d.label }).y(function(d) { return d.value });\n";
}