<?php

namespace Altamira\JsWriter\Ability;

interface Lineable
{
    /**
     * Stores line width information for series
     * @param string $seriesTitle
     * @param int    $value
     */
    public function setSeriesLineWidth( $seriesTitle, $value );
    /**
     * Determines whether to show the line for a series
     * @param string $seriesTitle
     * @param bool $bool
     */
    public function setSeriesShowLine( $seriesTitle, $bool );
    /**
     * Determines whether to show markers for the provided series title
     * @param string $seriesTitle
     * @param bool   $bool
     */
    public function setSeriesShowMarker( $seriesTitle, $bool );
    /**
     * Sets series marker style (e.g. diamond, point, etc)
     * @param string $seriesTitle
     * @param string $value
     */   
    public function setSeriesMarkerStyle( $seriesTitle, $value );
    /**
     * Sets the size of a marker (point) for a given series
     * @param string     $seriesTitle
     * @param int|string $value
     */ 
    public function setSeriesMarkerSize( $seriesTitle, $value );
}