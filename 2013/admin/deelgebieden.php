<?php
require_once '../init.php';
$authMgr->requireSuperAdmin();
require_once BASE_DIR . 'header.php';
require_once BASE_DIR . 'includes/KmlHelper.class.php';

if (isset($_POST['deelgebied_id'])) {
    $driver->addCoordinate(new Coordinate(null, $_POST['deelgebied_id'], $_POST['longitude'], $_POST['latitude'], $_POST['order_id']));
}

if (isset($_POST['event_id']) && isset($_POST['name']) && isset($_POST['colour'])) {
    $driver->addDeelgebied(new Deelgebied(null, $_POST['event_id'], $_POST['name'], $_POST['linecolor'], $_POST['polycolor']));
}

if (isset($_GET['action']) && 'remove' == $_GET['action']) {
    $driver->removeDeelgebied($_GET['deelgebied_id']);
}

$kmlHelper = new KmlHelper();
$kmlHelper->parseDeelgebiedenKml();

$events = $driver->getAllEvents();

if (sizeof($events) === 0) {?>
    <h1>No Events</h1>
    <?php
}

foreach ($events as $event) {
    echo '<h1>Event: ' . $event->getId() . '</h1>';
    $allDeelgebieden = $driver->getAllDeelgebiedenForEvent($event->getId());
    if (sizeof($allDeelgebieden) === 0 ) {
        ?>
        <h2>Geen deelgebieden!</h2>
        <p>Maak een KML met 2 (!) lagen, :<strong>Deelgebieden</strong> en <strong>Groepen</strong>.</p>
        <form action="/2013/admin/deelgebieden.php" method="POST" enctype="multipart/form-data">
            KML File: <input type="file" name="kml_file"><br />
            import: <input type="checkbox" name="import"><br />
            <input type="hidden" name="event_id" value="<?= $event->getId() ?>"><br />
            <input type="submit" value="Upload KML file">
        </form>
        
        <?php
    } else {
        echo '<ul>';
		foreach ($allDeelgebieden as $deelgebied) {
		    echo '<li>' . $deelgebied->getName() . ' ['.$deelgebied->getId().'] <a href="/2013/admin/deelgebieden.php?action=remove&deelgebied_id='.$deelgebied->getId().'">verwijder</a></li>';
		    
		    $coordinate_counter = 0;
		    $allCoordinates = $driver->getAllCoordinatesForDeelgebied($deelgebied->getId());
            if (sizeof($allCoordinates) === 0 ) {
                echo 'Geen coordinaten!';
            } else {
    		    echo '<ul>';
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
            Voeg coordinate toe:
            <form action="/2013/admin/deelgebieden.php" method="POST">
                Longitude:  <input type="text" name="longitude" />
                Latitude:  <input type="text" name="latitude" />
                Order ID:  <input type="text" name="order_id" value="<?= ++$coordinate_counter ?>" />
                <input type="hidden" name="deelgebied_id" value="<?= $deelgebied->getId() ?>"/>
                <input type="submit" name="Voeg coordinate toe"/>
            </form>
            <?php
		}
		echo '</ul>'; ?>
		Voeg deelgebied toe:
            <form action="/2013/admin/deelgebieden.php" method="POST">
                Naam:  <input type="text" name="name" />
                <input type="hidden" name="event_id" value="<?= $event->getId() ?>"/>
                <input type="submit" name="Voeg deelgebied toe"/>
            </form>
    <?php 
    }
}
?>
<h1>KML import</h1>
<form action="/2013/admin/deelgebieden.php" method="POST" enctype="multipart/form-data">
    KML File: <input type="file" name="kml_file"><br />
    import: <input type="checkbox" name="import"><br />
    <input type="submit" value="Upload KML file">
</form>
<?php
require_once BASE_DIR . 'footer.php';
?>