<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

$poitypes = $driver->getAllPoiTypes();
if ($authMgr->isSuperAdmin()) {
    $poitypes = $driver->getAllPoisTypeSu();
}

$pois = $driver->getAllPois();
if ($authMgr->isSuperAdmin()) {
    $pois = $driver->getAllPoisSu();
}
// Get the types available..
$allTypes = array();
foreach ($pois as $poi) {
    $allTypes[] = $poi->getType();
}
$allTypes = array_unique($allTypes);

?>

<script type="text/javascript">
$(document).ready(function() {
$('#poitypes').dataTable( {
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
            {sName: "onmap" },
            {sName: "onapp" },
            {sName: "image" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?= BASE_URL . 'ajax/poitypes.ajax.php' ?>",
	    sDeleteURL: "<?= BASE_URL . 'ajax/poitypes.ajax.php' ?>"
	})
});
</script>

<h1>Pois (Point Of Interest) Types</h1>

<div><strong>NOTE: De app respecteert deze instellingen (nog?) niet.</strong><br/>In de app kunnen gebruikers individueel de categorieen aan en uit zetten.</div>
<br /><br />
<button id="btnDeleteRow">Verwijder poi-type</button>

<form action="<?= BASE_URL . 'ajax/poitypes.ajax.php' ?>" method="POST">
<table id="poitypes">
    <thead>
        <tr>
            <th>ID</th>
            <th>event ID</th>
            <th>name</th>
            <th>on Map</th>
            <th>on App</th>
            <th>image URL</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addPoi">
                <td></td>
                <td>
                    
                    <?php if ($authMgr->isSuperAdmin()) { ?>
                    <select name="event_id">';
	            	<?php
						// Get all events for this user
						$events = $driver->getAllEvents();
					    foreach ($events as $event) {
					        $selected = '';
					        if ($authMgr->getMyEventId() == $event->getId()) {
					            $selected = 'selected="selected"';
					        }
					        echo '<option value="'.$event->getId().'" '.$selected.'>'.$event->getName().'</option>';
					    }
                    }
					?>
					
				</select></td>
                <td><select name="name">
                    <?php
                    foreach ($allTypes as $type) {
					        echo '<option value="'.$type.'">'.$type.'</option>';
					    }
					?>
					<option value="homebase">homebase</option>
                </select></td>
                <td><select name="onmap">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                    </select></td>
                <td><select name="onapp">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                    </select></td>
                <td><input type="text" name="image" /></td>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($poitypes as $poi) { ?>
        <tr id="<?= $poi->getId() ?>">
            <td class="read_only"><?= $poi->getId() ?></td>
            <td <?php if(!$authMgr->isSuperAdmin()) { ?>class="read_only"<?php } ?>><?= $poi->getEventId() ?></td>
            <td class="read_only"><?= $poi->getName() ?></td>
            <td><?= $poi->getOnMap() ?></td>
            <td><?= $poi->getOnApp() ?></td>
            <td><?= $poi->getImage() ?></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>

<div>
    <h2>Icon library (for reference)</h2>
    <a href="https://sites.google.com/site/gmapsdevelopment/">https://sites.google.com/site/gmapsdevelopment/</a>
</div>
<div>
    <h2>Icons</h2>
    <img src="https://maps.google.com/mapfiles/ms/micons/snack_bar.png" height="20" width="20"> https://maps.google.com/mapfiles/ms/micons/snack_bar.png<br />
    <img src="https://maps.google.com/mapfiles/ms/icons/ferry.png" height="20" width="20"> https://maps.google.com/mapfiles/ms/icons/ferry.png<br />

    <img src="/2013/images/maps-scoutinggroep.png" height="20" width="20"> /2013/images/maps-scoutinggroep.png<br />
    <img src="/2013/images/maps-scoutinggroep-home.png" height="20" width="20"> /2013/images/maps-scoutinggroep-home.png
</div>