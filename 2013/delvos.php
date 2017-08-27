<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

// Handle ID
if (null != JotihuntUtils::getUrlPart(1)) {
    $id = intval(JotihuntUtils::getUrlPart(1));
    $driver->removeVossenLocation($id);
}

// Handle TEAM
$team = '';
if (null != JotihuntUtils::getUrlPart(2)) {
    $team = '/' . JotihuntUtils::getUrlPart(1);
}

header('Location: ' . WEBSITE_URL . 'vossen' . $team);
die();