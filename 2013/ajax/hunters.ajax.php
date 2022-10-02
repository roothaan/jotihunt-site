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


if (isset($_POST ['copy_hunters_from_event'])) {
    $id = intval($_POST ['event_id']);
    $ridercollection = [];
    if ($id > 0) {
        // Get all riders from that event
        $ridercollection = $driver->getAllRiders($id);
        $current_riders = $driver->getAllRiders();
        $diff = array_udiff($ridercollection, $current_riders, function ($obj_a, $obj_b) {
            return intval($obj_a->getUserId()) - intval($obj_b->getUserId());
        });

        $event_id = $authMgr->getMyEventId();
        $event = $driver->getEventById($event_id);

        foreach ($diff as $rider) {
            // Reset deelgebied
            $rider->setDeelgebied($_POST ["deelgebied"]);
            // Reset start / end
            $rider->setVan(strtotime($event->getStarttime()));
            $rider->setTot(strtotime($event->getEndtime()));
            $driver->addRider($rider);
        }
    }
    echo json_encode(['id' => $id, 'success' => $id > 0, 'riders' => $ridercollection]);
    die();
}
