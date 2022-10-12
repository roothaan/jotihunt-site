<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
global $driver, $authMgr;

$authMgr->requireSuperAdmin();

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
	    sUpdateURL: "<?= BASE_URL . 'ajax/vossen.ajax.php' ?>",
	    sDeleteURL: "<?= BASE_URL . 'ajax/vossen.ajax.php' ?>"
	})
    $('select[name="deelgebied_id"]').on('load change', (e) => {
        $('input[name="name"]').val($(e.target).find('option:selected').text().split('(')[0].trim())
    })
    $('input[name="name"]').val($('select[name="deelgebied_id"] option:selected').text().split('(')[0].trim())

});
</script>

<h1>Vossen</h1>

<button id="btnDeleteRow">Verwijder vos</button>

<form action="<?= BASE_URL . 'ajax/vossen.ajax.php' ?>" method="POST">
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
                            // Check if $area (deelgebied) is already in $teams
                            $found = array_filter($teams, function($team) use ( $area ) { return $area->getId() === $team->getDeelgebied(); });
                            if (count($found) > 0) continue;
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
                    $events = $driver->getAllEvents();
                    foreach ($events as $key => $event) {
                        $speelhelften = $driver->getAllSpeelhelftenForEvent($event->getId());
                        foreach ($speelhelften as $speelhelft) {
                            echo '<option value="'.$speelhelft->getId() . '"' .
                                 (($key === array_key_last($events)) ? 'selected="selected"' : '') .
                                 '>'.
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
        <tr id="<?= $team->getId() ?>">
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