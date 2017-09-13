<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

function vossenUpdateCounterhuntrondje($vos, $deelgebiedName) {
    global $driver;
    
    if (!$vos) {
        return;
    }
    if(isset($_GET['counterhuntrondjeId']) && !empty($_GET['counterhuntrondjeId']) && is_numeric($_GET['counterhuntrondjeId']) && $_GET['counterhuntrondjeId'] > 0) {
	    $driver->setActiveCounterhuntrondjeId($deelgebiedName, $_GET['counterhuntrondjeId']);
    }
}

/**
 * Should return:
 * $errror
 */
function vossenAddVos($currentVos, $deelgebiedName) {
    global $driver;

    if (!(isset($_POST ["x"]) && isset($_POST ["y"]))) {
        return;
    }
    $lastx = '';
	$lasty = '';

	$type = 0;
	if(isset($_POST["submithint"])) {
		$type = 0;
	} else if(isset($_POST["submitspot"])) {
		$type = 3;
	} else if(isset($_POST["submithunt"])) {
		$type = 2;
	}
    $savex = $_POST ["x"];
    $savey = $_POST ["y"];
    $latitude = $_POST ["f"];
    $longitude = $_POST ["l"];
    $adres = $_POST ["adres"];
    $time = strtotime($_POST ['startdatum'] . ' ' . $_POST ['starttijd']);

    // Fetch latest coordinates
	$vos_locations = $currentVos->getLocations();
	if (count($vos_locations) > 0) {
		$vos_locatie = $vos_locations [0];
		$lastx = $vos_locatie->getX();
		$lasty = $vos_locatie->getY();
	}

    if (! ($savex > 10000 && $savex < 999999 && $savey > 10000 && $savey < 999999)) {
        return "<small><font color=\"red\">De coordinaten die je hebt ingevoerd zijn ongeldig!</font></small>";
    } elseif($savex == $lastx && $savey == $lasty) {
    	return "<small><font color=\"red\">Dit coördinaat is al toegevoegd!</font></small>";
    } else {
        while ( $savex < 100000 ) {
            $savex = $savex * 10;
        }
        while ( $savey < 100000 ) {
            $savey = $savey * 10;
        }

		$counterhuntrondje = $driver->getActiveCounterhuntRondje($deelgebiedName);
		$counterhuntrondjeId = $counterhuntrondje ? $counterhuntrondje->getId() : 0;
    
        $newLocation = $driver->addVosLocation($deelgebiedName, $savex, $savey, $latitude, $longitude, $adres, $counterhuntrondjeId, $time, $type);
        if ($newLocation) {
	        if($type == 2 && isset($_POST['rider']) && !empty($_POST['rider']) && isset($_POST['code']) && !empty($_POST['code'])) {
	        	$driver->addHunt($_POST['rider'], $newLocation->getId(), $_POST['code']);
	        }
	        
            vossenSendGcm($deelgebiedName, $newLocation);
        }
    }
}

/**
 *  Send new location to GCM
 */
function vossenSendGcm($deelgebiedName, $newLocation) {
    global $driver;
    $vosX = $driver->getVosXYByDeelgebied($deelgebiedName);
    $allGcmIds = $driver->getAllActiveGcms();
    $payload = array (
        'location' => $newLocation->toArray(),
        'teamName' => $vosX->getName() 
    );
    $gcmSender = new GcmSender();
    $gcmSender->setReceiverIds($allGcmIds);
    $gcmSender->setPayload($payload);
    $result = $gcmSender->send();

    if (false === $result) {
        echo '<div>Error sending location(s) to GCM: ' . $result . '</div>';
    } else {
        echo '<div><em>Locatie succesvol naar Google verstuurd. <a href="javascript:$(\'#gcmDetails\').toggle();">Details</a></em></div><div id="gcmDetails" style="display:none;">' . $result . '</div>';
    }
}