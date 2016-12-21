<?php
require_once '../init.php';
$authMgr->requireSuperAdmin();
require_once BASE_DIR . 'header.php';

$speelhelften = $driver->getAllSpeelhelften();
?>

<script type="text/javascript">
$(document).ready(function() {
$('#speelhelften').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": false,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": true,
	"aoColumns": [
            {sName: "id" },
            {sName: "event_id" },
            {sName: "starttime" },
            {sName: "endtime" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?php echo BASE_URL . 'ajax/speelhelften.ajax.php'; ?>",
	    sDeleteURL: "<?php echo BASE_URL . 'ajax/speelhelften.ajax.php'; ?>"
	})
});
</script>

<h1>Speelhelften</h1>

<button id="btnDeleteRow">Verwijder speelhelft</button>

<form action="<?php echo BASE_URL . 'ajax/speelhelften.ajax.php'; ?>" method="POST">
<table id="speelhelften">
    <thead>
        <tr>
            <th>ID</th>
            <th>event_id</th>
            <th>starttime</th>
            <th>endtime</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addSpeelhelft">
                <td></td>
                <td><select name="event_id">
                    <?php 
                    foreach ($driver->getAllEvents() as $event) {
                        echo '<option value="'.$event->getId().'">'.
                        $event->getName().'</option>';
                    } ?>
                </select></td>
                <td><input type="text" name="starttime" /></td>
                <td><input type="text" name="endtime" /></td>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($speelhelften as $speelhelft) { ?>
        <tr id="<?php echo $speelhelft->getId(); ?>">
            <td class="read_only"><?= $speelhelft->getId() ?></td>
            <td><?= $driver->getEventById($speelhelft->getEventId())->getName() ?> <code>(<?= $speelhelft->getEventId() ?>)</code></td>
            <td><?= $speelhelft->getStarttime() ?></td>
            <td><?= $speelhelft->getEndtime() ?></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>
<?php
require_once BASE_DIR . 'footer.php';
?>