<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireAdmin();

$pois = $driver->getAllPoiTypes();
if ($authMgr->isSuperAdmin()) {
    $pois = $driver->getAllPoisTypeSu();
}
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

<button id="btnDeleteRow">Verwijder poi-type</button>

<form action="<?= BASE_URL . 'ajax/poitypes.ajax.php' ?>" method="POST">
<table id="poi">
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
                <td><input type="text" name="event_id" /></td>
                <td><input type="text" name="name" /></td>
                <td><input type="text" name="onmap" /></td>
                <td><input type="text" name="onapp" /></td>
                <td><input type="text" name="image" /></td>
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
            <td><?= $poi->getOnMap() ?></td>
            <td><?= $poi->getOnApp() ?></td>
            <td><?= $poi->getImage() ?></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>