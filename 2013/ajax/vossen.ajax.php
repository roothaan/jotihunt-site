<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['deelgebied_id'])) {

    // Add team
    $team = new VossenTeam();
    $team->setDeelgebied($_POST['deelgebied_id']);
    $team->setName($_POST['name']);
    $team->setStatus($_POST['status']);
    $team->setSpeelhelftId($_POST['speelhelft_id']);
    
    $newTeam = $driver->addTeam($team);

    if (!$newTeam) {
        echo 'Something went horribly wrong!';
        die();
    }

    header('Location:' . BASE_URL . 'admin/vossen.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $team = $driver->getTeamById($id);
    if ($_POST ['columnName'] == 'deelgebied') {
        $team->setDeelgebied($newValue);
        $driver->updateTeam($team);
        $changed = true;
    }
    
    if ($_POST ['columnName'] == 'speelhelft_id') {
        $team->setSpeelhelftId($newValue);
        $driver->updateTeam($team);
        $changed = true;
    }
    
    if ($_POST ['columnName'] == 'name') {
        $team->setName($newValue);
        $driver->updateTeam($team);
        $changed = true;
    }
    
    if ($_POST ['columnName'] == 'status') {
        $team->setStatus($newValue);
        $driver->updateTeam($team);
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
    $success = $driver->removeTeam($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here