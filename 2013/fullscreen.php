<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();
$headerOptions = array();
$headerOptions['title'] = 'Fullscreen Kaart';
$headerOptions['includeBody'] = false;
require_once BASE_DIR . 'header.php';
?>

<style>
	.foxText {
		font-size: 20px;
	}

	.huntNow {
		color: #111111;
	}
	
	.huntWait {
		color: #FFFF00;
	}
</style>

<?php if(GOOGLE_MAPS_ENABLED) { ?>
<script type="text/javascript" src="<?= GOOGLE_MAPS_URI ?>"></script>
<?php } ?>

<script type="text/javascript">
<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
	var team<?= $deelgebied->getId() ?> = {
		id: 0,
		name: '',
		date: '',
		address : '',
		lat: 0,
		lng: 0,
		lastHuntTime: 0,
		status: ''
	};

var team<?= $deelgebied->getId() ?>_old = {
		id: 0,
		name: '',
		date: '',
		address : '',
		lat: 0,
		lng: 0,
		lastHuntTime: 0,
		status: ''
	};
<?php }?>
	var rank = 0;
	var rank_old = 0;
	var msg = '';
	var msg_old = '';
	var hunt = '';
	var hunt_old = '';
	var first = 1;

	<?php if(GOOGLE_MAPS_ENABLED) { ?>
	var geocoder = new google.maps.Geocoder();
	<?php } ?>
	
	function beforeParsing() {
		$('#refreshicon').show();

		//Hide current indicators
		$('#arrow_rank').css('visibility','hidden');
		$('#arrow_msg').css('visibility','hidden');
		$('#arrow_hunt').css('visibility','hidden');

		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','hidden');
			//Save current data to old
			team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
		<?php }?>

		rank_old = rank;
		msg_old = msg;
		hunt_old = hunt;
	}
	
	function afterParsing() {
		rank_old = rank;
		msg_old = msg;
		hunt_old = hunt;
		first = 0;

		//Hide preloader animation
		$('#refreshicon').hide();

		//Compare and visualize data
		compareData();
	}
	
	function showRank(rank) {
		$('#rank').html('#' + (rank == 0 ? '?' : rank ));
	}
	
	function showMessage(msg, lastHunt) {
		if (typeof msg !== 'undefined') {
		    $('#msgcontainer').html(msg);
		}
		if (typeof lastHunt !== 'undefined') {
		    $('#huntcontainer').html('Laatste hunt: <strong>' + lastHunt + '</strong>');
		}
	}
	
	function getJotihuntData(){
		//Set a timeout for 15 seconds
		setTimeout('getJotihuntData()',15000);

		$.getJSON('<?=BASE_URL?>spyJson.php', function( data ) {
			beforeParsing();
			showRank(data['plaats']);
			showMessage(data['lastbericht'], data['lasthunt']);
			showVossen(data['vossen']);
			afterParsing();
		});
	}
	
	function compareData(){		
		if(msg != msg_old){
			$('#arrow_msg').css('visibility','visible');
		}
		
		if(rank != rank_old){ 
			$('#arrow_rank').css('visibility','visible');
		}
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			if(team<?= $deelgebied->getId() ?>_old.address != team<?= $deelgebied->getId() ?>.address) {
				if (document.getElementById("iframe_<?= $deelgebied->getId() ?>")) {
					// I think this refreshes the page?
					document.getElementById("iframe_<?= $deelgebied->getId() ?>").src = document.getElementById("iframe_<?= $deelgebied->getId() ?>").src;
				}
			}
		<?php } ?>
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			if(team<?= $deelgebied->getId() ?>_old.status != team<?= $deelgebied->getId() ?>.status){
				$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','visible');
			}
		<?php } ?>
	}
	
	function showVossen(vossen) {
		<?php
		$counter = 0;
		foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
		if (vossen['<?= $deelgebied->getId() ?>']) {
			team<?= $deelgebied->getId() ?> = vossen['<?= $deelgebied->getId() ?>'];
		}
		<?php }?>
		
		//Fix old data if first
		if(first == 1){
			<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
				team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
			<?php }?>
		}

		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#status_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>_old.status);
			$('#link_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>.status);
			<?php if(GOOGLE_MAPS_ENABLED) { ?>
			if(team<?= $deelgebied->getId() ?>.address == '0' || team<?= $deelgebied->getId() ?>.address == '') {
				if(team<?= $deelgebied->getId() ?>.lat != "0" && team<?= $deelgebied->getId() ?>.lng != "0") {
					var latlng = new google.maps.LatLng(team<?= $deelgebied->getId() ?>.lat, team<?= $deelgebied->getId() ?>.lng);
					geocoder.geocode({'latLng': latlng}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (results[0]) {
								$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+results[0].formatted_address+'</strong> ('+team<?= $deelgebied->getId() ?>.date+')');
							} else if (results[1]) {
								$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+results[1].formatted_address+'</strong> ('+team<?= $deelgebied->getId() ?>.date+')');
							}
						}
					});
				} else {
					$('#loc_<?= $deelgebied->getId() ?>').html("Geen locaties bekend");
				}
			}else{
				$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+team<?= $deelgebied->getId() ?>.address +'</strong> ('+team<?= $deelgebied->getId() ?>.date+')');
			}
			<?php } ?>
			
			if(typeof team<?= $deelgebied->getId() ?>.lastHuntTime !== 'undefined' && team<?= $deelgebied->getId() ?>.lastHuntTime != 0) {
				var newHuntTime<?= $deelgebied->getId() ?> = Date.parse(team<?= $deelgebied->getId() ?>.lastHuntTime+' GMT+0100') + (60 * 60) - (60*60); // + 1hour for hunt - 1hour for daylightsaving
				startCountdown($(".text<?= $deelgebied->getId() ?>"),newHuntTime<?= $deelgebied->getId() ?>/1000);
			}		
			<?php }?>
	}
	$(document).ready(function(){
		getJotihuntData();
		startClock();
	});
	
	function startClock() {
	    var today=new Date();
	    var h=today.getHours();
	    var m=today.getMinutes();
	    var s=today.getSeconds();
	    m = checkTime(m);
	    s = checkTime(s);
	    document.getElementById('currentTime').innerHTML = h+":"+m+":"+s;
	    var t = setTimeout(function(){startClock()},500);
	}
	
	function checkTime(i) {
	    if (i<10) {i = "0" + i};  // add zero in front of numbers < 10
	    return i;
	}
	
	function startCountdown(clockContainer, untilTimeSeconds) {
		var currentTimeSeconds = new Date().getTime() / 1000;
		var differenceSeconds = untilTimeSeconds - currentTimeSeconds;
		
		if(differenceSeconds > 0) {
			var sec_num = parseInt(differenceSeconds, 10); // don't forget the second param
		    var hours   = Math.floor(sec_num / 3600);
		    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
		    var seconds = sec_num - (hours * 3600) - (minutes * 60);
		
		    hours = checkTime(hours);
		    minutes = checkTime(minutes);
		    seconds = checkTime(seconds);
		    
		    displayTime = hours+':'+minutes+':'+seconds;
		    
		    if(!$(clockContainer).hasClass("huntWait")) {
		    	$(clockContainer).addClass("huntWait");
		    }
		} else {
			var displayTime = "Actief hunten";
			if($(clockContainer).hasClass("huntWait")) {
		    	$(clockContainer).removeClass("huntWait");
		    }
		    if(!$(clockContainer).hasClass("huntNow")) {
		    	$(clockContainer).addClass("huntNow");
		    }
		}
		
		$(clockContainer).html("("+displayTime+")");
		clearTimeout($(clockContainer).data("countdownTimeout"));
	    $(clockContainer).data("countdownTimeout", setTimeout(function(){startCountdown(clockContainer, untilTimeSeconds)},500));
	}
</script>

	<img src="<?=BASE_URL?>images/preloader.gif" style="position: absolute; margin: auto; z-index: 100; top: 190px; left: 50%;" id="refreshicon" />
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right; padding-right: 30px; font-size: 24px; font-weight: bold; color: #66665E;"><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 30px;" id="arrow_msg" /><span id="msgcontainer"></span><br /><span id="huntcontainer"></span></td>
            <td style="text-align:center; font-size: 60px; font-weight: bold; color: #66665E;"><span id="currentTime"></span></td>
            <td style="padding-left: 30px; text-align: left; font-size: 60px; color: #D19917; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);">
            	<img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 30px;" id="arrow_rank" />
            	<span id="rank"></span>
            	<a href="./">
        			<img src="<?=THEME_LOGO_URL?>" alt="Jotihunt logo" style="height:80px;" />
				</a>
			</td>
        </tr>
	</table>
		<?php
        $deelgebieden = $driver->getAllDeelgebieden();
        $height = 100 / (count($deelgebieden) / 3);
        foreach ($deelgebieden as $deelgebied) { ?>
            <table style="width: 33%; display: inline-table;height: calc(<?= $height ?>vh - <?= $height ?>px)">
            <tr>
            <td
            	id="status_<?= $deelgebied->getId() ?>"
            	class="wit"
            	style="height: 40px; text-align: left; color: #000000; font-size: 10px; background-color: gray;">
            	<a
            		href="<?=WEBSITE_URL?>vossen/<?= $deelgebied->getName() ?>"
            		id="link_<?= $deelgebied->getId() ?>"
            		class="wit"
            		style="font-size: 32px; display: block; width: 100%; height: 100%; text-decoration: none; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);">
            		<img
            			src="<?=BASE_URL?>images/arrow-bouncing-left.gif"
            			style="visibility: hidden; height: 30px;" 
            			id="arrow_<?= $deelgebied->getId() ?>"
            		/>
        			<?= $deelgebied->getName() ?> 
        			<span 
        				class="huntNow text<?= $deelgebied->getId() ?> foxText">(Actief hunten)</span>
        		</a>
        	</td>
        	</tr><tr>
            <td
            	id="mapcon_<?= $deelgebied->getId() ?>"
            	style="min-height: 300px; width: 33%; overflow-x: hidden; overflow-y: hidden;">
            	<?php if(GOOGLE_MAPS_ENABLED) { ?>
            	<iframe
            		src="<?=BASE_URL?>fullscreen_map.php?team=<?= $deelgebied->getName() ?>"
            		id="iframe_<?= $deelgebied->getId() ?>"
            		width="100%"
            		height="100%"
			style="min-height: 300px;">
            	</iframe>
            	<?php } ?>
            </td>
        	</tr><tr>
            <td 
            	id="loc_<?= $deelgebied->getId() ?>" 
            	style="font-size: 11px; color: #000000; height: 40px; text-align: center;">
            	<?php if(GOOGLE_MAPS_ENABLED) { ?>
            	Loading...
            	<?php } ?>
        	</td>
        </tr></table>
		<?php } ?>

    </table>
    
<?php
$footerOptions = array();
$footerOptions['includeHtml'] = false;
require_once BASE_DIR . 'footer.php';
?>
