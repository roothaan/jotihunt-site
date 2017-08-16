<?php
require_once 'init.php';

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
    $styles .= '<Style id="greenIcon"><IconStyle><scale>1.0</scale><Icon><href>https://www.google.com/mapfiles/ms/micons/green.png</href></Icon></IconStyle></Style>';
    $styles .= '<Style id="blueIcon"><IconStyle><scale>1.0</scale><Icon><href>https://www.google.com/mapfiles/ms/micons/blue.png</href></Icon></IconStyle></Style>';
    $styles .= '<Style id="yellowIcon"><IconStyle><scale>1.0</scale><Icon><href>https://www.google.com/mapfiles/ms/micons/yellow.png</href></Icon></IconStyle></Style>';
    $styles .= '<Style id="redIcon"><IconStyle><scale>1.0</scale><Icon><href>https://www.google.com/mapfiles/ms/micons/red.png</href></Icon></IconStyle></Style>';
    return $styles;
}

if (isset($_GET ['riderId'])) {
    $riderId = $_GET ['riderId'];
    $sessionId = $_GET['sessionId'];
    $authMgr->setSessionId($sessionId);
    $riderTeam = $driver->getRider($riderId);
    if (null == $riderTeam) {
        return;
    }
    if (isset($_GET ['from']) && isset($_GET ['to'])) {
        $locations = $driver->getRiderLocationWithDateRange($riderTeam->getId(), $_GET ['from'], $_GET ['to']);
    } else {
        $locations = $driver->getRiderLocation($riderTeam->getId());
    }
    
    //
    header('Content-Type: text/xml');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<kml xmlns="http://www.opengis.net/kml/2.2">';
    echo '<Document>';
    echo getStyles();
    echo '<name>' . $riderTeam->getUser()->getDisplayName() . '</name>';
    echo '<description></description>';
    foreach ( $locations as $location ) {
        echo '<Placemark id="'.$riderTeam->getUser()->getId().'-'. $location->getId() . '">';
        echo '<name>Location ID: '.$location->getId().'</name>';
        // echo '<styleUrl>' . getStyle($location->getAccuracy()) . '</styleUrl>';
        // echo '<Style><BalloonStyle><bgColor>' . getColor($location->getAccuracy()) . '</bgColor></BalloonStyle></Style>';
        echo '<Point><coordinates>';
        echo $location->getLongitude() . ',' . $location->getLatitude();
        echo '</coordinates></Point>';
        echo '<description><![CDATA[';
        echo '<div>Datum: ' . $location->getTime() . '<br />Provider: ' . $location->getProvider() . ' (' . $location->getAccuracy() . ')</div>';
        echo ']]></description>';
        echo '</Placemark>';
    }
    
    // Line
    echo '<Placemark id="coordinates">';
    echo '<name>Coordinates</name>';
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
    
    echo '</Document></kml>';
}