<?php
// This code is part of /alarm and /beamer

require_once 'init.php';
JotihuntUtils::requireLogin();
$result = array();
$result['vossen'] = array();

// Vossen
function showStatusByTeamLetter($deelgebiedName) {
    global $driver, $result;
    
    $vos = $driver->getVosIncludingLocations($deelgebiedName);
    if (!$vos) {
        return;
    }
    $vos_locations = $vos->getLocations();

    $result['vossen'][$vos->getDeelgebied()] = array();
    $result['vossen'][$vos->getDeelgebied()]['id'] = intval($vos->getId());
    $result['vossen'][$vos->getDeelgebied()]['name'] = $vos->getName();
    $result['vossen'][$vos->getDeelgebied()]['date'] = 0;
    $result['vossen'][$vos->getDeelgebied()]['address'] = 0;
    $result['vossen'][$vos->getDeelgebied()]['lat'] = 0;
    $result['vossen'][$vos->getDeelgebied()]['lng'] = 0;
    $result['vossen'][$vos->getDeelgebied()]['lastHuntTime'] = 0;
    
    $result['vossen'][$vos->getDeelgebied()]['status'] = $vos->getStatus();
    if (! empty($vos_locations) && count($vos_locations)) {
        $last_location = $vos->getLastLocation();
        $adres = $last_location->getAddress();
        
        $date = date('Y-m-d H:i:s', strtotime($last_location->getDate()));
        if(empty($date)) {
            $date = "0";
        }
        
        $address = $last_location->getAddress();
        if(empty($address)) {
            $address = "0";
        }
        
        $lat = $last_location->getLatitude();
        if(empty($lat)) {
            $lat = 0;
        }
        
        $lng = $last_location->getLongitude();
        if(empty($lng)) {
            $lng = 0;
        }
        
        $last_hunt_location = $vos->getLastHuntLocation();
        $lastHuntTime = 0;
        if(!empty($last_hunt_location)) {
            $lastHuntTime = date('Y-m-d H:i:s', strtotime($last_hunt_location->getDate()));
        }
        
        $result['vossen'][$vos->getDeelgebied()]['date'] = $date;
        $result['vossen'][$vos->getDeelgebied()]['address'] = $address;
        $result['vossen'][$vos->getDeelgebied()]['lat'] = floatval($lat);
        $result['vossen'][$vos->getDeelgebied()]['lng'] = floatval($lng);
        $result['vossen'][$vos->getDeelgebied()]['lastHuntTime'] = $lastHuntTime;
    }
}

foreach ($driver->getAllDeelgebieden() as $deelgebied) {
    showStatusByTeamLetter($deelgebied->getName());
}

// Scorelijst
$result['plaats'] = 0;
$orgScore = $driver->getScore();

if($orgScore){
    $result['plaats'] = intval($orgScore->getPlaats());
}

// Laatste bericht
$lastbericht = $driver->getLastBericht();

$result['lastbericht'] = 'Geen berichten bekend';
if ($lastbericht) {
    $result['lastbericht'] = $lastbericht->getType().": ".$lastbericht->getTitel();
}

// Laatste hunt
$lasthunt = $driver->getLastHunt();
$result['lasthunt'] = 'Er zijn nog geen hunts, nog niet... :P';
if(!empty($lasthunt) && !empty($lasthunt['hunter_id'])) {
    $huntrider = $driver->getRider($lasthunt['hunter_id']);
    $user = $driver->getUserById($huntrider->getUserId());
    
    $location = $driver->getLocation($lasthunt['vossentracker_id']);
    $vos = $driver->getTeamById($location->getVossenId());
    $result['lasthunt'] = $vos->getName()." - ".$user->getDisplayName()." (".strftime("%a %H:%M",strtotime($lasthunt['time'])).")";
}

header('Content-type: application/json');
echo json_encode($result);
?>