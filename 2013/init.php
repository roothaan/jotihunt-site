<?php

define('BASE_DIR', dirname(__FILE__) . '/');

require_once BASE_DIR . 'includes/global_include.php';
require_once BASE_DIR . 'includes/functions.php';
require_once BASE_DIR . 'admin/paths.php';

$authMgr->attemptAuth();
if (! $driver->isReady()) {
    if (! defined('NO_DB_REQUIRED')) {
        echo 'not ready yet. Try <a href="' . WEBSITE_URL . 'admin">/admin</a>?';
        die();
    }
}

// Redirect to https if possible
if (defined('REDIRECT_TO_HTTPS') && REDIRECT_TO_HTTPS == true) {
    if(
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http')
        ||
        (isset($_SERVER['X-Forwarded-Proto']) && $_SERVER['X-Forwarded-Proto'] == 'http')
        ||
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off')
    )
    {
        $redirect = 'https:' . WEBSITE_URL;
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
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