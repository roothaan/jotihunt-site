<?php
require_once 'init.php';
JotihuntUtils::requireLogin();

$removeHunterAfter = 3600;
$staleHunterAfter = 1800;

$hunterlocaties_array = array ();
$hunterlocationcollection = $driver->getLastRiderLocations();
foreach ( $hunterlocationcollection as $riderlocation ) {
    $timeLastLocation = strtotime($riderlocation->getTime());
    $currentTime = time();
    $diff = $currentTime - $timeLastLocation;
    if ($diff > $removeHunterAfter) {
        continue;
    }
    
    $rider_array = array ();
    $hunter = $driver->getRider($riderlocation->getRiderId());
    if (null == $hunter) {
        continue;
    }
    
    $rider_array ['latitude'] = $riderlocation->getLatitude();
    $rider_array ['longitude'] = $riderlocation->getLongitude();
    $rider_array ['naam'] = $hunter->getUser()->getDisplayName();
    $rider_array ['bijrijder'] = $hunter->getBijrijder();
    $rider_array ['formatted_date'] = strftime('%R, %a, %d %b', strtotime($riderlocation->getTime()));
    $rider_array ['tel'] = $hunter->getTel();
    $rider_array ['stale'] = ($diff > $staleHunterAfter ? 'true' : 'false');
    $rider_array ['auto'] = $hunter->getAuto();
    
    $hunterlocaties_array [] = $rider_array;
}

if (defined('DEV_MODE') && DEV_MODE == true) {
    if (sizeof($hunterlocaties_array) == 0) {
        $rider_array = array ();
        $rider_array ['latitude'] = '52.036576456008405';
        $rider_array ['longitude'] = '6.136371488119137';
        $rider_array ['naam'] = 'Test user';
        $rider_array ['bijrijder'] = 'Test bijrijder';
        $rider_array ['formatted_date'] = 'Test datum';
        $rider_array ['tel'] = 'Test tel';
        $rider_array ['stale'] = 'true';
        $rider_array ['auto'] = 'img';
    
        $hunterlocaties_array [] = $rider_array;
    }
}

echo json_encode($hunterlocaties_array);