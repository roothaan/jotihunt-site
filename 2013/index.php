<?php
ob_start();
require_once 'admin/paths.php';
require_once 'init.php';

if(!isset($noHeaderFooter[$urlArray[0]])) {
    require_once BASE_DIR . 'header.php';
    echo '<div id="page"><div id="content">';
} else if ($noHeaderFooter[$urlArray[0]] == 1) {
    echo '<div class="noHeaderFooter"><div id="page"><div id="content">';
}

define('opoiLoaded',true);

if(isset($paths[$urlArray[0]])) {
    require_once $paths[$urlArray[0]];
} else {
    header("HTTP/1.0 404 Not Found");
    echo '404 - Deze pagina is helaas niet gevonden.';
}

if(!isset($noHeaderFooter[$urlArray[0]])) {
    echo '</div></div>';
    require_once BASE_DIR . 'footer.php';
} else if ($noHeaderFooter[$urlArray[0]] == 1) {
    echo '</div></div></div>';
}
ob_end_flush();