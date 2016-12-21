<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/Poi.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['name'])) {
    // Add POI
    $poi = new Poi(null, 
                $_POST ['event_id'], 
                $_POST ['name'], 
                $_POST ['data'],
                $_POST ['latitude'],
                $_POST ['longitude'],
                $_POST ['type']
                );
    $newPoi = $driver->addPoi($poi);

    if (!$newPoi) {
        echo 'Something went horribly wrong!';
        die();
    }
    
    header('Location:' . BASE_URL . 'admin/poi.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $poi = $driver->getPoiById($id);
    if ($_POST ['columnName'] == 'event_id') {
        $poi->setEventId($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'name') {
        $poi->setName($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'data') {
        $poi->setData($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'latitude') {
        $poi->setLatitude($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'longitude') {
        $poi->setLatitude($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'type') {
        $poi->setType($newValue);
        $driver->updatePoi($poi);
        $changed = true;
    }

    if ($changed) {
        echo $newValue;
    } else {
        echo 'Nothing updated!';
    }
    die();
}

// Delete
if (isset($_POST ['id'])) {
    $id = intval($_POST ['id']);
    $success = $driver->removePoi($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here