<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

require_once CLASS_DIR . 'jotihunt/helpers/KmlGenerator.php';
require_once CLASS_DIR . 'jotihunt/helpers/DeelgebiedHelper.class.php';
global $authMgr;

$authMgr->requireAdmin();

// Get API
$jotihuntApi = new JotihuntInformatieRest();
$deelnemers = $jotihuntApi->getDeelnemers();

// Create KML
$klmHelper = new KmlGenerator();
$kmlOutput = $klmHelper->getHeader("Groepen");
$kmlOutput .= $klmHelper->addDescription("Deelnemers aan de Jotihunt");
foreach($deelnemers as $deelnemer) {
    $address = $deelnemer->getStreet() . ' ' . $deelnemer->getHousenumber() . ($deelnemer->getHousenumberAddition() ?? '');
    $address .= ' ' . $deelnemer->getPostcode() . ' ' . $deelnemer->getCity() . ' (' . $deelnemer->getAccomodation() . ')';

    $kmlOutput .= $klmHelper->addPlacemark(
        $deelnemer->getName(),
        $address,
        $deelnemer->getLat(),
        $deelnemer->getLong()
    );
}
$kmlOutput .= $klmHelper->getFooter();

// Export KML
$deelgebiedHelper = new DeelgebiedHelper();
$deelgebiedHelper->outputAsKml($kmlOutput, "groepen.kml");
