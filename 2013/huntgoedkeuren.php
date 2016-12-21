<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");
if (isset($_GET ["id"])) {
    $hunt = $driver->getHunt($_GET ["id"]);
    
    if($_GET ["goedkeuren"] == 2) {
        $hunt['goedgekeurd'] = 2;
    } elseif($_GET ["goedkeuren"] == 1) {
        $hunt['goedgekeurd'] = 1;
    } else {
        $hunt['goedgekeurd'] = 0;
    }
    
    $driver->updateHunt($hunt);
}

header("Location: ".WEBSITE_URL."hunts");
die();