<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();

require_once BASE_DIR . 'includes/make_map_js.include.php';
require_once CLASS_DIR . 'jotihunt/MapOptions.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';

$allDeelgebieden = $driver->getAllDeelgebieden();
if (sizeof($allDeelgebieden) === 0 ) {
	?>Dit event heeft nog geen deelgebieden<?php
} else {

if(isset($urlArray[1]) && !empty($urlArray[1])) {
	$deelgebiedName = urldecode($urlArray[1]);
} else {
	$deelgebiedName = $allDeelgebieden[0]->getName();
}

$currentVos = $driver->getVosIncludingLocations($deelgebiedName);
$huidigRondje = $driver->getActiveCounterhuntRondje($deelgebiedName);

if($currentVos && isset($_GET['counterhuntrondjeId']) 
	&& !empty($_GET['counterhuntrondjeId']) && is_numeric($_GET['counterhuntrondjeId'])
	&& $_GET['counterhuntrondjeId'] > 0) {
	$driver->setActiveCounterhuntrondjeId($deelgebiedName, $_GET['counterhuntrondjeId']);
}

$savex = '';
$savey = '';
$error = '';

// Fetch active hunters
$ridercollection = $driver->getActiveRiders($deelgebiedName);

if (isset($_POST ["x"]) && isset($_POST ["y"])) {
	$type = 0;
	if(isset($_POST["submithint"])) {
		$type = 0;
	} else if(isset($_POST["submitspot"])) {
		$type = 3;
	} else if(isset($_POST["submithunt"])) {
		$type = 2;
	}
    $savex = $_POST ["x"];
    $savey = $_POST ["y"];
    $latitude = $_POST ["f"];
    $longitude = $_POST ["l"];
    $adres = $_POST ["adres"];
    $time = strtotime($_POST ['startdatum'] . ' ' . $_POST ['starttijd']);
    
    // Fetch latest coordinates
	$lastx = "";
	$lasty = "";
	$vos_locations = $currentVos->getLocations();
	if (count($vos_locations) > 0) {
		$vos_locatie = $vos_locations [0];
		$lastx = $vos_locatie->getX();
		$lasty = $vos_locatie->getY();
	}
    
    if (! ($savex > 10000 && $savex < 999999 && $savey > 10000 && $savey < 999999)) {
        $error = "<small><font color=\"red\">De coordinaten die je hebt ingevoerd zijn ongeldig!</font></small>";
        $lastx = $savex;
        $lasty = $savey;
    } elseif($savex == $lastx && $savey == $lasty) {
    	$error = "<small><font color=\"red\">Dit coördinaat is al toegevoegd!</font></small>";
        $lastx = $savex;
        $lasty = $savey;
    } else {
        while ( $savex < 100000 ) {
            $savex = $savex * 10;
        }
        while ( $savey < 100000 ) {
            $savey = $savey * 10;
        }
    
    	if(isset($_POST['counterhuntrondjeId']) && !empty($_POST['counterhuntrondjeId']) 
    		&& is_numeric($_POST['counterhuntrondjeId']) && $_POST['counterhuntrondjeId'] > 0) {
    		$counterhuntrondjeId = $_POST['counterhuntrondjeId'];
    	} else {
    		$counterhuntrondje = $driver->getActiveCounterhuntRondje($deelgebiedName);
    		$counterhuntrondjeId = $counterhuntrondje ? $counterhuntrondje->getId() : 0;
    	}
    
        $newLocation = $driver->addVosLocation($deelgebiedName, $savex, $savey, $latitude, $longitude, $adres, $counterhuntrondjeId, $time, $type);
        if ($newLocation) {
	        if($type == 2 && isset($_POST['rider']) && !empty($_POST['rider']) && isset($_POST['code']) && !empty($_POST['code'])) {
	        	$driver->addHunt($_POST['rider'], $newLocation->getId(), $_POST['code']);
	        }
	        
	        // Send message to devices
	        $vosX = $driver->getVosXYByDeelgebied($deelgebiedName);
	        $allGcmIds = $driver->getAllActiveGcms();
	        $payload = array (
	                'location' => $newLocation->toArray(),
	                'teamName' => $vosX->getName() 
	        );
	        $gcmSender = new GcmSender();
	        $gcmSender->setReceiverIds($allGcmIds);
	        $gcmSender->setPayload($payload);
	        $result = $gcmSender->send();
	        // End send to GCM
        
	        if (false === $result) {
	            echo '<div>Error sending location(s) to GCM: ' . $result . '</div>';
	        } else {
	            echo '<div><em>Locatie succesvol naar Google verstuurd. <a href="javascript:$(\'#gcmDetails\').toggle();">Details</a></em></div><div id="gcmDetails" style="display:none;">' . $result . '</div>';
	        }
        }
    }
}

// Fetch latest coordinates
$lastx = '';
$lasty = '';
$vos_locations = array();
if ($currentVos) {
	$vos_locations = $currentVos->getLocations();
	if (count($vos_locations) > 0) {
	    $vos_locatie = $vos_locations [0];
	    $lastx = $vos_locatie->getX();
	    $lasty = $vos_locatie->getY();
	}
}
$aantalVosLocations = count($vos_locations);
?>

<script type="text/javascript">
$(document).ready(function() {
    $(".datepicker").datepicker();
    $('.timepicker').timepicker();
    
    $(".voegLocatieToe").click(function() {
    	$('#locform').slideToggle('200');
    	$('#oldloc').slideToggle('200');
    });
    
    $(".wijzigRondeBtn").click(function() {
    	$('#rondeForm').slideToggle('200');
    });
    
    $(".rdContainer .check").click(function(e) {
    	e.preventDefault();
    	coordRD();
    });
    
    $(".latLongContainer .check").click(function(e) {
    	e.preventDefault();
    	coordLL();
    });
    
    $(".adresContainer .check").click(function(e) {
    	e.preventDefault();
    	coordAddress();
    });
    
    $(".kaartContainer .crosshair").click(function(e) {
    	e.preventDefault();
    	$('#mapcenter').show();
    });
    
    $(".kaartContainer .gebruikCenter").click(function(e) {
    	e.preventDefault();
    	coordCenter();
    });
    
    $(".locatieContainer .gebruikLocatie").click(function(e) {
    	e.preventDefault();
    	coordLoc();
    });
    
    $(".delVos").click(function(e) {
    	return confirm('Weet je zeker dat je deze locatie wilt verwijderen?');
    });
    
    $(".showRondeBtn").click(function() {
    	$(".rondeKeuzeContainer").slideToggle(200);
    });
});
</script>

<div class="vossenContainer">
	<? // Create menu ?>
	<div class="vossenMenuContainer">
		<?php
		foreach($allDeelgebieden as $deelgebied) { 
			$current = '';
			if($deelgebied->getName() == $deelgebiedName) {
				$current = 'currentTeam';
			} ?>
			
			<div class="vossenMenuItem <?=$current?>">
				<a href="<?=WEBSITE_URL?>vossen/<?=$deelgebied->getName()?>"><span class="team">Team</span>
				<span class="deelgebied"><?=$deelgebied->getName()?></span></a>
			</div>
			<?php
		} ?>
		<div class="clear"></div>
	</div>
	
	<div id="mapcontainer" style="float:left">
		<div id="vos_map">
		    <div id="map"></div>
			<?php
			if ($aantalVosLocations == 0) { 
				    $mapOptions = new MapOptions();
				    $mapOptions->x = 189898;
				    $mapOptions->y = 460709;
				    $mapOptions->team = $deelgebiedName;
				    $mapOptions->zoom = 9;
				    make_map($mapOptions); 
			} else {
			    if (isset($_GET ["id"]) && ! empty($_GET ["id"])) {
			        $vosid = $_GET ["id"];
			        $location = $driver->getVosXYById($vosid);
			        if ($location != false) {
			            $mapOptions = new MapOptions();
			            $mapOptions->x = $location->getX();
			            $mapOptions->y = $location->getY();
			            $mapOptions->team = $deelgebiedName;
			            $mapOptions->crosshair = true;
			            make_map($mapOptions);?>
			            
			            <div style="clear: both;width: 100%; padding: 20px 0px; text-align: center;">
			            	Vossenlocatie #<?=$vosid?> (<?=$location->getX()?>,<?=$location->getY()?>) is te vinden onder de crosshair <img src="<?=BASE_URL?>images/crosshair.png" style="margin-bottom: -7px;"/>
		            	</div>
		            	<?php
			        }
			    } else if (count($vos_locations) > 0) {
		            $vos_lastloc = $vos_locations [0];
		            $mapOptions = new MapOptions();
		            $mapOptions->x = $vos_lastloc->getX();
		            $mapOptions->y = $vos_lastloc->getY();
		            $mapOptions->team = $deelgebiedName;
		            make_map($mapOptions);
			    }
			} ?>
		</div>
	</div>
	<div id="vos_lijst">
	<?php if ($currentVos) { ?>
	<script type="text/javascript" src="<?=BASE_URL?>js/vossenPage.js"></script>
	
		<div class="errorMsg"><?=$error?></div>
		
		<?php
		if($authMgr->hasGroup("admin")) { ?>
			<div class="changeStatusContainer">
				Sudo vos changestatus <select name="status" id="statusChange">
					<option value="groen" <?php if($currentVos->getStatus() == "groen") echo "selected";?>>Groen</option>
					<option value="oranje"<?php if($currentVos->getStatus() == "oranje") echo "selected";?>>Oranje</option>
					<option value="rood"<?php if($currentVos->getStatus() == "rood") echo "selected";?>>Rood</option>
				</select>
			</div>
			<script>
				$("#statusChange").change(function() {
					$.ajax({
					    url: "<?=BASE_URL?>ajax/vos.ajax.php",
					    type: "POST",
					    data: {
					        status: $(this).val(),
					        deelgebied: "<?=$deelgebiedName?>"
					    }
					});
				});
			</script>
			<?php
		} ?>
		
		<div class="voegLocatieToe">Voeg locatie toe</div>
		<?php if ($huidigRondje) { ?>
		<div class="wijzigRondeBtn">Wijzig ronde</div>
		<?php } ?>
		<div class="clear"></div>
		<a href="<?=WEBSITE_URL?>invoer" class="invoerlink">Alle deelgebieden tegelijk invoeren</a>
		
		<div id="locform">
			<form id="submitlocatie" action="<?=WEBSITE_URL?>vossen/<?=$deelgebiedName?>" method="post">
				<div class="stepOneContainer stepContainer">
					<div class="heading">1. Vul een co&ouml;rdinaat of adres in</div>
					<div class="rdContainer optieContainer">
						<div class="locatieToevoegenLabel">RD <span class="smallLabel">(Dit co&ouml;rdinaat wordt opgeslagen)</span></div>
						<input type="text" name="x" id="x" value="219153" /> 
						<input type="text" name="y" id="y" value="440393" />
						<input type="button" name="previewrd" value="Check" class="check" />
					</div>
					<div class="latLongContainer optieContainer">
						<div class="locatieToevoegenLabel">LatLong</div>
						<input type="text" name="f" id="f" value="51.94924492918301" /> 
						<input type="text" name="l" id="l" value="6.320918574758574" />
						<input type="button" name="previewll" value="Check" class="check" />
					</div>
					<div class="adresContainer optieContainer">
						<div class="locatieToevoegenLabel">Adres</div>
						<input type="text" name="adres" id="adres" value="Straatnaam, stad" />
						<input type="button" name="previewadres" value="Check" class="check" />
					</div>
					<div class="kaartContainer optieContainer">
						<div class="locatieToevoegenLabel">Kaart</div>
						<input type="button" name="previewkaarttoggle" value="Crosshair" class="crosshair" />
						<input type="button" name="previewkaart" value="Gebruik center van kaart" class="gebruikCenter" />
					</div>
					<div class="locatieContainer optieContainer">
						<div class="locatieToevoegenLabel">Huidige locatie <span class="smallLabel">(Browser-afhankelijk)</span></div>
						<input type="button" name="previewkaartlocatie" value="Gebruik huidige locatie" class="gebruikLocatie" />
					</div>
				</div>
				<div class="stepTwoContainer stepContainer">
					<div class="heading">2. Controleer de locatie</div>
					<div class="text">De locatie bevindt zich onder de rode marker op de kaart.</div>
				</div>
				<div class="stepThreeContainer stepContainer">
					<div class="heading">3. Wanneer</div>
					<div style="margin-left: 25px;">
					    <input type="text" name="startdatum" value="<?=date('d-m-Y')?>" class="datepicker" />
					    <input type="text" name="starttijd" value="<?=date('H:i')?>" class="timepicker" />
					</div>
					<div class="setRondeContainer">
						<div class="showRondeBtn">Pas de ronde handmatig aan</div>
						<div class="rondeKeuzeContainer">
							<select name="counterhuntrondjeId">
								<option value="">Automatisch</option>
								<?php
								for($i=1;$i<=4;$i++) { ?>
									<option value="<?=$i?>">Ronde <?=$i?></option>
									<?php
								} ?>
							</select>
						</div>
					</div>
				</div>
				<div class="stepFourContainer stepContainer">
					<div class="heading">4. Druk op de Toevoegen-knop</div>
					<fieldset>
						<legend>Alleen voor de hunt</legend>
						<div class="codeContainer">
							<span class="label">Code:</span> 
							<input type="text" name="code" />
						</div>
						<div class="teamContainer">
							<span class="label">Team:</span> 
							<select name="rider">
								<?php
								foreach ( $ridercollection as $rider ) { ?>
									<option value="<?=$rider->getId()?>"><?=$rider->getUser()->getDisplayName()?></option>
							        <?php
							    } ?>
							</select>
						</div>
					</fieldset>
					<div style="margin-left: 75px;">
						<input type="submit" name="submithint" value="Hint" id="submit_hint" style="font-weight: bold;" disabled="disabled"/>
						<input type="submit" name="submitspot" value="Spot" id="submit_spot" style="font-weight: bold;" disabled="disabled"/>
						<input type="submit" name="submithunt" value="Hunt" id="submit_hunt" style="font-weight: bold;" disabled="disabled"/>
					</div>
				</div>
	
			</form>
		</div>
		
		<div id="rondeForm">
			<?php if ($huidigRondje) { ?>
			<div class="huidigeRonde">De huidige ronde van gebied <?=$deelgebiedName?> is: <?=$huidigRondje->getName()?></div>
			<?php } else { ?>
			<div class="huidigeRonde">Gebied <?=$deelgebiedName?> heeft nog geen ronde.</div>
			<?php } ?>
			
			<div class="startRonde">
				<div class="label">Kies een ronde</div>
				<div class="rondes">
					<?php
					foreach ($driver->getCounterhuntrondjeForDeelgebied($deelgebiedName) as $counterhuntrondje) {
						$rondeActief = "";
						if(null !== $huidigRondje && $huidigRondje->getId() == $counterhuntrondje->getId()) {
							$rondeActief = "actief";
						} ?>
						<a href="<?=WEBSITE_URL?>vossen/<?=$deelgebiedName?>/?counterhuntrondjeId=<?=$counterhuntrondje->getId()?>" title="Start ronde <?=$counterhuntrondje->getName()?>" class="rondeNummer <?=$rondeActief?>"><?=$counterhuntrondje->getName()?></a>
						<?php
					} ?>
				</div>
			</div>
		</div>
	<?php } // End of if($currentVos) ?>
		
		<div id="hunters">
			<div class="label">Actieve hunters</div>
			<div class="list">
				<?php
				if (empty($ridercollection)) { ?>
				    Geen hunters actief op dit moment.
				    <?php
				} else {?> 
					<ul>
						<?php
					    foreach ( $ridercollection as $rider ) {
							echo '<li>' . $rider->getUser()->getDisplayName();
							if ($rider->getBijrijder()) {
					    		echo '('.$rider->getBijrijder().')';
							}
							$telNummers = JotihuntUtils::getPhoneNumbersForUserId($rider->getUser()->getId());
							if ($telNummers) {
								echo ': ' . $telNummers;
							}
							echo '</li>';
					    } ?>
				    </ul>
				    <?php
				} ?>
			</div>
		</div>
		
		<?php
		function createVosLocationRow($nummer, $locatie, $deelgebiedName, $isAdmin) { ?>
			<div class="vosLocation">
				#<?=$nummer?>: <a href="<?=WEBSITE_URL?>vossen/<?=$deelgebiedName?>?id=<?=$locatie->getId()?>" ><?=$locatie->getDate()?> : <?=$locatie->getX()?>, <?=$locatie->getY() ?></a>
	            <?php
	            if ($isAdmin) { ?>
	                &nbsp;<a href="<?=WEBSITE_URL?>delete_locatie/<?=$locatie->getId()?>/<?=$deelgebiedName?>" class="delVos">[x]</a>
	                <?php
	            } ?>
            </div>
            <?php
		}
		
		$i = 0;
		if (count($vos_locations) > 0) { 
			$newLocatie = $vos_locations[0]; ?>
	    	<div id="newloc">
	    		<div class="label">Laatste locatie</div>
	            <span id="adresss"><?=$newLocatie->getAddress()?></span>
	            <?php 
	            createVosLocationRow($aantalVosLocations, $newLocatie, $deelgebiedName, $authMgr->isAdmin()); ?>
            </div>
            <div id="oldloc">
        		<div class="label">Oudere locaties</div>
	    		<?php
	    		if ($aantalVosLocations == 1) { ?>
			    	Geen oudere locaties bekend.
			    	<?php
			    } else {
		    		foreach ( $vos_locations as $locatie ) {
		        		$num = $aantalVosLocations - $i;
			        	if ($num < 10) $num = "0" . $num;
			        	
				        if ($i > 0) { 
				        	createVosLocationRow($num, $locatie, $deelgebiedName, $authMgr->isAdmin());
				        }
				        $i ++;
				    }
			    } ?>
		    </div>
		    <?php
		} else {
			if ($currentVos) {
		    	echo 'Geen vossenlocaties bekend.';
			} else {
				echo 'Dit deelgebied heeft (nog) geen vossen.';
			}
		    
		} ?>
	</div>
	
	<div id="voscontainer">&nbsp;&nbsp;</div>
	
	<?php
	$amountOfVossenLocations = $driver->getTotalAmountOfVossenLocations(); ?>
	<p>Totaal aantal vossenlocaties: <strong><?=$amountOfVossenLocations?></strong></p>
</div>
<?php } // end of amountofDeelgebieden === 0 ?>