<?php

namespace Altamira\JsWriter\Ability;

interface Labelable
{
    
    public function useSeriesLabels( $seriesTitle, array $labels = array() );
    
    public function setSeriesLabelSetting( $seriesTitle, $name, $value );
    
}