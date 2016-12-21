<?php
require_once BASE_DIR . '../config.inc.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

$driver = DataStore::getSiteDriver();
$authMgr = new AuthMgr();

?>