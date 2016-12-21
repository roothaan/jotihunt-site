<?php
require_once 'init.php';

$image = $driver->getImageById($_GET['imageId']);

$ctype = "";
switch($image->getExtension()) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    default:
}

header('Content-type: ' . $ctype);
header('Content-Length: ' . $image->getFileSize());

echo $image->getData();