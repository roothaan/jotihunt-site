<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/Event.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['name'])) {
    // Add event
    $event = new Event(null, $_POST ['name'], $_POST ['public'], $_POST ['starttime'], $_POST ['endtime']);
    $newEvent = $driver->addEvent($event);

    if (!$newEvent) {
        echo 'Something went horribly wrong!';
        die();
    }
    
    header('Location:' . WEBSITE_URL . 'suadmin-events');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $event = $driver->getEventById($id);
    if ($_POST ['columnName'] == 'name') {
        $event->setName($newValue);
        $driver->updateEvent($event);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'public') {
        $event->setPublic($newValue);
        $driver->updateEvent($event);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'starttime') {
        $event->setStarttime($newValue);
        $driver->updateEvent($event);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'endtime') {
        $event->setEndtime($newValue);
        $driver->updateEvent($event);
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
    $success = $driver->removeEvent($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here