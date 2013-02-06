<?php

namespace Altamira\ChartDatum;

use Altamira\Chart;

class FactoryStrategy
{
    /**
     * @var Chart
     */
    protected $chart;
    
    protected $dataIsNested = false;
    protected $dataIsScalar = false;
    protected $dataIsAssociative = false;
    
    public function __construct( Chart $chart )
    {
        $this->chart = $chart;
    }
    
    public function buildData( array $data )
    {
        $this->inspectData( $data );
        switch ( $this->chart->getLibrary() ) {
            case \Altamira\JsWriter\Flot::LIBRARY:
            case \Altamira\JsWriter\JqPlot::LIBRARY:
                $result = $this->buildForFlotOrJqPlot( $data );
                break;
            case \Altamira\JsWriter\D3::LIBRARY:
                $result = $this->buildForD3( $data );
                break;
        }
        $this->resetFindings();
        return $result;
    }
    
    protected function buildForFlotOrJqPlot( array $data )
    {
        $type = $this->chart->getJsWriter()->getType();
        if ( $type instanceof \Altamira\Type\TypeAbstract ) {
            $typeOptions = $type->getOptions();
            switch ( $type->getName() ) {
                case 'Bar':
                    if ( isset( $typeOptions['horizontal'] ) && $typeOptions['horizontal'] ) {
                        return TwoDimensionalPointFactory::getFromXValues( $data );
                    }
                    break;
            }
        }
        
        if ( $this->dataIsNested ) {
            return TwoDimensionalPointFactory::getFromNested( $data );
        }
        
        return TwoDimensionalPointFactory::getFromYValues( $data );
    }
    
    protected function buildForD3( array $data )
    {
        //@todo
    }
    
    protected function inspectData( array $data )
    {
        $this->dataIsNested = is_array( $data[0] );
        $this->dataIsScalar = array_values( $data ) == $data;
        $this->dataIsAssociative = array_keys( $data ) !== range( 0, count( $data ) - 1 );
    }
    
    protected function resetFindings()
    {
        $this->dataIsNested = false;
        $this->dataIsScalar = false;
        $this->dataIsAssociative = false;
    }
}