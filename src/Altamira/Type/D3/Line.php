<?php
/**
 * Class definition for \Altamira\Type\D3\Line
 */
namespace Altamira\Type\D3;
/**
 * This type stores logic needed to render lines with NVD3
 * @author relwell
 */
class Line extends \Altamira\Type\D3\D3TypeAbstract
{
    const TYPE = 'Line';
    
    /**
     * NVD3 lineChart model
     * @var string
     */
    protected $chartDirective = "var chart = nv.models.lineChart();\n";
}