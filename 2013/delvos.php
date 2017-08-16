<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

// Handle ID
if (isset($urlArray[1])) {
    $id = intval($urlArray[1]);
    $driver->removeVossenLocation($id);
}

// Handle TEAM
$team = '';
if (isset($urlArray[2])) {
    $team = '/'.$urlArray[2];
}

header("Location: ".WEBSITE_URL."vossen".$team);
die();