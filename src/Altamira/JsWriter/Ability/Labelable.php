<?php

namespace Altamira\JsWriter\Ability;

interface Labelable
{
    
    public function useSeriesLabels( $seriesTitle );
    
    public function setSeriesLabelSetting( $seriesTitle, $name, $value );
    
}