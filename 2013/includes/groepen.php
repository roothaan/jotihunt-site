<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();
?>

var aMarkers = new Array();

<?php

$allPois = $driver->getAllPois();

foreach ( $allPois as $poi ) {
    if ($poi) { ?>
        var groepnaam = <?= json_encode($poi->getName()) ?>;
        var tekst = <?= json_encode($poi->getData()) ?>;
        aMarkers.push(
            createGroep(
                groepnaam + '<br />' + tekst,
                <?= $poi->getLongitude() ?>,
                <?= $poi->getLatitude() ?>,
                '<?= $poi->getType()?>'
            )
        );
    <?php
    }
}

?>

function createGroep(info, long, lat, type) {
    var aGroep = new Array();
    aGroep.push(new google.maps.LatLng(lat, long));
    aGroep.push(info);
    aGroep.push(type);
    return aGroep;
}