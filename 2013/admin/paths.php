<?php
$paths = array(
    "" => "home.php",
    "home" => "home.php",
    "login" => "login.php",
    "logout" => "logout.php",
    "berichten" => "berichten.php",
    "vossen" => "vossen.php",
    "hunts" => "edithunts.php",
    "hunters" => "hunters.php",
    "opzieners" => "opzieners.php",
    "telefoonnummers" => "telnummers.php",
    "alarm" => "alarmsite.php",
    "beamer" => "fullscreen.php",
    "kaart" => "map.php",
    "admin" => "admin/admin.php",
    "delete_locatie" => "delvos.php",
    "huntgoedkeuren" => "huntgoedkeuren.php",
    "deletehunt" => "delhunt.php",
    "deleteopziener" => "delopziener.php",
    "deletetelefoonnummer" => "deltel.php",
    "bericht"=> "bericht_detail.php",
    "opdrachten"=> "opdrachten.php",
    "invoer"=>"invoer.php",
    "events" => "choose_event.php",
    'admin-gcm' => 'gcm.php',
    'privacy' => 'privacy.php',
    'deelgebieden-kml' => 'kml/deelgebieden.php'
);

$needsNoEvent = array(
    'login',
    'logout',
    'events'
);

$needsNoDb = array(
    'admin' => 1
);

$noHeaderFooter = array(
    "beamer" => 1,
    "kaart" => 1,
    "delete_locatie" => 1
);

$urlToParse = $_SERVER['REQUEST_URI'];
if (getenv('PROXY_BASE_URL')) {
    $pos = strpos($urlToParse, getenv('PROXY_BASE_URL'));
    if ($pos !== false) {
        $urlToParse = substr($urlToParse, $pos + strlen(getenv('PROXY_BASE_URL')));
    }
}

$urlParts = parse_url($urlToParse);

$urlArray = array();
if (isset($urlParts['path'])) {
    $urlArray = explode('/', trim($urlParts['path'], '/'));
}

if (count($urlArray) > 0 && in_array($urlArray[0], $needsNoEvent)) {
    define('NEEDS_NO_EVENT', true);
}

if(isset($needsNoDb[$urlArray[0]])) {
    define('NO_DB_REQUIRED', true);
}

unset($needsNoEvent);
unset($needsNoDb);