<?php
/**
 * Class definition for \Altamira\Type\D3\D3TypeAbstract
 */
namespace Altamira\Type\D3;
use Altamira\Type\TypeAbstract;
/**
 * Abstract class that enforces that types are responsible for rendering in D3
 * Why is this, you may ask? Well, it's because D3 is not a chart abstraction library;
 * it is a framework for manipulating data in the document. This means that even 
 * rendering simple lines must be fully specified here in Altamira
 */
abstract class D3TypeAbstract extends TypeAbstract
{
    /**
     * Specifies the model used to generate this chart
     * @var string
     */
    protected $chartDirective;
    
    /**
     * Generates the appropriate directive for generating the correct NVD3 model
     * @return string;
     */
    public function getChart()
    {
        if (! isset( $this->chartDirective ) ) {
            throw new \Exception( "Every instance of D3TypeAbstract should have a chartDirective value specified" );
        }
        return $this->chartDirective;
    }
    
    public function setStrokeColor( $name = null, $seriesIndex = null, $val = null) { return ''; }
}