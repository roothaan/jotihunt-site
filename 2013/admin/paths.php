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
    'privacy' => 'privacy.php',
    'deelgebieden-kml' => 'kml/deelgebieden.php',
    'scores' => 'scores.php',
    'kml' => 'kml.php',
    'admin-hunter-map' => 'hunter_map.php',
    'admin-users' => 'admin/users.php',
    'admin-createusers' => 'admin/createusers.php',
    'admin-poi' => 'admin/poi.php',
    'admin-gcm' => 'gcm.php',
    'suadmin-config' => 'admin/config.php',
    'suadmin-kml-import' => 'admin/kml-import.php',
    'suadmin-events' => 'admin/events.php',
    'suadmin-organisations' => 'admin/organisations.php',
    'suadmin-deelgebieden' => 'admin/deelgebieden.php',
    'suadmin-speelhelften' => 'admin/speelhelften.php',
    'suadmin-counterhunt' => 'admin/counterhunt.php',
    'suadmin-vossen' => 'admin/vossen.php',
    'suadmin-showdatabase' => 'admin/showdatabase.php',
    'suadmin-phpinfo' => 'admin/phpinfo.php'

);

$needsNoEvent = array(
    'login',
    'logout',
    'privacy',
    'events'
);

$needsNoDb = array(
    'admin' => 1,
    'privacy' => 1
);

// 1 == <div class="noHeaderFooter"><div id="page"><div id="content">
// 2 == nothing
$noHeaderFooter = array(
    "beamer" => 2,
    "kaart" => 2,
    // TODO => This should probably be 2. Also, terrible name??
    "delete_locatie" => 1,
    'kml' => 2,
    'deelgebieden-kml' => 2
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