<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin(); ?>

<script type="text/javascript">
var custom = 0;
var me;
var me2;
var me3;
var me4;

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
var hunt = '';
var hunt_old = '';

var first = 1;


function getJotihuntData(){
	//Set a timeout for 15 seconds
	setTimeout("getJotihuntData()",15000);
		
	var randomnumber=Math.floor(Math.random()*10001);
	$('#refreshicon').show();
	$.get('<?=BASE_URL?>spy.php?i='+randomnumber, function(data) {
		
		//Hide current indicators
		$('#arrow_rank').css('visibility','hidden');
		$('#arrow_msg').css('visibility','hidden');
		$('#arrow_hunt').css('visibility','hidden');

		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','hidden');
			//Save current data to old
			team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
			team<?= $deelgebied->getId() ?>_location_old = team<?= $deelgebied->getId() ?>_location;
		<?php }?>
		
		rank_old = rank;
		msg_old = msg;
		hunt_old = hunt;
		
		//Set latest update-timestamp
		var currentDate = new Date();
		var hours = currentDate.getHours();
		var minutes = currentDate.getMinutes();
		if(hours < 10) hours = '0'+hours;
		if(minutes < 10) minutes = '0'+minutes;
		$('#timestamp').html('Laatste update: ' +hours+':'+minutes);
		
		//Save new data
		var items = data.split("||||||");

		<?php
		$counter = 0;
		foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			team_tmp = items[<?= $counter++ ?>];
			team_arr = team_tmp.split("|||");
			team<?= $deelgebied->getId() ?> = team_arr[0];
			team<?= $deelgebied->getId() ?>_time = team_arr[1];
			team<?= $deelgebied->getId() ?>_location = team_arr[2];
		<?php }?>

		rank = items[6];
		msg = items[7];
		hunt = items[8];
		
		//Fix old data if first
		if(first == 1){
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			team<?= $deelgebied->getId() ?>_old = team<?= $deelgebied->getId() ?>;
		<?php }?>
			rank_old = rank;
			msg_old = msg;
			hunt_old = hunt;
			first = 0;
		}
		
		//Display new data
		//Rank
		if (!rank) {
			rank = '?';
		}
		$('#rank').html('#'+rank);
		
		//Message
		$('#msgcontainer').html(msg);
		
		// Hunt
		$('#huntcontainer').html(hunt);
		
		//Vossenteams

		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			$('#status_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>_old);
			$('#link_<?= $deelgebied->getId() ?>').attr('class',team<?= $deelgebied->getId() ?>);
			if(team<?= $deelgebied->getId() ?>_location == '0'){
				$('#loc_<?= $deelgebied->getId() ?>').html('Geen locaties bekend');
			}else{
				$('#loc_<?= $deelgebied->getId() ?>').html('<strong>'+team<?= $deelgebied->getId() ?>_location+'</strong> ('+team<?= $deelgebied->getId() ?>_time+')');
			}
		<?php }?>

		//Hide preloader animation
		$('#refreshicon').hide();
		
		//Compare and visualize data
		compareData();
	});
}

function compareData(){		
	if(msg != msg_old){
		$('#arrow_msg').css('visibility','visible');
		jwplayer('player1').play();
	}
	
	if(parseInt(rank) < parseInt(rank_old)){ 
		$('#arrow_rank').css('visibility','visible');
		jwplayer('player2').play();
	}else if(parseInt(rank) > parseInt(rank_old)){
		$('#arrow_rank').css('visibility','visible');
		jwplayer('player3').play();
	}
	
	var voschange = 0;
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
			if(team<?= $deelgebied->getId() ?>_old != team<?= $deelgebied->getId() ?>){
				voschange = 1;
				$('#arrow_<?= $deelgebied->getId() ?>').css('visibility','visible');
			}
		<?php }?>

	if(voschange == 1){
		jwplayer('player4').play();
	}
	
	if(hunt != hunt_old){
		$('#arrow_hunt').css('visibility','visible');
		jwplayer('player5').play();
	}
}

function hidePlayers(){
	if(custom == 0) $('#audiocover').css('background','#FFFFFF');
}
</script>
<div id="voscontainer" class="alarmPaginaVossenContainer" style="float: left; display: inline-block; width: 450px;">
    <table style="width: 450px;">
		<?php foreach ($driver->getAllDeelgebieden() as $deelgebied) { ?>
        <tr>
            <td style="width: 50px;"><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 40px;" id="arrow_<?= $deelgebied->getId() ?>" /></td>
            <td id="status_<?= $deelgebied->getId() ?>" class="wit" style="width: 150px; text-align: center; color: #000000; font-size: 10px;"><a href="<?=WEBSITE_URL?>vossen/<?= $deelgebied->getName() ?>" id="link_<?= $deelgebied->getId() ?>" class="wit" style="display: block; width: 100%; height: 100%; text-decoration: none; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);"> Team <br />
                <span class="header headerh1"><?= $deelgebied->getName() ?></span>
            </a></td>
            <td id="loc_<?= $deelgebied->getId() ?>" style="width: 250px; font-size: 11px; color: #000000; margin-left: 10px; overflow: hidden; height: 40px;"></td>
        </tr>
		<?php }?>
    </table>
</div>

<!--- Audio stuff below -->

<div id="contentcontainer" style="float: left; display: inline-block;">
    <table style="width: 450px">
        <tr>
            <td style="width: 300px; height: 100px; text-align: right;">Huidige notering:</td>
            <td style="width: 50px;"><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 40px;" id="arrow_rank" /></td>
            <td id="rank" style="width: 100px; font-size: 60px; color: #D19917; text-shadow: 0 1px 0 rgba(0, 0, 0, 0.2);"></td>
        </tr>
        <tr>
            <td><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 40px; margin-bottom: -10px;" id="arrow_msg" /> <strong>Laatste bericht:</strong></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" id="msgcontainer" style="overflow: hidden;"></td>
        </tr>
        <tr>
            <td><img src="<?=BASE_URL?>images/arrow-bouncing-left.gif" style="visibility: hidden; height: 40px; margin-bottom: -10px;" id="arrow_hunt" /> <strong>Laatste hunt:</strong></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" id="huntcontainer" style="overflow: hidden;"></td>
        </tr>
    </table>
</div>
<img src="<?=BASE_URL?>images/preloader.gif" style="position: absolute; margin: auto; z-index: 100; top: 190px; left: 50%;" id="refreshicon" />
<div id="centerheader" style="position: relative; clear: both; margin: 0px auto; width: 300px;">
    <small><span id="timestamp">Nog geen updates.</span> - Test geluid: <a href="javascript:void(0);" onclick="jwplayer('player1').play();" style="text-decoration: none;">1</a> <a href="javascript:void(0);" onclick="jwplayer('player2').play();" style="text-decoration: none;">2</a> <a href="javascript:void(0);" onclick="jwplayer('player3').play();" style="text-decoration: none;">3</a> <a href="javascript:void(0);" onclick="jwplayer('player4').play();" style="text-decoration: none;">4</a> <a href="javascript:void(0);" onclick="jwplayer('player5').play();" style="text-decoration: none;">5</a> - <a href="javascript:void(0);" onclick="$('#audiocover').css('background','none');custom = 1;" style="text-decoration: none;">show</a> - <a href="javascript:void(0);" onclick="$('#audiocover').css('background','#FFFFFF');custom = 1;" style="text-decoration: none;">hide</a></small>
</div>
<div id="audioplayers" style="position: absolute; left: 0px; top: 40%; width: 50px; height: 250px; padding: 10px;">
    <span id="audiocover" style="float: left; position: absolute; width: 50px; height: 250px; z-index: 100; background: none;"> </span>
    <audio id="player1" src="<?=BASE_URL?>sounds/firetrucksiren.mp3" controls="controls"></audio>
    <a href="<?=BASE_URL?>sounds/firetrucksiren.mp3" target="_blank">mp3</a><br /> <br />
    <audio id="player2" src="<?=BASE_URL?>sounds/smb_stage_clear.mp3" controls="controls"></audio>
    <a href="<?=BASE_URL?>sounds/smb_stage_clear.mp3" target="_blank">mp3</a><br /> <br />
    <audio id="player3" src="<?=BASE_URL?>sounds/smb_mariodie.mp3" controls="controls"></audio>
    <a href="<?=BASE_URL?>sounds/smb_mariodie.mp3" target="_blank">mp3</a><br /> <br />
    <audio id="player4" src="<?=BASE_URL?>sounds/siren.mp3" controls="controls"></audio>
    <a href="<?=BASE_URL?>sounds/siren.mp3" target="_blank">mp3</a>
    <audio id="player5" src="<?=BASE_URL?>sounds/wegothim.mp3" controls="controls"></audio>
    <a href="<?=BASE_URL?>sounds/wegothim.mp3" target="_blank">mp3</a>
</div>

<script type="text/javascript">
$(document).ready(function(){
	jwplayer("player1").setup({ width: '24', height: '24', flashplayer: "<?=BASE_URL?>/jwplayer/player.swf" });
	jwplayer("player2").setup({ width: '24', height: '24', flashplayer: "<?=BASE_URL?>/jwplayer/player.swf" });
	jwplayer("player3").setup({ width: '24', height: '24', flashplayer: "<?=BASE_URL?>/jwplayer/player.swf" });
	jwplayer("player4").setup({ width: '24', height: '24', flashplayer: "<?=BASE_URL?>/jwplayer/player.swf" });
	jwplayer("player5").setup({ width: '24', height: '24', flashplayer: "<?=BASE_URL?>/jwplayer/player.swf" });
	setTimeout("hidePlayers()",3000);
	getJotihuntData();
});
</script>