<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();
require_once CLASS_DIR . 'jotihunt/Bericht.class.php';

if ($authMgr->isSuperAdmin()) {
	if (isset($_POST['bericht_id'])) {
		$bericht = new Bericht();	
	    $bericht->setBericht_id($_POST ['bericht_id']);
	    $bericht->setEventId($_POST ['event_id']);
	    $bericht->setTitel($_POST ['titel']);
	    $bericht->setDatum($_POST ['datum']);
	    $bericht->setEindtijd($_POST ['eindtijd']);
	    $bericht->setMaxpunten($_POST ['maxpunten']);
	    $bericht->setInhoud($_POST ['inhoud']);
	    $bericht->setLastupdate($_POST ['lastupdate']);
	    $bericht->setType($_POST ['type']);
		$driver->addBericht($bericht);	
	}
}

$berichtcollection = $driver->getBerichtCollection();
?>
<?php if ($authMgr->isAdmin() && !$authMgr->isSuperAdmin()) {
?>
<script type="text/javascript">
var syncJotihunt = function() {
	$( ".result" ).html( 'Syncing...' );
	$.get( "<?= BASE_URL ?>cronjob/SyncJotihunt.php", function( data ) {
		$( ".result" ).html( "Sync was performed." + data );
	});
};
</script>
<a onclick="syncJotihunt();return false;">Sync</a>
<div class="result"></div>
<?php } ?>
<div class="berichten">
	<h1>Berichten</h1>
	<?php if (!$authMgr->isSuperAdmin()) { ?>
	<h2><?= sizeof($berichtcollection) ?> bericht(en)</h2>
	<div class="berichtenContainer">
		<?php if (sizeof($berichtcollection) === 0) { ?>
			<div>Geen berichten gevonden</div>			
		<?php
		}
		foreach ( $berichtcollection as $bericht ) { ?>
			<div class="bericht <?=$bericht->getType()?>">
			    <div class="titel">
			        <a href="<?=WEBSITE_URL?>bericht/?id=<?=$bericht->getBericht_id()?>"><?=$bericht->getTitel()?></a>
			    </div>
			    <div class="type"><?=ucfirst($bericht->getType())?></div>
			    <div class="datum"><?=strftime('%a, %d %b %H:%M', strtotime($bericht->getDatum()))?></div>
			    
			    <div class="clear"></div>
			    <div class="inhoud"><?=$bericht->getInhoud()?></div>
		        <?php
			    if ($bericht->getType() == 'opdracht') { ?>
		            <div class="einddatum">Deadline: <?=ucfirst(strftime('%a, %d %b %H:%M', strtotime($bericht->getEindtijd())))?></div>
		            <?php
			    } ?>
			    </div>
			<?php
		} ?>
		<div class="clear"></div>
	</div>
</div>
<?php } ?>
<?php if ($authMgr->isSuperAdmin()) { ?>
<script type="text/javascript">
$(document).ready(function() {
	var date = new Date;
        $(".datepicker").datepicker({
        	dateFormat: "yy-mm-dd " + date.getHours() + ":" + date.getMinutes()
        });
});
</script>

<h2>Voeg een bericht toe</h2>
	<form action="<?= WEBSITE_URL ?>berichten" method="POST">
		<table>
			<tr>
				<td>bericht ID <small><font color="red"><br />Verplicht</font></small></td>
				<td><input type="text" name="bericht_id" /> - 57c149337a44b35216ac406c</td>
			</tr>
			<tr>
				<td>event ID <small><font color="red"><br />Verplicht</font></small></td>
				<td><select name="event_id">';
	            	<?php
						// Get all events for this user
						$events = $driver->getAllEvents();
					    foreach ($events as $event) {
					        echo '<option value="'.$event->getId().'">'.$event->getName().'</option>';
					    }
					?>
				</select></td>
			</tr>
			<tr>
				<td>Title <small><font color="red"><br />Verplicht</font></small></td>
				<td><input type="text" name="titel" /> - Plaats banner</td>
			</tr>
			<tr>
				<td>Datum <small><font color="red"><br />Verplicht</font></small></td>
				<td><input type="text" name="datum" value="<?= date('Y-m-d H:i') ?>" class="datepicker" /></td>
			</tr>
			<tr>
				<td>Max punten <small><font color="red"><br />Verplicht</font></small></td>
				<td><input type="text" name="maxpunten" /> - 1</td>
			</tr>
			<tr>
				<td>Eindtijd <small><font color="gray"><em><br />optioneel</em></font></small></td>
				<td><input type="text" name="eindtijd" class="datepicker" /></td>
			</tr>
			<tr>
				<td>Inhoud (HTML) <small><font color="gray"><em><br />optioneel</em></font></small></td>
				<td><textarea name="inhoud"></textarea></td>
			</tr>
			<tr>
				<td>Last update <small><font color="gray"><em><br />optioneel</em></font></small></td>
				<td><input type="text" name="lastupdate" class="datepicker" /> - leeg laten voor nieuw bericht</td>
			</tr>
			<tr>
				<td>Type <small><font color="gray"><em><br />optioneel</em></font></small></td>
				<td><select name="type">
						<option value="opdracht">Opdracht</option>
						<option value="nieuws">Nieuws</option>
						<option value="hint">Hint</option>
					</select>
					 - opdracht, nieuws, hint</td>
			</tr>
			<tr><td></td><td><input type="submit"/></td></tr>
		</table>
	</form>
<?php } ?>