<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

// http://www.zonums.com/online/kmlArea/

function getStyle($accuracy = 0.0) {
    $a = floatval($accuracy);
    if ($a === 0.0) {
        return '#blueIcon';
    }
    if ($a <= 5.0) {
        return '#greenIcon';
    }
    if ($a <= 10.0) {
        return '#yellowIcon';
    }
    return '#redIcon';
}

function getColor($accuracy = 0.0) {
    $a = floatval($accuracy);
    if ($a === 0.0) {
        // Blue
        return 'FF0000FF';
    }
    if ($a <= 5.0) {
        // Green
        return 'FF00FF00';
    }
    if ($a <= 10.0) {
        // Yellow
        return 'FFFFFF00';
    }
    // Red
    return 'FFFF0000';
}

function getStyles() {
    $styles = '';
    $styles .= '<Style id="line">
      <LineStyle>
        <color>7f0000B2</color>
        <width>3</width>
      </LineStyle>
      <PolyStyle>
        <color>7f00ff00</color>
      </PolyStyle>
    </Style>';
    return $styles;
}

$sessionId = $authMgr->getSessionId();
if (isset($_GET['sessionId'])) {
    $sessionId = $_GET['sessionId'];
}
$authMgr->setSessionId($sessionId);


header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<kml xmlns="http://www.opengis.net/kml/2.2">';
echo '<Document>';
echo getStyles();
echo '<name>Jotihunt ' + date('Y') +'</name>';
echo '<description></description>';

$max = 20;
$offset = (isset($_GET['offset']) ? $_GET['offset'] : 0 );

// Get all riders
$riders = array();
if (isset($_GET['hunterId'])) {
    $riders[] = $driver->getRider($_GET['hunterId']);
} else {
    $riders = $driver->getAllRiders();
}
foreach ($riders as $riderTeam) {
    if (null == $riderTeam) {
        continue;
    }

    if (isset($_GET ['from']) && isset($_GET ['to'])) {
        $locations = $driver->getRiderLocationWithDateRange($riderTeam->getId(), $_GET ['from'], $_GET ['to']);
    } else {
        $locations = $driver->getRiderLocation($riderTeam->getId());
    }

    if (count($locations) == 0) {
        continue;
    }
    
    if ($offset > 0) {
        $offset--;
        continue;
    }
    $max--;
    if ($max == 0) {
        break;
    }
    
    // Line
    echo '<Placemark id="coordinates_'.$riderTeam->getUser()->getId().'">';
    echo '<name>'.$riderTeam->getUser()->getDisplayName().'</name>';
    echo '<styleUrl>#line</styleUrl>';
    echo '<LineString>';
    echo '<extrude>1</extrude>';
    echo '<tessellate>1</tessellate>';
    echo '<altitudeMode>absolute</altitudeMode>';
    echo '<coordinates>';
    foreach ( $locations as $location ) {
        echo $location->getLongitude() . ',' . $location->getLatitude() . ',2357 ';
    }
    echo '</coordinates>';
    echo '</LineString>';
    echo '</Placemark>';
}

echo '</Document></kml>';