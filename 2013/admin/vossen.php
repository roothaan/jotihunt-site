<?php
require_once '../init.php';
$authMgr->requireSuperAdmin();
require_once BASE_DIR . 'header.php';

$teams = $driver->getAllTeams();
?>

<script type="text/javascript">
$(document).ready(function() {
$('#vossen').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": false,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": true,
	"aoColumns": [
            {sName: "id" },
            {sName: "deelgebied" },
            {sName: "name" },
            {sName: "status" },
            {sName: "speelhelft_id" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?php echo BASE_URL . 'ajax/vossen.ajax.php'; ?>",
	    sDeleteURL: "<?php echo BASE_URL . 'ajax/vossen.ajax.php'; ?>"
	})
});
</script>

<h1>Vossen</h1>

<button id="btnDeleteRow">Verwijder vos</button>

<form action="<?php echo BASE_URL . 'ajax/vossen.ajax.php'; ?>" method="POST">
<table id="vossen">
    <thead>
        <tr>
            <th>ID</th>
            <th>Deelgebied</th>
            <th>name</th>
            <th>status</th>
            <th>speelhelft</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addVos">
                <td></td>
                <td><select name="deelgebied_id">
                    <?php 
                    foreach ($driver->getAllEvents() as $event) {
                        $areas = $driver->getAllDeelgebiedenForEvent($event->getId());
                        foreach ($areas as $area) {
                            echo '<option value="'.$area->getId().'">'.
                            $area->getName().
                            ' (' .$event->getName().')</option>';
                        }
                    } ?>
                </select></td>
                <td><input type="text" name="name" /></td>
                <td>
                    <select name="status">
                      <option value="rood">Rood</option>
                      <option value="oranje">Oranje</option>
                      <option value="groen">Groen</option>
                    </select>
                </td>
                <td><select name="speelhelft_id">
                    <?php 
                    foreach ($driver->getAllEvents() as $event) {
                        $speelhelften = $driver->getAllSpeelhelftenForEvent($event->getId());
                        foreach ($speelhelften as $speelhelft) {
                            echo '<option value="'.$speelhelft->getId().'">'.
                            $speelhelft->getId().
                            ' (' .$event->getName().')</option>';
                        }
                    } ?>
                </select></td>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($teams as $team) { ?>
        <tr id="<?php echo $team->getId(); ?>">
            <td class="read_only"><?= $team->getId() ?></td>
            <td><?= $driver->getDeelgebiedByIdSu($team->getDeelgebied())->getName() ?> <code>(<?= $team->getDeelgebied() ?>)</code></td>
            <td><?= $team->getName() ?></td>
            <td><?= $team->getStatus() ?></td>
            <td>
                <?= $driver->getEventById(
                        $driver->getSpeelhelftById(
                            $team->getSpeelhelftId()
                        )->getEventId()
                            )->getName() ?>
                <code>(<?= $team->getSpeelhelftId() ?>)</code></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>
<?php
require_once BASE_DIR . 'footer.php';
?>