<?php
ob_start();
require_once 'admin/paths.php';
require_once 'init.php';

if(!isset($noHeaderFooter[$urlArray[0]])) {
    require_once BASE_DIR . 'header.php';    
} else { ?>
    <div class="noHeaderFooter">
    <?php
}

define('opoiLoaded',true);
?>

<div id="page">
    <div id="content">
        <?php
        if(isset($paths[$urlArray[0]])) {
            require_once $paths[$urlArray[0]];
        } else {
            header("HTTP/1.0 404 Not Found");
            echo '404 - Deze pagina is helaas niet gevonden.';
        } ?>
    </div>
</div>

<?php
if(!isset($noHeaderFooter[$urlArray[0]])) {
    require_once BASE_DIR . 'footer.php';
} else { ?>
    </div>
    <?php
}
ob_end_flush();