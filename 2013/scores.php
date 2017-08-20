<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
JotihuntUtils::requireLogin();

$scorecollection = $driver->getScoreCollection();
$firstGroep = array_shift(array_values($scorecollection));

$keyArray = array ();
if (null == $firstGroep) {
    echo '<h1>Scores</h1>';
    echo '<p>Er zijn (nog?) geen scores bekend</p>';
} else {

    foreach ( $firstGroep as $key => $score ) {
        $keyArray [] = strftime('%a %R', $key);
    }
    ?>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 400px; height: 800px; margin: 0 auto"></div>

<script>
$(function () {
    $('#container').highcharts({
        title: {
            text: 'Scores Jotihunt <?= date('Y') ?>',
            x: -20 //center
        },
        subtitle: {
            text: 'Gemaakt door de Roothaangroep Doetinchem',
            x: -20
        },
        xAxis: {
            categories: ["start","<?= implode('","',$keyArray) ?>"],
        },
        yAxis: {
            title: {
                text: 'Punten'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' Punten'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        
        
        series : [
    	    <?php
        foreach ( $scorecollection as $groepsnaam => $groepArray ) {
            ?>
    	    {
    	        name: '<?= addslashes($groepsnaam) ?>',
    			data : [0
    			    <?php
            foreach ( $groepArray as $groep ) {
                echo ', ' . $groep->getPlaats();
            }
            ?>
    		    ]
    	    },
    	    <?php
        }
        ?>
    	]
    });
});
</script>
<?php
}
?>
