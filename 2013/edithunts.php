<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();

if (isset($_POST ['submithunt'])) {
    $hunter_id = $_POST ['hunter_id'];
    $vossentracker_id = $_POST['vossentracker_id'];
    $code = $_POST ['code'];
    $driver->addHunt($hunter_id, $vossentracker_id, $code);
} ?>

<script type="text/javascript">
function showRij(){
	$('#rijtoggle').fadeOut(200, function() {
		$('#rijform').fadeIn(200);
	});
}
function hideRij(){
	$('#rijform').fadeOut(200, function() {
		$('#rijtoggle').fadeIn(200);
	});
}

$(document).ready(function() {
    $('.datepicker').datepicker();
    $('.timepicker').timepicker();

    $('#hunts').dataTable( {
		"bPaginate": false,
		"bLengthChange": false,
		"bFilter": false,
		"bSort": false,
		"bInfo": false,
		"bAutoWidth": true
	} );
});

</script>
<?php
// HUNTS
$allHunts = $driver->getAllHunts();
$huntcount = count($allHunts);
?>
<h1>Hunts (<?php echo $huntcount; ?>)</h1>
<?php if($authMgr->isAdmin()) { ?><form method="post"><?php } ?>
<table style="border: 0px" id='hunts'>
        <thead>
            <tr>
                <td style="width: 100px;"><b>Deelgebied</b></td>
                <td style="width: 200px;"><b>Hunters</b></td>
                <td><strong>Code</strong></td>
                <td style="width: 150px;"><b>Tijd</b></td>
                <td></td>
            </tr>
        </thead>
<?php

if ($authMgr->isAdmin()) {
    ?>
    <tfoot>
            <tr>
                <td colspan="5"><hr style="float: left; width: 785px; height: 0px; border: 0px; border-top: 1px solid #512e6b; display: block;" /> <br /></td>
            </tr>
            <tr id="rijtoggle" style="height: 30px;">
                <td colspan="6" style="text-align: left;"><small><a href="javascript:void(0);" onclick="showRij();" style="text-decoration: none;">Voeg hunt toe</a></small></td>
            </tr>
            <tr id="rijform" style="height: 30px; display: none;">
                <td><select name="vossentracker_id">
                <?php 
                $allDeelgebieden = $driver->getAllDeelgebieden();
        		foreach($allDeelgebieden as $deelgebied) {
                    $currentVos = $driver->getVosIncludingLocations($deelgebied->getName());
                    if ($currentVos) {
                        $vos_locations = $currentVos->getLocations();
                        foreach ($vos_locations as $location) { ?>
                            <option value="<?= $location->getId() ?>"><?= $deelgebied->getName() ?> - <?= $location->getAddress() ?></option>
                        <?php }
                    }
        		} ?>
                </select></td>
                <td><select name="hunter_id">
	<?php
    $ridercollection = $driver->getAllRiders();
    foreach ( $ridercollection as $rider ) {
        echo '<option value="' . $rider->getId() . '">' . $rider->getUser()->getDisplayName() . '</option>';
    }
    ?>
	</select></td>
                <td><input type="text" name="code" /></td>
                <td><em>Geen datum nodig</em></td>
                <td><input type="submit" name="submithunt" value="Voeg toe" id="button" /> <small><a href="javascript:void(0);" onclick="hideRij();" style="text-decoration: none;">Annuleer</a></small></td>
            </tr>
        </tfoot>
	<?php
}
?>
<tbody>
<?php
foreach ( $allHunts as $hunt ) { 
    $adres = "";
    if (! empty($hunt ['adres'])) {
        $adres = '<br/>' . $hunt ['adres'];
    } ?>
    <tr>
        <td><?php echo $hunt ['deelgebied'];?></td>
        <td><?php echo $hunt ['username'];?></td>
        <td><strong><code><?php echo $hunt ['code'];?></code><?php echo $adres;?></strong></td>
        <td><?php echo date('d-m-Y H:i', strtotime($hunt ["time"]));?></td>
        <td>
            <?php
            if($hunt ['goedgekeurd'] == 2) { ?>
                <a href="huntgoedkeuren/?id=<?php echo $hunt["id"];?>&goedkeuren=0" class="goedkeuren"><img src="<?php echo BASE_URL;?>images/goedgekeurd.png" title="Inleveren ongedaanmaken" alt="Inleveren ongedaanmaken" /></a>
                <?php
            } elseif($hunt ['goedgekeurd'] == 1) { ?>
                <a href="huntgoedkeuren/?id=<?php echo $hunt["id"];?>&goedkeuren=2" class="goedkeuren"><img src="<?php echo BASE_URL;?>images/goedgekeurd_ingeleverd.png" title="Goedkeuren" alt="Goedkeuren" /></a>
                <?php
            } else { ?>
                <a href="huntgoedkeuren/?id=<?php echo $hunt["id"];?>&goedkeuren=1" class="goedkeuren"><img src="<?php echo BASE_URL;?>images/goedgekeurd_leeg.png" title="Inleveren" alt="Inleveren" /></a>
                <?php
            }
            
            if ($authMgr->isAdmin()) { ?>
                <a href="deletehunt/?id=<?php echo $hunt ["id"];?>" onclick="return confirm('Weet je zeker dat je deze hunt wilt verwijderen?');"><img src="<?php echo BASE_URL;?>images/delete.png" title="Verwijderen" alt="Verwijderen" /></a>
                <?php
            } ?>
        </td>
    </tr>
    <?php
}
?>
</tbody>
    </table>
<?php
if ($authMgr->isAdmin()) {
    ?></form><?php
}
include BASE_DIR . 'blocks/scorePerDeelgebied.block.php';