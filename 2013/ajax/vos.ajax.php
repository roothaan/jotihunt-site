<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

if(isset($_POST["deelgebied"])) {
    $vosteam = $driver->getVosXYByDeelgebied($_POST["deelgebied"]);
    
    if (isset($_POST["status"])) {
        $authMgr->requireAdmin();
        
        /* GCM message */
        $allGcmIds = $driver->getAllActiveGcms();
        $payload = array (
                'status' => $_POST["status"],
                'teamName' => $vosteam->getName() 
        );
    
        /* Verstuurt status update naar Google Message Service */
        $gcmSender = new GcmSender();
        $gcmSender->setReceiverIds($allGcmIds);
        $gcmSender->setPayload($payload);
        $result = $gcmSender->send();
            
            
        $vosteam->setStatus($_POST["status"]);
        
        $driver->updateTeam($vosteam);
    }
}elseif (isset($_POST ["x"]) && isset($_POST ["y"])) {
    //hints via /invoer
    $msg = "Er ging iets mis";
	$type = 0;
	$deelgebiedId = $_POST ["team"];
    $savex = $_POST ["x"];
    $savey = $_POST ["y"];
    $latitude = $_POST ["f"];
    $longitude = $_POST ["l"];
    $adres = $_POST ["adres"];
    $time = strtotime($_POST ['startdatum'] . ' ' . $_POST ['starttijd']);
    
    // Fetch latest coordinates
	$lastx = "";
	$lasty = "";
	$team = $driver->getTeamByDeelgebiedId($deelgebiedId);

	$vos = $driver->getVosIncludingLocations($team->getName());
	$vos_locations = $vos->getLocations();
	if (count($vos_locations) > 0) {
		$vos_locatie = $vos_locations [0];
		$lastx = $vos_locatie->getX();
		$lasty = $vos_locatie->getY();
	}
    
    if (! ($savex > 10000 && $savex < 999999 && $savey > 10000 && $savey < 999999)) {
        $msg = "Ongeldige coördinaten.";
    } elseif($savex == $lastx && $savey == $lasty) {
    	$msg = "Reeds ingevoerde coördinaten.";
    } else {
        while ( $savex < 100000 ) {
            $savex = $savex * 10;
        }
        while ( $savey < 100000 ) {
            $savey = $savey * 10;
        }
    
    	$counterhuntRondjeId = $driver->getActiveCounterhuntRondje($team->getName())->getId();
    
        $newLocation = $driver->addVosLocation($team->getName(), $savex, $savey, $latitude, $longitude, $adres, $counterhuntRondjeId, $time, $type);
        
        // Send message to devices
        $vosX = $driver->getVosXYByDeelgebied($team->getName());
        $allGcmIds = $driver->getAllActiveGcms();
        $payload = array (
                'location' => $newLocation->toArray(),
                'teamName' => $vosX->getName() 
        );
        $gcmSender = new GcmSender();
        $gcmSender->setReceiverIds($allGcmIds);
        $gcmSender->setPayload($payload);
        $result = $gcmSender->send();
        // End send to GCM
        
        if (false === $result) {
            $msg = 'Error sending location(s) to GCM: ' . $result;
        } else {
            $msg = "succes";
        }
    }
    echo $msg;
}