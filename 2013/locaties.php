<?php
require_once 'init.php';
JotihuntUtils::requireLogin();
require_once BASE_DIR . 'header.php';

require_once CLASS_DIR . 'jotihunt/MapOptions.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';

echo "<h2>Laatste vossenlocaties</h2>";
$teams = array("A","B","C","D","E","F");
echo "<code style=\"font-size:20px;\">";
foreach ($teams as $team){
echo $team.": ";
// Fetch latest coordinates
$lastx = "";
$lasty = "";
$vos = $driver->getVosIncludingLocations($team);
$vos_locations = $vos->getLocations();
if (count($vos_locations) > 0) {
    $vos_locatie = $vos_locations [0];
    $lastx = substr($vos_locatie->getX(),0,5);
    $lasty = substr($vos_locatie->getY(),0,5);
    echo $lastx."-".$lasty.". (".$vos_locatie->getDate().")<br />";
}else{
	echo "onbekend<br />";
}
}
echo "</code>";

require_once BASE_DIR . 'footer.php';
?>