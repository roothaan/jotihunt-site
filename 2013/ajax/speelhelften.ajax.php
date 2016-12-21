<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/Speelhelft.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['event_id'])) {

    // Add speelhelft
    $speelhelft = new Speelhelft(null, $_POST ['event_id'], $_POST ['starttime'], $_POST ['endtime']);
    $newSpeelhelft = $driver->addSpeelhelft($speelhelft);

    if (!$newSpeelhelft) {
        echo 'Something went horribly wrong!';
        die();
    }

    header('Location:' . BASE_URL . 'admin/speelhelften.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $speelhelft = $driver->getSpeelhelftById($id);
    if ($_POST ['columnName'] == 'event_id') {
        $speelhelft->setEventId($newValue);
        $driver->updateSpeelhelft($speelhelft);
        $changed = true;
    }
    
    if ($_POST ['columnName'] == 'starttime') {
        $speelhelft->setStarttime($newValue);
        $driver->updateSpeelhelft($speelhelft);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'endtime') {
        $speelhelft->setEndtime($newValue);
        $driver->updateSpeelhelft($speelhelft);
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
    $success = $driver->removeSpeelhelft($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here