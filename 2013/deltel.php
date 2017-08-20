<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

$authMgr->requireAdmin();

if (isset($_GET ["id"])) {
    $id = intval($_GET ["id"]);
    $driver->removePhonenumber($id);
}

header("Location: ".WEBSITE_URL."telefoonnummers");
die();