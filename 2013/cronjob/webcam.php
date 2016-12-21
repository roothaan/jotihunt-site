<?php
require_once '../init.php';

$url = "http://192.168.90.113/dms";
$basicAuthHeaderValue = 'YWRtaW46c2FuZGVyaXNsaWVm';
// NIET MEER AANZITTEN HIERONDER, VRAAG EEN VOLWASSENE OM HULP

$requestHeaders =  array (
        'Authorization: Basic ' . $basicAuthHeaderValue 
);

$optsArr = array (
    'method' => 'GET',
    'header' => implode("\n", $requestHeaders)
);

$opts = array (
        'http' => $optsArr 
);
$context = stream_context_create($opts);

$use_include_path = false;
$imageData = file_get_contents($url, $use_include_path, $context);

//header("Content-Type: image/jpg");
$driver->updateWebcam(pg_escape_bytea($imageData));