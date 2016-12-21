<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();
$headerOptions = array();
$headerOptions['title'] = 'Fullscreen Kaart';
$headerOptions['includeBody'] = false;
require_once BASE_DIR . 'header.php'; ?>

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
	var team<?= $deelgebied->getId() ?> = '';
	var team<?= $deelgebied->getId() ?>_old = '';
	var team<?= $deelgebied->getId() ?>_location = '';
	var team<?= $deelgebied->getId() ?>_location_old = '';
	var team<?= $deelgebied->getId() ?>_time = '';
<?php }?>

	var rank = '';
	var rank_old = '';
	var msg = '';
	var msg_old = '';
	
	<?php if(GOOGLE_MAPS_ENABLED) { ?>
	var geocoder = new google.maps.Geocoder();
	<?php } ?>
	var first = 1;
	
	function getJotihuntData(){
		//Set a timeout for 15 seconds
		setTimeout("getJotihuntData()",15000);
			
		var randomnumber=Math.floor(Math.random()*10001);

		$.get('<?=BASE_URL?>spy.php?i=randomnumber', function(data) {
			
			//Hide current indicators
			$('#arrow_rank').css('visibility','hidden');
			$('#arrow_msg').css('visibility','hidden');

		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','hidden');
			//Save current data to old
			team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
			team<?= $deelgebied->getId() ?>_location_old = team<?= $deelgebied->getId() ?>_location;
		<?php }?>
		
			rank_old = rank;
			msg_old = msg;
			
			//Save new data
			var items = data.split("||||||");
			
			<?php
			$counter = 0;
			foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
				team_tmp = items[<?= $counter++ ?>];
				if (team_tmp) {
					team_arr = team_tmp.split("|||");
					if (team_arr) {
						team<?= $deelgebied->getId() ?> = team_arr[0];
						team<?= $deelgebied->getId() ?>_time = team_arr[1];
						team<?= $deelgebied->getId() ?>_location = team_arr[2];
						team<?= $deelgebied->getId() ?>_lat = team_arr[3];
						team<?= $deelgebied->getId() ?>_long = team_arr[4];
						team<?= $deelgebied->getId() ?>_lastHuntTime = team_arr[5];
					}
				}
			<?php }?>
			
			rank = items[6];
			
			var tmpmsg = items[7];
			msg = items[7];
			
			if (8 in items) {
				lastHunt = items[8];
			}
			
			// In case we have no clue
			if (!rank) {
				rank = '?';
			}
			
			//Fix old data if first
			if(first == 1){
				<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
					team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
				<?php }?>
				rank_old = rank;
				msg_old = msg;
			}

			
			//Display new data
			//Rank
			$('#rank').html('#'+rank);
			
			//Message
			if (typeof msg !== 'undefined') {
			    $('#msg').html(msg);
			}
			if (typeof lasthunt !== 'undefined') {
			    $('#msg').append('<br />Laatste hunt: <strong>' + lastHunt + '</strong>');
			}
			
			//Vossenteams
			
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#status_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>_old);
			$('#link_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>);
			<?php if(GOOGLE_MAPS_ENABLED) { ?>
			if(team<?= $deelgebied->getId() ?>_location == '0' || team<?= $deelgebied->getId() ?>_location == ''){
				if(team<?= $deelgebied->getId() ?>_lat != "0" && team<?= $deelgebied->getId() ?>_long != "0") {
					var latlng = new google.maps.LatLng(team<?= $deelgebied->getId() ?>_lat, team<?= $deelgebied->getId() ?>_long);
					geocoder.geocode({'latLng': latlng}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (results[0]) {
								$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+results[0].formatted_address+'</strong> ('+team<?= $deelgebied->getId() ?>_time+')');
							} else if (results[1]) {
								$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+results[1].formatted_address+'</strong> ('+team<?= $deelgebied->getId() ?>_time+')');
							}
						}
					});
				} else {
					$('#loc_<?= $deelgebied->getId() ?>').html("Geen locaties bekend");
				}
			}else{
				$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+team<?= $deelgebied->getId() ?>_location+'</strong> ('+team<?= $deelgebied->getId() ?>_time+')');
			}
			<?php } ?>
			
			if(typeof team<?= $deelgebied->getId() ?>_lastHuntTime !== 'undefined' && team<?= $deelgebied->getId() ?>_lastHuntTime != 0) {
				var newHuntTime<?= $deelgebied->getId() ?> = Date.parse(team<?= $deelgebied->getId() ?>_lastHuntTime+' GMT+0100') + (60 * 60) - (60*60); // + 1hour for hunt - 1hour for daylightsaving
				startCountdown($(".text<?= $deelgebied->getId() ?>"),newHuntTime<?= $deelgebied->getId() ?>/1000);
			}		
			<?php }?>
			
			if(first == 1) first = 0;
			
			//Compare and visualize data
			compareData();
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
			if(team<?= $deelgebied->getId() ?>_location_old != team<?= $deelgebied->getId() ?>_location) {
				if (document.getElementById("iframe_<?= $deelgebied->getId() ?>")) {
					document.getElementById("iframe_<?= $deelgebied->getId() ?>").src = 
						document.getElementById("iframe_<?= $deelgebied->getId() ?>").src;
				}
			}
		<?php } ?>
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			if(team<?= $deelgebied->getId() ?>_old != team<?= $deelgebied->getId() ?>){
				$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','visible');
			}
		<?php } ?>
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
</head>
<body>
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right; padding-right: 30px; font-size: 24px; font-weight: bold; color: #66665E;"><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 30px;" id="arrow_msg" /><span id="msg"></span></td>
            <td style="text-align:center; font-size: 60px; font-weight: bold; color: #66665E;"><span id="currentTime"></span></td>
            <td style="padding-left: 30px; text-align: left; font-size: 60px; color: #D19917; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);">
            	<img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 30px;" id="arrow_rank" />
            	<span id="rank"></span>
            	<a href="./">
	<?php
$r = rand(1, 1);
switch ($r) {
    case 1 :
        echo '<img src="' . BASE_URL . 'images/logos/jotihunt-asianfox.png" alt="Jotihunt Roothaan logo" 
        		style="height:80px;" />';
    break;
    /*case 2 :
        echo '<img src="' . BASE_URL . 'images/logos/2013/joti-hatter.png" alt="Jotihunt Mad Hatter logo" width="100px" style="float:left; border:0; margin: -10px 20px;position:absolute;" />';
    break;*/
}
?>
				</a>
			</td>
        </tr>
	</table>
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
            <table style="width: 33%; display: inline-table;">
            <tr>
            <td
            	id="status_<?= $deelgebied->getId() ?>"
            	class="wit"
            	style="height: 40px; text-align: left; color: #000000; font-size: 10px;">
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
            		height="100%">
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