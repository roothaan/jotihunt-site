<?php
require_once 'init.php';
JotihuntUtils::requireLogin();

function drawVossenByTeam($deelgebiedName) {
    global $driver;
    
    $vossen_array = array ();
    $vos_locations = array();
    $old_coord = '';

    $vos = $driver->getVosIncludingLocations($deelgebiedName);
    
    if ($vos) {
        $vos_locations = $vos->getLocations();
    }
    
    if (! empty($vos_locations)) {
        $i = 0;
        foreach ( $vos_locations as $location ) {
            $vos_array = array ();
            
            switch ($location->getType()) {
                case 2 :
                    $vos_array ['type'] = "hunt";
                break;
                case 3 :
                    $vos_array ['type'] = "spot";
                break;
                default :
                    $vos_array ['type'] = "hint";
                break;
            }
            
            if ($i === 0) {
                $vos_array ['last_in_line'] = 'true';
            } else {
                $vos_array ['last_in_line'] = 'false';
            }
            
            $vos_array ['new_coord'] = JotihuntUtils::convert($location->getX(), $location->getY());
            $vos_array ['old_coord'] = $old_coord;
            $vos_array ['naam'] = $vos->getName();
            $vos_array ['adres'] = $location->getAddress();
            $vos_array ['formatted_datetime'] = strftime('%a, %d %b %R', strtotime($location->getDate()));
            $vos_array ['active_counterhuntrondje_id'] = $driver->getActiveCounterhuntRondje($deelgebiedName)->getId();
            $vos_array ['locatie_counterhuntrondje_id'] = $location->getCounterhuntrondjeId();
            
            $vossen_array [] = $vos_array;
            
            $old_coord = $vos_array ['new_coord'];
            $i ++;
        }
        
        return $vossen_array;
    }
    return false;
}

$voslocaties_array = array ();
if (isset($_POST ['team']) && ! empty($_POST ['team'])) {
    $voslocaties_array [$_POST ['team']] = drawVossenByTeam($_POST ['team']);
} else {
    $allDeelgebieden = $driver->getAllDeelgebieden();
	foreach($allDeelgebieden as $deelgebied) {
        $voslocaties_array [$deelgebied->getName()] = drawVossenByTeam($deelgebied->getName());
	}
}

echo json_encode($voslocaties_array);