<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL&E_NOTICE);
include(__DIR__ . '/autoload.php');

use Altamira\Chart;
use Altamira\ChartIterator;
use Altamira\Series;
use Altamira\Series\BubbleSeries;
use Altamira\ChartRenderer;
use Altamira\Config;
use Altamira\ChartDatum\TwoDimensionalPoint;

$config = new Config( 'altamira-config.ini' );

$library = isset($_GET['library']) ? $_GET['library'] : 'jqPlot';

if ($library == 'flot') {
    ChartRenderer::pushRenderer( 'Altamira\ChartRenderer\DefaultRenderer' );
    ChartRenderer::pushRenderer( 'Altamira\ChartRenderer\TitleRenderer' );
}

function make2dPoints1dArray( $oneDimensionalArray, $result = array() ) {
    foreach ($oneDimensionalArray as $x => $y ) {
        $result[] = new Altamira\ChartDatum\TwoDimensionalPoint( array('x' => $x+1, 'y' => $y ) );
    }
    return $result;
}

function make2dPointsNestedArray( $nestedArray, $result = array() ) {
    foreach ( $nestedArray as $array ) {
        $result[] = new Altamira\ChartDatum\TwoDimensionalPoint( array('x' => $array[0], 'y' => $array[1] ) );
    }
    return $result;
}

$chart = new Chart('chart1', $library);

$series1Points = make2dPoints1dArray( array(2, 8, 5, 3, 8, 9, 7, 8, 4, 2, 1, 6) );

$series2Points = make2dPoints1dArray( array(7, 3, 7, 8, 2, 3, 1, 2, 5, 7, 8, 3) );

$chart->addSeries($chart->createSeries($series1Points, 'Sales'))->
    addSeries($chart->createSeries($series2Points, 'Returns'))->
    setTitle('Basic Line Chart')->
    setAxisOptions('y', 'formatString', '$%d')->
    setAxisOptions('x', 'tickInterval', 1)->
    setAxisOptions('x', 'min', 0)->
    setLegend(array('on'=>true));

$seriesPoints = make2dPointsNestedArray( array( array('1/4/1990', 850),
                                                array('2/27/1991', 427),
                                                array('1/6/1994', 990),
                                                array('8/6/1994', 127),
                                                array('12/25/1995', 325) ) 
                                        );

//@todo this chart's labels aren't showing here in jqplot
$chart2 = new Chart('chart2', $library);
$series = $chart2->createSeries($seriesPoints, 'Measured Readings');
$series->useLabels(array('a', 'b', 'c', 'd', 'e'))->
    setLabelSetting('location', 'w')->
    setLabelSetting('xpadding', 8)->
    setLabelSetting('ypadding', 8);
$chart2->setTitle('Line Chart With Highlights and Labels')->
    addSeries($series)->
    useDates()->
    useHighlighting();

$chart3 = new Chart('chart3', $library);
$seriesA = $chart3->createSeries(make2dPoints1dArray( array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10) ), 'First');
$seriesB = $chart3->createSeries(make2dPoints1dArray( array(1, 10, 2, 9, 3, 8, 4, 7, 5, 6) ), 'Second');
$seriesC = $chart3->createSeries(make2dPoints1dArray( array(10, 7, 6, 5, 3, 1, 3, 5, 6, 7) ), 'Third');


// These styles are only supported by Flot
$seriesA->showMarker(false)->
    setLineWidth(8);
$seriesB->setMarkerStyle('filledSquare')->
    showLine(false);
$seriesC->setLineWidth(1)->
    setMarkerSize(15)->
    setMarkerStyle('diamond');

$chart3->setTitle('Line Chart With Custom Formats And Zoom (drag to zoom, double-click to reset)')->
    addSeries($seriesA)->
    addSeries($seriesB)->
    addSeries($seriesC)->
    useZooming();
/**
$chart4 = new Chart('chart4', $library);
$chart4->setTitle('Horizontal Bar Chart')->
    addSeries($chart4->createSeries(array(1, 4, 8, 2, 1, 5), 'Runs'))->
    addSeries($chart4->createSeries(array(3, 3, 5, 4, 2, 6), 'Walks'))->
    setType('Bar')->
    setTypeOption('horizontal', true)->
    setAxisTicks('y', array('1st Inning', '2nd Inning', '3rd Inning', '4th Inning', '5th Inning', '6th Inning'))->
    setLegend(array('on'=>true, 'location'=>'se', 'x'=>5, 'y'=>5));

$chart5 = new Chart('chart5', $library);
$chart5->setTitle('Pie Chart')->
    addSeries($chart5->createManySeries(array(array('Pots', 7), array('Pans', 5), array('Spoons', 2), array('Knives', 5), array('Forks', 12)), 'Utensils'))->
    setType('Pie')->
    setLegend();

$chart6 = new Chart('chart6', $library);
$chart6->setTitle('Donut Chart With Custom Colors And Labels')->
    addSeries($chart6->createManySeries(array(array('Metals', 3), array('Plastics', 5), array('Wood', 2), array('Glass', 7), array('Paper', 9)), 'Internal'))->
    addSeries($chart6->createManySeries(array(array('Metals', 4), array('Plastics', 2), array('Wood', 5), array('Glass', 4), array('Paper', 12)), 'External'))->
    setSeriesColors(array('#dd3333', '#d465f1', '#aa2211', '#3377aa', '#6699bb', '#9933aa'))->
    setType('Donut')->
    setLegend()->
    setTypeOption('sliceMargin', 3)->
    setTypeOption('showDataLabels', true);

$chart7 = new Chart('chart7', $library);
$chart7->addSeries($chart7->createManySeries(
    array(  array(4, 7, 5, 'Screws'),
        array(5, 3, 6, 'Nails'),
        array(4, 5, 7, 'Bolts'),
        array(3.5, 4, 6, 'Nuts'),
        array(3, 2, 5, 'Washers'),
        array(4, 1, 5, 'Pliers'),
        array(4.5, 6, 6, 'Hammers')), null, 'Bubble'))->
    setTitle('Bubble Chart')->
    setType('Bubble')->
    setTypeOption('bubbleAlpha', .5)->
    setTypeOption('highlightAlpha', .7);

$array1 = array(1, 4, 8, 2, 1, 5);
$array2 = array(3, 3, 5, 4, 2, 6);

$num = max(count($array1), count($array2));
for($i = 0; $i < $num; $i++) {
    $total = $array1[$i] + $array2[$i];
    $array1[$i] = $array1[$i] / $total * 100;
    $array2[$i] = $array2[$i] / $total * 100;
}

$chart8 = new Chart('chart8', $library);
$chart8->setTitle('Vertical Stack Chart')->
    addSeries($chart8->createSeries($array1, 'Is'))->
    addSeries($chart8->createSeries($array2, 'Is Not'))->
    setType('Bar')->
    setLegend(array('on'=>true, 'location'=>'se', 'x'=>5, 'y'=>5))->
    setAxisOptions('y', 'max', 100)->
    setTypeOption('stackSeries', true);
**/
$charts = array($chart,
                $chart2, 
                $chart3, 
/*                $chart4, 
                $chart5, 
                $chart6, 
                $chart7, 
                $chart8
*/                );

$chartIterator = new ChartIterator($charts, $config);

?>
<html>
<head>
<script type="text/javascript" src="js/jquery-1.7.2.js"></script>

<!-- enable this if you want to display the charts on IE -->
<!--<script type="text/javascript" src="js/excanvas.js"></script>-->

<?php $chartIterator->renderLibraries()
                 ->renderCss()
                 ->renderPlugins() ?>
</head>
<body>

<?php  
while ( $chartIterator->valid() ) {
    
    echo $chartIterator->current()->getDiv();
    $chartIterator->next();
    
}
?>

<?php $chartIterator->renderScripts() ?>

</body>
</html>