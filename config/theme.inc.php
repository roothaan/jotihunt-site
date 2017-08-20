<?php
/**
 * Used for Skinning the site
 */
 if (getenv('THEME_NAME') && getenv('THEME_LOGOS') ) {
    define('THEME_NAME', getenv('THEME_NAME'));
    define('THEME_LOGOS', intval(getenv('THEME_LOGOS')));
} else {
    define('THEME_NAME', 'default');
    define('THEME_LOGOS', 1);
}
define('THEME_LOGO_URL', BASE_URL . 'images/themes/' . THEME_NAME . '/logo-' . rand(1, THEME_LOGOS) . '.png')
?>