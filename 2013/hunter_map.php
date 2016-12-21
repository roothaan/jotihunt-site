<?php
require_once 'init.php';
$authMgr->requireAdmin();
require_once BASE_DIR . 'header.php';
define('opoiLoaded',true);

require_once BASE_DIR . 'includes/make_map_js.include.php';
require_once CLASS_DIR . 'jotihunt/MapOptions.class.php';

$amountOfLocations = $driver->getTotalAmountOfRiderLocations();
echo '<h1>Totaal aantal locaties geregistreerd: <strong>' . $amountOfLocations . '</strong></h1><hr />';


$ridercollection = $driver->getAllRiders();
if ($authMgr->isSuperAdmin()) {
    $ridercollection = $driver->getAllRidersSu();
}

$riderId = null;
if (isset($_GET ['hunterId'])) {
    foreach ( $ridercollection as $rider ) {
        if ($rider->getId() == $_GET ['hunterId']) {
            $riderId = $_GET ['hunterId'];
        }
    }
}
?>
<form action="<?php echo BASE_URL; ?>hunter_map.php" method="GET">
    Kies een hunter: <input type="hidden" name="from" id="from" value="<?php if (isset($_GET['from'])) { echo intval($_GET['from']); } ?>" /> <input type="hidden" name="to" id="to" value="<?php if (isset($_GET['to'])) { echo intval($_GET['to']); } ?>" />
<?php
$sep = '';
foreach ( $ridercollection as $rider ) {
    $currentRider = $rider->getId() == $riderId;
    $locationCount = count($driver->getRiderLocation($rider->getId()));
    if ($locationCount > 0) {
        echo $sep;
        if ('' === $sep) {
            $sep = ' | ';
        }

        echo '<input type="radio" name="hunterId" ' . ($currentRider ? 'checked="checked"' : '') . ' id="hunter_' . $rider->getId() . '" value="' . $rider->getId() . '" />';
        if ($currentRider) {
            echo '<strong>';
        }

        echo '<label for="hunter_' . $rider->getId() . '">' . $rider->getUser()->getDisplayname() . ' <small>('.$locationCount.')</small></label>';
        if ($currentRider) {
            echo '</strong>';
        }
    }
}
echo '</p>';
?>

<table>
        <thead>
            <tr>
                <th>Van</th>
                <th>Tot</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><div id="datetimepicker_from"></div></td>
                <td><div id="datetimepicker_to"></div></td>
            </tr>
        </tbody>
    </table>

    <script type="text/javascript">
var momentFormat = 'YYYY/MM/DD HH:mm';
<?php
$minimum = 1388534400;
if (isset($_GET ['from']) && $_GET ['from'] > $minimum) {
    echo 'var d_from = moment.unix(' . intval($_GET ['from']) . ').format(momentFormat);';
} else {
    echo 'var d_from = moment().subtract(\'days\', 1).format(momentFormat);';
}
if (isset($_GET ['to']) && $_GET ['to'] > $minimum) {
    echo 'var d_to = moment.unix(' . intval($_GET ['to']) . ').format(momentFormat);';
} else {
    echo 'var d_to = moment().format(momentFormat);';
}
?>

$('#datetimepicker_from').datetimepicker({
    value: d_from,
    format: 'Y/m/d H:i',
    roundTime:'floor',
	onChangeDateTime:function( currentDateTime ){
		$('#from').val(currentDateTime.getTime() / 1000);
	},
	onGenerate:function( currentDateTime ){
		$('#from[value=""]').val(currentDateTime.getTime() / 1000);
	},
	inline:true
	});
$('#datetimepicker_to').datetimepicker({
    value: d_to,
    format: 'Y/m/d H:i',
    roundTime:'floor',
	onChangeDateTime:function( currentDateTime ){
		$('#to').val(currentDateTime.getTime() / 1000);
	},
	onGenerate:function( currentDateTime ){
		$('#to[value=""]').val(currentDateTime.getTime() / 1000);
	},
	inline:true
	});
</script>
    <br /> <input type="submit" value="toon" />
</form>
<hr />
<?php

if (null != $riderId) {
    $riderTeam = $driver->getRider($riderId);
    $graphPoints = $driver->getRiderLocationGraph($riderTeam->getId());
    $locations = $driver->getRiderLocation($riderTeam->getId());
    
    echo '<h1>' . $riderTeam->getUser()->getDisplayname() . '</h1>';
    echo '<em>Aantal locaties voor ' . $riderTeam->getUser()->getDisplayname() . ': <strong>' . count($locations) . '</strong></em>';
    if (count($locations) > 0) {
        ?>

<div id="chart_div" style="width: 1200px; height: 300px;"></div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Moment', 'Locaties'],
          <?php
        $points = 0;
        foreach ( $graphPoints as $graphPoint ) {
            $total = $graphPoint ['points'];
            $current = $total - $points;
            echo '[\'' . $graphPoint ['hour_slice'] . '\',  ' . $current . '],';
            $points += $current;
        }
        ?>
        ]);

        var options = {
          title: 'Locaties per uur'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
<?php
        echo '<div id="vos_map">
    <div id="map" style="width: 1200px; height: 600px;"></div>';
        $mapOptions = new MapOptions();
        $mapOptions->showVos = false;
        $mapOptions->hunter = $riderId;
        $mapOptions->showHunter = true;
        $mapOptions->showHuntersLastLocation = false;
        if (isset($_GET ['from'])) {
            $mapOptions->hunterFrom = intval($_GET ['from']);
        }
        if (isset($_GET ['to'])) {
            $mapOptions->hunterTo = intval($_GET ['to']);
        }
        make_map($mapOptions);
        echo '</div>';
    } else {
        echo '<div><strong>Geen locaties bekend</strong></div>';
    }
}

require_once BASE_DIR . 'footer.php';
?>