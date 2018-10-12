<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

// Insert
if (isset($_POST ["submitrij"])) {
    $rider = new Rider();
    $rider->setUserId($_POST ["userId"]);
    $rider->setDeelgebied($_POST ["deelgebied"]);
    $rider->setTel($_POST ["tel"]);
    $rider->setVan(strtotime($_POST ['startdatum'] . ' ' . $_POST ['starttijd']));
    $rider->setTot(strtotime($_POST ['einddatum'] . ' ' . $_POST ['eindtijd']));
    $rider->setAuto(end(explode("/",$_POST ["auto"])));
    $driver->addRider($rider);
    header('Location:' .WEBSITE_URL. 'hunters');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $riderId = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    $rider = $driver->getRider($riderId);
    
    if ($_POST ['columnName'] == 'deelgebied') {
        $changed = true;
        $rider->setDeelgebied($newValue);
    }
    if ($_POST ['columnName'] == 'tel') {
        $changed = true;
        $rider->setTel($newValue);
    }
    if ($_POST ['columnName'] == 'van') {
        $changed = true;
        $rider->setVan(strtotime($newValue));
    }
    if ($_POST ['columnName'] == 'tot') {
        $changed = true;
        $rider->setTot(strtotime($newValue));
    }
    if ($_POST ['columnName'] == 'auto') {
        $changed = true;
        $rider->setAuto(strtotime($newValue));
    }
    
    if ($changed) {
        $driver->updateRider($rider);
    }
    echo $newValue;
    die();
}

// Delete
if (isset($_POST ['id'])) {
    $id = intval($_POST ['id']);
    $driver->removeRider($id);
    echo 'ok';
    die();
}
?>