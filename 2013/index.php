<?php
ob_start();
require_once 'init.php';

if(JotihuntUtils::hasHeaderOrFooter()) {
    require_once BASE_DIR . 'header.php';
    echo '<div id="page"><div id="content">';
}

define('opoiLoaded',true);

if(defined('JH_PATH')) {
    require_once JH_PATH;
} else {
    header("HTTP/1.0 404 Not Found");
    echo '404 - Deze pagina is helaas niet gevonden.';
}

if(JotihuntUtils::hasHeaderOrFooter()) {
    echo '</div></div>';
    require_once BASE_DIR . 'footer.php';
}
ob_end_flush();