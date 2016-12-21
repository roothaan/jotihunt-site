<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

// Insert
if (isset($_POST ["gcmid"])) {
    $gcm = new Gcm();
    $gcm->setGcmId($_POST ["gcmid"]);
    $gcm->setRiderId($_POST ["riderid"]);
    $gcm->setTime(time());
    
    $newGcm = $driver->addGcm($gcm);
    
    echo $newGcm->getId();
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    $gcm = $driver->getGcm($id);
    
    if ($_POST ['columnName'] == 'enabled') {
        $gcm->setEnabled($newValue === '1');
        $changed = true;
    }
    if ($_POST ['columnName'] == 'riderid') {
        $changed = true;
        $gcm->setRiderId($newValue);
    }
    
    if ($changed) {
        $driver->updateGcm($gcm);
    }
    echo $newValue;
    die();
}

// Delete
if (isset($_POST ['id'])) {
    $id = intval($_POST ['id']);
    $driver->removeGcm($id);
    echo 'ok';
    die();
}
?>