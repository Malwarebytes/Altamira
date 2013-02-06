<?php 
include(__DIR__ . '/autoload.php');

ini_set( 'display_errors', 'on' );
error_reporting( E_ALL );

use Altamira\Chart;
use Altamira\ChartIterator;
use Altamira\Series;
use Altamira\ChartRenderer;
use Altamira\Config;
use Altamira\ChartDatum;

\Altamira\Config::setConfigFile( 'altamira-config.ini' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\SVGRenderer' );
\Altamira\ChartRenderer::pushRenderer( '\Altamira\ChartRenderer\DefaultRenderer' );
$library = 'd3';

$charts = array();

$chart = new Chart('chart1', $library);
$chart->addSeries( $chart->createSeries( ChartDatum\TwoDimensionalPointFactory::getFromXValues( array( 1, 2, 3, 4, 5 ) ), 'Series A' ) );
$chart->setLegend( array( 'on' => false ) )->useCursor()->useCursor();
$charts[] = $chart;

$points = array( 'golf clubs' => 1, 'golf shoes' => 5, 'holes' => 18, 'strokes' => 32 );
$chart = new Chart('chart2', $library );
$chart->setType( 'Pie' );
$data = ChartDatum\ScalarValueFactory::getFromAssociativeArray( $points );
$series = $chart->createSeries( $data );
$series->useLabels(); // since labels are on by default, this turns it off :-\
$chart->addSeries( $series );
$charts[] = $chart;

// this is an option we can set to move the donut labels out
$calls = array( "chart.donutLabelsOutside(true);" );
$nested = array( array( 45, 'cool' ), array( 25, 'cold' ), array( '60', 'temperate' ), array( '75', 'warm' ), array( '90', 'hot' ) );
$chart = new Chart('chart3', $library );
$chart->setType( 'Donut' );
$chart->addSeries( $chart->createSeries( ChartDatum\ScalarValueFactory::getFromNestedArray( $nested ) ) );
$chart->getJsWriter()->pushExtraFunctionCalls( $calls );
$charts[] = $chart;

$tuples = array( 
        array('Screws', 4, 7, 5),
        array('Nails', 5, 3, 6),
        array('Bolts', 4, 5, 7),
        array('Nuts', 3, 4, 6),
        array('Washers', 3, 2, 5),
        array('Pliers', 4, 1, 5),
        array('Hammers', 4, 6, 6)
        );
$chart = new Chart('chart4', $library );
$chart->addSeries( $chart->createManySeries( $tuples, array( '\Altamira\ChartDatum\BubbleFactory', 'getBubbleDatumFromTupleSet' ) ) );
$chart->setType( 'Bubble' );
$charts[] = $chart;

$chart5 = new Chart('chart5', $library);
$chart5->setType('Bar', array( 'horizontal' => true ) );
$array1 = array( 'A'=>1, 'B'=>4, 'C'=>8, 'D'=>2, 'E'=>1, 'F'=>5);
$array2 = array( 'A'=>6, 'B'=>3, 'C'=>2, 'D'=>8, 'E'=>9, 'F'=>4);
$series1 = $chart5->createSeries( ChartDatum\ScalarValueFactory::getFromAssociativeArray( $array1 ), 'Runs');
$series2 = $chart5->createSeries( ChartDatum\ScalarValueFactory::getFromAssociativeArray( $array2 ), 'Walks');
$series1->setFill( array( 'use'=>true, 'stroke'=>'rgba(20, 255, 20, 1)' ) );
$series2->setFill( array( 'color' => '#dd2222' ) );
$chart5->setTitle('Horizontal Bar Chart')->
    addSeries( $series1 )->
    addSeries( $series2 )
    ;
$charts[] = $chart5;

$array1 = array(1, 4, 8, 2, 1, 5);
$array2 = array(3, 3, 5, 4, 2, 6);
$alphas = range('A', 'Z');
$num = max(count($array1), count($array2));
for($i = 0; $i < $num; $i++) {
    $total = $array1[$i] + $array2[$i];
    $array1[$alphas[$i]] = (int) ($array1[$i] / $total * 100);
    $array2[$alphas[$i]] = (int) ($array2[$i] / $total * 100);
    unset( $array1[$i] );
    unset( $array2[$i] );
}

$chart = new Chart('chart6', $library);
$chart->setTitle('Vertical Stack Chart')->
    addSeries($chart->createSeries(ChartDatum\ScalarValueFactory::getFromAssociativeArray( $array1 ), 'Is'))->
    addSeries($chart->createSeries(ChartDatum\ScalarValueFactory::getFromAssociativeArray( $array2 ), 'Is Not'))->
    setType('Bar', array( 'stackSeries' => true ) );
$charts[] = $chart;

$arr1 = array( "SF" => 50, "Oakland" => 75, "Marin" => 2, "Millbrae" => 10, "San Bruno" => 5 );
$arr2 = array( "SF" => 15, "Oakland" => 125, "Marin" => 12, "Millbrae" => 4, "San Bruno" => 12 );
$chart = new Chart( 'chart7', $library );
$chart->setType( "Bar" )->setTitle( 'Another Bar Chart' );
$chart->addSeries( $chart->createSeries( ChartDatum\ScalarValueFactory::getFromAssociativeArray( $arr1 ), 'Players' ) )
      ->addSeries( $chart->createSeries( ChartDatum\ScalarValueFactory::getFromAssociativeArray( $arr2 ), 'Hustlers' ) );
$charts[] = $chart;

$chart = new Chart( 'chart8', $library );
$chart->setTitle( 'Line Chart With Zooming' );
$chart->addSeries( 
        $chart->createSeries( 
                ChartDatum\TwoDimensionalPointFactory::getFromNested( 
                        array_map( function($x) { return array( $x, $x*$x ); }, range( 0, 100 ) ) 
                        ), 
                'Whatever' 
                ) 
        )
       ->useZooming();
$charts[] = $chart;

$chartIterator = new ChartIterator( $charts );

?>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="http://d3js.org/d3.v2.min.js"></script>

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
