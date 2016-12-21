<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

$authMgr->logout();
header('Location: '.WEBSITE_URL);
die();