<?php
// This code is part of /alarm and /beamer

require_once 'init.php';
JotihuntUtils::requireLogin();

// Vossen
function showStatusByTeamLetter($deelgebiedName) {
    global $driver;
    
    $vos = $driver->getVosIncludingLocations($deelgebiedName);
    if (!$vos) {
        return;
    }
    $vos_locations = $vos->getLocations();
    
    echo $vos->getStatus() . "|||";
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
            $lat = "0";
        }
        
        $long = $last_location->getLongitude();
        if(empty($long)) {
            $long = "0";
        }
        
        $last_hunt_location = $vos->getLastHuntLocation();
        $lastHuntTime = 0;
        if(!empty($last_hunt_location)) {
            $lastHuntTime = date('Y-m-d H:i:s', strtotime($last_hunt_location->getDate()));
        }
        
        echo $date . "|||" . $address . "|||" . $lat . "|||" . $long . "|||" . $lastHuntTime;
    } else {
        echo "0|||0|||0|||0|||0";
    }
    echo "||||||";
}

foreach ($driver->getAllDeelgebieden() as $deelgebied) {
    showStatusByTeamLetter($deelgebied->getName());
}

// Scorelijst
$orgId = $authMgr->getMyOrganisationId();
$org = $driver->getOrganisationById($orgId);
$orgScore = $driver->getScoreByGroep($org->getName());

if($orgScore){
    echo $orgScore->getPlaats() . "||||||";
}else{
    echo "0||||||";
}

// Laatste bericht
$lastbericht = $driver->getLastBericht();

if ($lastbericht) {
    echo $lastbericht->getType().": ".$lastbericht->getTitel();
} else {
    echo 'Geen berichten bekend';
}

// Laatste hunt
$lasthunt = $driver->getLastHunt();
if(!empty($lasthunt) && !empty($lasthunt['hunter_id'])) {
    $huntrider = $driver->getRider($lasthunt['hunter_id']);
    $user = $driver->getUserById($huntrider->getUserId());
    
    $location = $driver->getLocation($lasthunt['vossentracker_id']);
    $vos = $driver->getTeamById($location->getVossenId());
    echo "||||||".$vos->getName()." - ".$user->getDisplayName()." (".strftime("%a %H:%M",strtotime($lasthunt['time'])).")";
} else {
    echo "||||||Er zijn nog geen hunts, nog niet... :P";
}