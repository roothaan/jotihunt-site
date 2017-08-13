<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/Counterhuntrondje.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['deelgebied_id'])) {

    // Add Counterhuntrondje
    $counterhuntRondje = new Counterhuntrondje(
        null, 
        $_POST ['deelgebied_id'], 
        $_POST ['organisation_id'], 
        $_POST ['name'],
        $_POST ['active']
    );
    $newCounterhuntrondje = $driver->addCounterhuntrondje($counterhuntRondje);

    if (!$newCounterhuntrondje) {
        echo 'Something went horribly wrong!';
        die();
    }

    header('Location:' . BASE_URL . 'admin/counterhunt.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $counterhuntRondje = $driver->getCounterhuntrondjeByIdSu($id);
    if ($_POST ['columnName'] == 'name') {
        $counterhuntRondje->setName($newValue);
        $driver->updateCounterhuntrondje($counterhuntRondje);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'active') {
        $counterhuntRondje->setActive($newValue);
        $driver->updateCounterhuntrondje($counterhuntRondje);
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
    $success = $driver->removeCounterhuntrondje($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here