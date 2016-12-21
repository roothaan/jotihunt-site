<?php
define('BASE_DIR', dirname(__FILE__) . '/');

require_once BASE_DIR . 'includes/global_include.php';
require_once BASE_DIR . 'includes/functions.php';

$authMgr->attemptAuth();
if (! $driver->isReady()) {
    if (! defined('NO_DB_REQUIRED')) {
        echo 'not ready yet. Try <a href="' . WEBSITE_URL . 'admin">/admin</a>?';
        die();
    }
}

// Figure out if they need to go to the "choose_event" page
if ($authMgr->isLoggedIn() && !$authMgr->isSuperAdmin() 
&& !$authMgr->getMyEventId()) {
    // Are we on a page that doesn't need it?
    if (!(defined('NEEDS_NO_EVENT') && NEEDS_NO_EVENT === true)) {
        header('Location: '.WEBSITE_URL.'events?redirect=1');
        die();
    }
}