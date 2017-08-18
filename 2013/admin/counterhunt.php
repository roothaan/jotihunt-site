<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();

$counterhuntrondjes = $driver->getAllCounterhuntrondjesSu();
?>

<script type="text/javascript">
$(document).ready(function() {
$('#counterhuntrondjes').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": false,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": true,
	"aoColumns": [
            {sName: "id" },
            {sName: "deelgebied_id" },
            {sName: "organisation_id" },
            {sName: "name" },
            {sName: "active" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?= BASE_URL . 'ajax/counterhuntrondjes.ajax.php' ?>",
	    sDeleteURL: "<?= BASE_URL . 'ajax/counterhuntrondjes.ajax.php' ?>"
	})
});
</script>

<h1>Counterhunt rondjes</h1>

<button id="btnDeleteRow">Verwijder ronde</button>

<form action="<?= BASE_URL . 'ajax/counterhuntrondjes.ajax.php' ?>" method="POST">
<table id="counterhuntrondjes">
    <thead>
        <tr>
            <th>ID</th>
            <th>Deelgebied</th>
            <th>Organisatie</th>
            <th>Naam</th>
            <th>Actief</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addCounterhuntrondje">
                <td></td>
                <td><select name="deelgebied_id">
                    <?php 
                    foreach ($driver->getAllEvents() as $event) {
                        foreach ($driver->getAllDeelgebiedenForEvent($event->getId()) as $deelgebied) {
                            echo '<option value="'.$deelgebied->getId().'">'.
                            $deelgebied->getName() . ' (' .$event->getName().')</option>';
                        }
                    } ?>
                </select></td>
                <td><select name="organisation_id">
                    <?php
                    $orgs = $driver->getAllOrganisations();
                    foreach ($orgs as $org) { ?>
                        <option value="<?= $org->getId() ?>"><?= $org->getName() ?></option>
                    <?php } ?>
                </select></td>
                <td><input type="text" name="name" /></td>
                <td>
                    <select name="active">
                        <option value="t">Active</option>
                        <option value="f">NOT Active</option>
                    </select>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($counterhuntrondjes as $counterhuntrondje) { ?>
        <tr id="<?= $counterhuntrondje->getId() ?>">
            <td class="read_only"><?= $counterhuntrondje->getId() ?></td>
            <td class="read_only">
                <?php
                $deelgebied = $driver->getDeelgebiedByIdSu($counterhuntrondje->getDeelgebiedId()); 
                $event = $driver->getEventById($deelgebied->getEventId());
                    ?>
                <?= $deelgebied->getName() ?> (<?= $event->getName() ?>) (<?= $counterhuntrondje->getDeelgebiedId() ?>)
            </td>
            <td class="read_only">
                <?php $organisation = $driver->getOrganisationById($counterhuntrondje->getOrganisationId()); ?>
                <?= $organisation->getName() ?> (<?= $counterhuntrondje->getOrganisationId() ?>)
            </td>
            <td><?= $counterhuntrondje->getName() ?></td>
            <?php $active = $counterhuntrondje->getActive() ?>
            <td><?= 't' == $active ? 'Active' : 'x' ?></td>
            <td class="read_only"></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>