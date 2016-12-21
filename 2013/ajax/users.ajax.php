<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'user/User.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Insert
if (isset($_POST ['username'])) {
    // The provided 'pw_hash' in $_POST isn't really a hash yet, converting it here.
    $pw_hash = AuthMgr::getHash($_POST ['pw_hash']);
    
    // Add user
    $user = new User(null, $_POST ['username'], $_POST ['displayname'], $pw_hash);
    $newUser = $driver->addUser($user);

    if (!$newUser) {
        echo 'Something went horribly wrong!';
        die();
    }
    
    // Tie to user group
    foreach ($_POST['groups'] as $groupId) {
        $success = $driver->addUserToGroup($newUser->getId(), $groupId);
        error_log('tied user ' . $newUser->getId() . ' to group ' . $groupId . ', success: ' . $success);
    }
    
    // Tie to organisation (if needed)
    if (intval($_POST['organisation']) > 0 ) {
        $success = $driver->addUserToOrganisation($newUser->getId(), $_POST['organisation']);
        error_log('tied user ' . $newUser->getId() . ' to org ' . $_POST['organisation'] . ', success: ' . $success);
    } else {
        error_log('Not a organisation ID (or 0 for admins): ' . $_POST['organisation']);
    }
    //echo $newUser->getId();
    header('Location:' . BASE_URL . 'admin/users.php');
    die();
}

// Update
if (isset($_POST ['columnName'])) {
    $id = $_POST ['id'];
    $newValue = $_POST ['value'];
    $changed = false;
    
    $user = $driver->getUserById($id);
    if ($_POST ['columnName'] == 'displayname') {
        $user->setDisplayName($newValue);
        $driver->updateUser($user);
        $changed = true;
    }

    if ($_POST ['columnName'] == 'pw_hash') {
        $pw_hash = AuthMgr::getHash($newValue);
        $user->setPwhash($pw_hash);
        $driver->updateUser($user);
        $changed = true;
        $newValue = "Password successfully changed!";
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
    $success = $driver->removeUser($id);
    if ($success) {
        echo 'ok';
    } else {
        echo 'Something went wrong!';
    }
    die();
}
?>
You shouldn't be here