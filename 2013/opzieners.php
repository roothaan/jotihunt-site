<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin(); ?>

<script type="text/javascript">
function showOpziener(){
	$('#opzienertoggle').fadeOut(200, function() {
		$('#opzienerform').fadeIn(200);
	});
}
function hideOpziener(){
	$('#opzienerform').fadeOut(200, function() {
		$('#opzienertoggle').fadeIn(200);
	});
}

$(document).ready(function() {
    $('#opzieners').dataTable( {
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bSort": false,
		"bInfo": false,
		"bAutoWidth": false
	} );
});
</script>

<h1>Opzieners</h1>
<form action="<?=BASE_URL?>ajax/opzieners.ajax.php" method="post">
    <table style="border: 0px" id="opzieners">
        <thead>
            <tr>
                <td style="width: 150px;"><strong>Deelgebied</strong></td>
                <td style="width: 200px;"><strong>Opziener</strong></td>
                <td style="width: 200px;"><strong>Tel. nummer</strong></td>
                <td style="width: 20px;">&nbsp;</td>
            </tr>
        </thead>
        
        <?php if ($authMgr->isAdmin()) { ?>
         	<tfoot>
                <tr>
                    <td colspan="4"><hr style="float: left; width: 575px; height: 0px; border: 0px; border-top: 1px solid #512e6b; display: block;" /></td>
                </tr>
                <tr id="opzienertoggle" style="height: 30px;">
                    <td colspan="4" style="text-align: left;"><small><a href="javascript:void(0);" onclick="showOpziener();" style="text-decoration: none;">Voeg Opziener toe</a></small></td>
                </tr>
                <tr id="opzienerform" style="height: 30px; display: none;">
                    <td><select name="deelgebied_id">
                    <?php 
                    $allDeelgebieden = $driver->getAllDeelgebieden();
            		foreach($allDeelgebieden as $deelgebied) { ?>
                        <option value="<?= $deelgebied->getId() ?>"><?= $deelgebied->getName() ?></option>
                        <?php
            		} ?>
                    </select> <select name="type">
                            <option value="1">Hoofd</option>
                            <option value="2">Stand-in</option>
                    </select></td>
                    <td><select name="userId">
                <?php
                    $users = $driver->getAllUsers();
                    foreach ( $users as $user ) {
                        echo '<option value="' . $user->getId() . '">' . $user->getDisplayName() . '</option>';
                    }
                ?></td>
                    <td><em>Wordt automatisch ingevuld</em></td>
                    <td><input type="submit" name="submitopziener" value="Voeg toe" class="button" /> <small><a href="javascript:void(0);" onclick="hideOpziener();" style="text-decoration: none;">Annuleer</a></small></td>
                </tr>
            </tfoot>
            <?php
        } ?>

        <tbody>
            <?php
            $result = $driver->getAllOpzieners();
            foreach ( $result as $opziener ) { ?>
                <tr>
                    <td>
                        <?= $driver->getDeelgebiedById($opziener->getDeelgebiedId())->getName() ?>
                        <?php
                        if ($opziener->getType() == 2) echo " <i>(Stand-in)</i>"; ?>
                    </td>
                    <td><?= $opziener->getDisplayName() ?></td>
                    <td><?= JotihuntUtils::getPhoneNumbersForUserId($opziener->getUserId()) ?></td>
                    <td>
                        <?php
                        if ($authMgr->isAdmin()) { ?>
                            <a href="deleteopziener/<?= $opziener->getId() ?>" onclick="return confirm('Weet je zeker dat je deze Opziener wilt verwijderen?');">[x]</a>
                            <?php
                        } ?>
                    </td>
                </tr>
                <?php
            } ?>
        </tbody>
    </table>
</form>