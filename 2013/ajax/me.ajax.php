<?php
require_once '../init.php';
JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'user/User.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

// Update
if (isset($_POST ['displayname'])) {
    $displayName = $_POST ['displayname'];
    $errors = array();
    $user = $authMgr->getMe();

    if (strlen($displayName) > 0) {
        $user->setDisplayName($_POST ['displayname']);
    } else {
        $errors['displayname'] = 'Displayname cannot be empty';
    }

    $pw = $_POST ['new_password'];
    if (isset($pw) && strlen($pw) > 0) {
        $pwCorrect = checkPassword($pw, $errors);
        if ($pwCorrect) {
            $pw_hash = AuthMgr::getHash($pw);
            $user->setPwHash($pw_hash);
        }
    }
    $driver->updateUser($user);
    $qs = '?success';
    if (count($errors) > 0) {
        $qs = '?' . http_build_query($errors);
    }
    header('Location:' . WEBSITE_URL . 'me' . $qs);
    die();
}

function checkPassword($pwd, &$errors) {
    $errors_init = $errors;

    if (strlen($pwd) < 8) {
        $errors['new_password'][] = "Password too short (8 or more)!";
    }

    if (!preg_match("#[0-9]+#", $pwd)) {
        $errors['new_password'][] = "Password must include at least one number!";
    }

    if (!preg_match("#[a-zA-Z]+#", $pwd)) {
        $errors['new_password'][] = "Password must include at least one letter!";
    }     

    return ($errors == $errors_init);
}
?>
Not an update call