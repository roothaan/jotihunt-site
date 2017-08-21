<?php

/**
 *
 * @var string the root dir (cannot go up further)
 */
define('ROOT_DIR', dirname(__FILE__) . '/');

/**
 *
 * @var string usually /src/main/php/
 */
define('CLASS_DIR', ROOT_DIR . '/src/main/php/');

/**
 *
 * @var string usually /test/src/main/php/
 */
define('TEST_CLASS_DIR', ROOT_DIR . '/test/src/main/php/');

require_once ROOT_DIR . 'config/user.inc.php';

$port = $_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443 ? '' : ':' . $_SERVER['SERVER_PORT'];
// Overwrite port in case of environment setting
if (getenv('PROXY_SERVER_PORT')) {
    $port = ':' . getenv('PROXY_SERVER_PORT');
}
// Overwrite / extend port in case there is (also) a base URL set
if (getenv('PROXY_BASE_URL')) {
    $port = $port . '/' . getenv('PROXY_BASE_URL');
}
if (getenv('DEV_MODE')) {
    define('DEV_MODE', boolval(getenv('DEV_MODE')));
}

/**
 *
 * @var string Base URL, Ends in a /
 */
define('BASE_URL', '//' . $_SERVER ['SERVER_NAME'] . $port . '/2013/');
/**
 *
 * @var string Website URL, Ends in a /
 */
define('WEBSITE_URL', '//' . $_SERVER ['SERVER_NAME'] . $port .'/');
/**
 *
 * @var string Base Test URL, Ends in a /
 */
define('TEST_URL', '//' . $_SERVER ['SERVER_NAME'] . $port . '/test/');

require_once ROOT_DIR . 'config/dbconfig.inc.php';
require_once ROOT_DIR . 'config/google.inc.php';
require_once ROOT_DIR . 'config/mailgun.inc.php';
require_once ROOT_DIR . 'config/proximo.inc.php';
require_once ROOT_DIR . 'config/theme.inc.php';

// For time methods
date_default_timezone_set('Europe/Amsterdam');

// For floating point operations (like "52.3" (US) instead of "52,3" (Europe))
setlocale(LC_NUMERIC, 'en_US');
setlocale(LC_TIME, 'nl_NL');

// For error reporting
error_reporting(E_ALL);
ini_set('display_errors', getenv('SITE_SHOW_ERRORS') ? boolval(getenv('SITE_SHOW_ERRORS')) : 0);
ini_set('html_errors', getenv('SITE_SHOW_ERRORS') ? boolval(getenv('SITE_SHOW_ERRORS')) : 0);
?>