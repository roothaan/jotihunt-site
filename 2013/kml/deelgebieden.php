<?php
// Generate the KML file needed for this event
// Required Parameter: Event ID

if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
//JotihuntUtils::requireLogin();

require_once CLASS_DIR . 'jotihunt/helpers/DeelgebiedHelper.class.php';

if(!empty(JotihuntUtils::getUrlPart(1))) {
    $eventId = intval(JotihuntUtils::getUrlPart(1));
} else {
    echo 'No event_id';
    die();
}

$deelgebiedHelper = new DeelgebiedHelper();
$kmlOutput = $deelgebiedHelper->getKmlForEventId($eventId);

if (!$kmlOutput) {
    header('HTTP/1.1 404 Not Found');
    echo 'Not a valid event_id ['.$eventId.']';
    die();
}

$deelgebiedHelper->outputAsKml($kmlOutput);