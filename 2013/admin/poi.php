<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();
require_once BASE_DIR . 'includes/KmlHelper.class.php';

$kmlHelper = new KmlHelper();
$kmlHelper->parsePoiKml();

$pois = $driver->getAllPois();
if ($authMgr->isSuperAdmin()) {
    $pois = $driver->getAllPoisSu();
}
?>

<script type="text/javascript">
$(document).ready(function() {
$('#poi').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": false,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": true,
	"aoColumns": [
            {sName: "id" },
            {sName: "event_id" },
            {sName: "name"},
            {sName: "data" },
            {sName: "latitude" },
            {sName: "longitude" },
            {sName: "type" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?= BASE_URL . 'ajax/poi.ajax.php' ?>",
	    sDeleteURL: "<?= BASE_URL . 'ajax/poi.ajax.php' ?>"
	})
});
</script>

<h1>Pois (Point Of Interest)</h1>

<button id="btnDeleteRow">Verwijder poi</button>

<form action="<?= BASE_URL . 'ajax/poi.ajax.php' ?>" method="POST">
<table id="poi">
    <thead>
        <tr>
            <th>ID</th>
            <th>event ID</th>
            <th>name</th>
            <th>data</th>
            <th>latitude</th>
            <th>longitude</th>
            <th>type</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addPoi">
                <td></td>
				<td><select name="event_id">';
	            	<?php
						// Get all events for this user
						$events = $driver->getAllEvents();
					    foreach ($events as $event) {
					        echo '<option value="'.$event->getId().'">'.$event->getName().'</option>';
					    }
					?>
				</td>
                <td><input type="text" name="name" /></td>
                <td><input type="text" name="data" /></td>
                <td><input type="text" name="latitude" /></td>
                <td><input type="text" name="longitude" /></td>
                <td><input type="text" name="type" /></td>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($pois as $poi) { ?>
        <tr id="<?= $poi->getId() ?>">
            <td class="read_only"><?= $poi->getId() ?></td>
            <td><?= $poi->getEventId() ?></td>
            <td><?= $poi->getName() ?></td>
            <td><?= $poi->getData() ?></td>
            <td><?= $poi->getLatitude() ?></td>
            <td><?= $poi->getLongitude() ?></td>
            <td><?= $poi->getType() ?></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>

<h1>KML import</h1>
<form action="<?= WEBSITE_URL?>admin-poi" method="POST" enctype="multipart/form-data">
    Type: <input type="text" name="poi_type" value="group" /><br />
    Folder name: <input type="text" name="poi_folder_name" value="Groepen" /> (use this if there are multiple layers. Leave empty if you exported a single layer)<br />
    KML File: <input type="file" name="kml_file"><br />
    import: <input type="checkbox" name="import"><br />
    <input type="submit" value="Upload KML file">
</form>