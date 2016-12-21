<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

// Insert
if (isset($_POST ["submitopziener"])) {
    $userId = $_POST ["userId"];
    $deelgebiedId = $_POST ["deelgebied_id"];
    $type = $_POST ["type"];
    $driver->addOpziener($userId, $deelgebiedId, $type);
    
    header('Location:' .WEBSITE_URL. 'opzieners');
    die();
}