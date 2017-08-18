<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();

if (isset($_GET['organisationId']) && isset($_GET['eventId']) && isset($_GET['action'])) {
    $organisationId = $_GET['organisationId'];
    $eventId = $_GET['eventId'];
    $action = $_GET['action'];
    
    if ('add' == $action) {
        $driver->addOrganisationToEvent($eventId, $organisationId);
    }
    if ('remove' == $action) {
        $driver->removeOrganisationFromEvent($eventId, $organisationId);
    }
}

$organisations = $driver->getAllOrganisations();
?>

<script type="text/javascript">
$(document).ready(function() {
$('#organisations').dataTable( {
	"bPaginate": false,
	"bLengthChange": false,
	"bFilter": false,
	"bSort": false,
	"bInfo": false,
	"bAutoWidth": true,
	"aoColumns": [
            {sName: "id" },
            {sName: "name" },
            {sName: "submit" }
      ]
	})
	.makeEditable({
	    sUpdateURL: "<?= BASE_URL . 'ajax/organisations.ajax.php' ?>",
	    sDeleteURL: "<?= BASE_URL . 'ajax/organisations.ajax.php' ?>"
	})
});
</script>

<h1>Organisaties</h1>

<button id="btnDeleteRow">Verwijder organisatie</button>

<form action="<?= BASE_URL . 'ajax/organisations.ajax.php' ?>" method="POST">
<table id="organisations">
    <thead>
        <tr>
            <th>ID</th>
            <th>name</th>
            <th></th>
        </tr>
    </thead>
 	<tfoot>
            <tr id="addOrganisation">
                <td></td>
                <td><input type="text" name="name" /></td>
                <td>
                    <input type="submit" value="Voeg toe" class="button" />
                </td>
            </tr>
        </tfoot>
    <tbody>
        <?php foreach ($organisations as $organisation) { ?>
        <tr id="<?= $organisation->getId() ?>">
            <td class="read_only"><?= $organisation->getId() ?></td>
            <td><?= $organisation->getName() ?></td>
            <td class="read_only">
                <?php // Which event(s) are they in?
                    $events = $driver->getEventsForOrganisation($organisation->getId());
                    $myEventIds = array();
                    foreach ($events as $event) {
                        $myEventIds[] = $event->getId();
                    }
                    
                    $events = $driver->getAllEvents();
                    foreach ($events as $event) {
                        $inIt = in_array($event->getId(), $myEventIds);
                        echo 'Event: <strong>' . $event->getName() . '</strong>';
                        if ($inIt) {
                            echo ' <a href="?organisationId='.$organisation->getId().'&eventId='.$event->getId().'&action=remove">remove</a>';
                        } else {
                            echo ' <a href="?organisationId='.$organisation->getId().'&eventId='.$event->getId().'&action=add">add</a>';
                        }
                        echo '<br />';
                    }
                ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</form>