<?php
if(!defined('opoiLoaded')) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

if (null != JotihuntUtils::getUrlPart(1)) {
    $id = intval(JotihuntUtils::getUrlPart(1));
    $driver->removeOpziener($id);
}

header('Location: ' . WEBSITE_URL . 'opzieners');
die();