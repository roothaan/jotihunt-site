<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();
?>

var aMarkers = new Array();

<?php

$allPois = $driver->getAllMapPois();

foreach ( $allPois as $poi ) {
    if ($poi) {
        $poiTypeName = $poi->getType();
        $poiType = $poi->getPoiType();
        $url = '';
        if ($poiType) {
            $url = $poiType->getImage();
        }
        ?>
        var groepnaam = <?= json_encode($poi->getName()) ?>;
        var tekst = <?= json_encode($poi->getData()) ?>;
        aMarkers.push(
            createGroep(
                groepnaam + '<br />' + tekst,
                <?= $poi->getLongitude() ?>,
                <?= $poi->getLatitude() ?>,
                '<?= $poiTypeName ?>',
                '<?= $url ?>'
            )
        );
    <?php
    }
}

?>

function createGroep(info, long, lat, type, url) {
    var aGroep = new Array();
    aGroep.push(new google.maps.LatLng(lat, long));
    aGroep.push(info);
    aGroep.push(type);
    aGroep.push(url);
    return aGroep;
}