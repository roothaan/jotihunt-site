<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");
$authMgr->requireAdmin();

if (isset($_GET ["id"])) {
    $id = intval($_GET ["id"]);
    $driver->removeOpziener($id);
}

header("Location: ".WEBSITE_URL."opzieners");
die();