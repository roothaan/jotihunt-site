<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

$authMgr->logout();
header('Location: '.WEBSITE_URL);
die();