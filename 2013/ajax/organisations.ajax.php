<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'user/Organisation.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['name'])) {

    // Add org
    $organisation = new Organisation(null, $_POST ['name']);
    $newOrg = $driver->addOrganisation($organisation);

    if (!$newOrg) {
        echo 'Something went horribly wrong!';
        die();
    }

    header('Location:' . BASE_URL . 'admin/organisations.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $org = $driver->getOrganisationById($id);
    if ($_POST ['columnName'] == 'name') {
        $org->setName($newValue);
        $driver->updateOrganisation($org);
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
    $success = $driver->removeOrganisation($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here