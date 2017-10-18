<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/PoiType.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['name'])) {
    // Add POIType
    $poitype = new PoiType(null, 
                $_POST ['event_id'], 
                $authMgr->getMyOrganisationId(), 
                $_POST ['name'], 
                $_POST ['onmap'],
                $_POST ['onapp'],
                $_POST ['image']
                );
    $newPoiType = $driver->addPoiType($poitype);

    if (!$newPoiType) {
        echo 'Something went horribly wrong!';
        die();
    }
    
    header('Location:' . WEBSITE_URL . 'admin-poi-type');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $poitype = $driver->getPoiTypeById($id);
    if ($_POST ['columnName'] == 'event_id') {
        $poitype->setEventId($newValue);
        $driver->updatePoiType($poitype);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'name') {
        $poitype->setName($newValue);
        $driver->updatePoiType($poitype);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'onmap') {
        $poitype->setOnmap($newValue);
        $driver->updatePoiType($poitype);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'onapp') {
        $poitype->setOnapp($newValue);
        $driver->updatePoiType($poitype);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'image') {
        $poitype->setImage($newValue);
        $driver->updatePoiType($poitype);
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
    $success = $driver->removePoiType($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here