<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

require_once BASE_DIR . 'includes/KmlHelper.class.php';

if (isset($_POST['deelgebied_id'])) {
    $driver->addCoordinate(new Coordinate(null, $_POST['deelgebied_id'], $_POST['longitude'], $_POST['latitude'], $_POST['order_id']));
}

if (isset($_POST['event_id']) && isset($_POST['name']) && isset($_POST['colour'])) {
    $driver->addDeelgebied(new Deelgebied(null, $_POST['event_id'], $_POST['name'], $_POST['linecolor'], $_POST['polycolor']));
}

if (isset($_GET['action']) && 'remove' == $_GET['action']) {
    // Verwijder counterhunt rondjes
    $deelgebied = $driver->getDeelgebiedById($_GET['deelgebied_id']);
    $counterhuntrondjes = $driver->getCounterhuntrondjeForDeelgebied($deelgebied->getName());
    foreach ($counterhuntrondjes as $counterhuntrondje) {
        $driver->removeCounterhuntrondje($counterhuntrondje->getId());
        }
    // Verwijder vos
    $vos = $driver->getVosXYByDeelgebied($deelgebied->getName());
    if ($vos) {
        $driver->removeTeam( $vos->getId() );
    }
    $driver->removeDeelgebied($_GET['deelgebied_id']);
}

$kmlHelper = new KmlHelper();
$kmlHelper->parseDeelgebiedenKml();

$events = $driver->getAllEvents();

if (sizeof($events) === 0) {?>
    <h1>No Events</h1>
    <?php
}else {
    echo '<h1>Evenementen</h1>';
}
foreach ($events as $event) {
    echo '<p><h2>' .  $event->getName() . ' [' . $event->getId() . ']</h2><hr /></p>';
    $allDeelgebieden = $driver->getAllDeelgebiedenForEvent($event->getId());
    if (sizeof($allDeelgebieden) === 0 ) {
        ?>
        <h3>Geen deelgebieden!</h3>
        <p>Maak een KML met 2 (!) lagen, :<strong>Deelgebieden</strong> en <strong>Groepen</strong>.</p>
        <form action="<?=WEBSITE_URL?>suadmin-deelgebieden" method="POST" enctype="multipart/form-data">
            KML File: <input type="file" name="kml_file"><br />
            import: <input type="checkbox" name="import"><br />
            <input type="hidden" name="event_id" value="<?= $event->getId() ?>"><br />
            <input type="submit" value="Upload KML file">
        </form>
        
        <?php
    } else {
		foreach ($allDeelgebieden as $deelgebied) {
		    echo '<p><h3>' . $deelgebied->getName() . ' ['.$deelgebied->getId().'] <a href="'.WEBSITE_URL.'suadmin-deelgebieden?action=remove&deelgebied_id='.$deelgebied->getId().'">verwijder</a></h3>';
		    
		    $coordinate_counter = 0;
		    $allCoordinates = $driver->getAllCoordinatesForDeelgebied($deelgebied->getId());
            if (sizeof($allCoordinates) === 0 ) {
                echo '<p>Geen coordinaten voor dit deelgebied gevonden!</p>';
            } else {
                echo "<p><strong>" . count($allCoordinates) . "</strong> coordinaten gevonden. ";
                echo "<a onclick=\"$( '.coords_" . $deelgebied->getId() . "').toggle();return false;\" href=\"#\">Show/hide coordinates</a> </p>";
                echo "<ul class=\"coords_" . $deelgebied->getId() . "\" style=\"display:none;\">";
    		    foreach ($allCoordinates as $coordinate) {
    		        echo '<li>' . 
    		            'ID: ' . $coordinate->getId() . 
    		            ', Deelgebied ID: ' . $coordinate->getDeelgebiedId() . 
    		            ', LngLat: ' . $coordinate->getLongitude() . 
    		            ' / ' . $coordinate->getLatitude() . 
    		            ', Order ID: ' . $coordinate->getOrderId() . 
    		            '</li>';
    		        
    		        $coordinate_counter = $coordinate->getOrderId();
    		    }
    		    echo '</ul>';
            }
            ?>
            Voeg coordinate toe aan <strong><?= $deelgebied->getName() ?></strong>:
            <form action="<?=WEBSITE_URL?>suadmin-deelgebieden" method="POST">
                Longitude:  <input type="text" name="longitude" />
                Latitude:  <input type="text" name="latitude" />
                Order ID:  <input type="text" name="order_id" value="<?= ++$coordinate_counter ?>" />
                <input type="hidden" name="deelgebied_id" value="<?= $deelgebied->getId() ?>"/>
                <input type="submit" name="Voeg coordinate toe"/>
            </form></p>
            <?php
		} ?>
		Voeg deelgebied toe:
            <form action="<?=WEBSITE_URL?>suadmin-deelgebieden" method="POST">
                Naam:  <input type="text" name="name" />
                <input type="hidden" name="event_id" value="<?= $event->getId() ?>"/>
                <input type="submit" name="Voeg deelgebied toe"/>
            </form>
    <?php 
    }
}
?>
<hr />
<h2>KML import</h2>
<form action="<?=WEBSITE_URL?>suadmin-deelgebieden" method="POST" enctype="multipart/form-data">
    KML File: <input type="file" name="kml_file"><br />
    <label>import: <input type="checkbox" name="import"></label><br />
    <input type="submit" value="Upload KML file">
</form>
