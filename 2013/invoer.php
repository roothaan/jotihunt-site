<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

function getHintInstuurTijd() {
    //vossenteams sturen hun locatie om 10 voor het hele uur op
    //meestal verschijnen ze rond kwart over het hele uur op de site
    $date = new DateTime();
    $minutes = $date->format('i')+10;
    $date->modify('-'.$minutes.' minutes');
    return $date;
}

$hint_instuur_tijd = getHintInstuurTijd();

JotihuntUtils::requireLogin();

//fetch laatste bericht
$bericht = $driver->getLastBerichtByType("hint");
if($bericht){
    $iframeUrl = "https://jotihunt.net/groep/hint.php?MID=".$bericht->getBericht_id();
}else{
    $iframeUrl = "https://jotihunt.net";
}

//fetch vossen locaties voor placeholder
$deelgebieden = $driver->getAllDeelgebieden();
$deelgebieden_locs = array();
foreach($deelgebieden as $deelgebied){
    $vos = $driver->getVosIncludingLocations($deelgebied->getName());
    $vos_locations = array();
    if ($vos) {
        $vos_locations = $vos->getLocations();
    }
    if (count($vos_locations) > 0) {
        $vos_locatie = $vos_locations [0];
        $vos_x = (string)$vos_locatie->getX();
        $vos_y = (string)$vos_locatie->getY();
    }else{
        $vos_x = "00000";
        $vos_y = "00000";
    }
    if(strlen($vos_x) > 5) {
        $vos_x = substr($vos_x,0,5);
        $vos_y = substr($vos_y,0,5);
    }
    $deelgebieden_locs[$deelgebied->getId()] = array($vos_x, $vos_y);
}
?>
<script type="text/javascript" src="<?= GOOGLE_MAPS_URI ?>"></script>
<script type="text/javascript" src="<?=BASE_URL?>js/vossenPage.js"></script>
<script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    
    function getLatLong(x,y){
    	while(parseInt(x) < 100000){ x = x*10; }
    	while(parseInt(y) < 100000){ y = y*10; }
    	var f; var l;
    
    	x0  = 155000.000;
    	y0  = 463000.000;
    
    	f0 = 52.156160556;
    	l0 =  5.387638889;
    
    	a01=3236.0331637 ; b10=5261.3028966;
    	a20= -32.5915821 ; b11= 105.9780241;
    	a02=  -0.2472814 ; b12=   2.4576469;
    	a21=  -0.8501341 ; b30=  -0.8192156;
    	a03=  -0.0655238 ; b31=  -0.0560092;
    	a22=  -0.0171137 ; b13=   0.0560089;
    	a40=   0.0052771 ; b32=  -0.0025614;
    	a23=  -0.0003859 ; b14=   0.0012770;
    	a41=   0.0003314 ; b50=   0.0002574;
    	a04=   0.0000371 ; b33=  -0.0000973;
    	a42=   0.0000143 ; b51=   0.0000293;
    	a24=  -0.0000090 ; b15=   0.0000291;
    
    	with(Math){
    	dx=(x-x0)*pow(10,-5);
    	dy=(y-y0)*pow(10,-5);
    
    	df =a01*dy + a20*pow(dx,2) + a02*pow(dy,2) + a21*pow(dx,2)*dy + a03*pow(dy,3)
    	df+=a40*pow(dx,4) + a22*pow(dx,2)*pow(dy,2) + a04*pow(dy,4) + a41*pow(dx,4)*dy
    	df+=a23*pow(dx,2)*pow(dy,3) + a42*pow(dx,4)*pow(dy,2) + a24*pow(dx,2)*pow(dy,4);
    	f = f0 + df/3600;
    
    	dl =b10*dx +b11*dx*dy +b30*pow(dx,3) + b12*dx*pow(dy,2) + b31*pow(dx,3)*dy;
    	dl+=b13*dx*pow(dy,3)+b50*pow(dx,5) + b32*pow(dx,3)*pow(dy,2) + b14*dx*pow(dy,4);
    	dl+=b51*pow(dx,5)*dy +b33*pow(dx,3)*pow(dy,3) + b15*dx*pow(dy,5);
    	l = l0 + dl/3600};
    	
    	var ll = new Array();
    	ll["latitude"] = f;
    	ll["longitude"] = l;
    	return ll;
    }
    
    function setAddress(lat,long,element){
        var latlng = new google.maps.LatLng(lat, long);
    	geocoder.geocode({'latLng': latlng}, function(results, status) {
    		if (status == google.maps.GeocoderStatus.OK) {
    			if (results[0]) {
    			    $(element).val(results[0].formatted_address);
    			}else if (results[1]) {
    			    $(element).val(results[1].formatted_address);
    			    document.getElementById("adres").value = results[1].formatted_address;
    			}
    		}
    	});
    }
    function logMessage(msg) {
        $('.invoerResponse').html(msg+'<br />'+$('.invoerResponse').html());
    }

    var teams = {
        <?php foreach($deelgebieden as $deelgebied){ ?>
        <?= $deelgebied->getId() ?>: false,
        <?php } ?>
    };
    var teamNames = {
        <?php foreach($deelgebieden as $deelgebied){ ?>
        <?= $deelgebied->getId() ?>: <?= json_encode($deelgebied->getName()) ?>,
        <?php } ?>
    };

    $(document).ready(function(){
        $(".datepicker").datepicker();
        $('.timepicker').timepicker();
    
        $('.invoerRD').focusout(function(){
            $('.save').addClass('disabled');
            var team = parseInt($(this).attr('name'));
            $('.item'+team).removeClass('success').removeClass('fail');
            var x = $('input[name="'+team+'x"]').val();
            var y = $('input[name="'+team+'y"]').val();
            if(x && x.length == 5 && x > 12000 && x < 26000 && y && y.length == 5 && y > 41000 && y < 51000){
                $('.item'+team).addClass('success');
                var fl = getLatLong(x,y);
                var f = fl["latitude"];
                var l = fl["longitude"];
                $('input[name="'+team+'f"]').val(f);
                $('input[name="'+team+'l"]').val(l);
                var el = 'input[name="'+team+'a"]';
                setAddress(f,l,el);
            }else{
                $('.item'+team).addClass('fail');
                $('input[name="'+team+'f"]').val('');
                $('input[name="'+team+'l"]').val('');
                $('input[name="'+team+'a"]').val('');
            }
        });
        
        $('.check').click(function(){
            $('.save').addClass('disabled');
            $('.invoerItem').removeClass('blue');
            //check if values are correct
            jQuery.each(teams, function(team, value){
                var x = $('input[name="'+team+'x"]').val();
                var y = $('input[name="'+team+'y"]').val();
                var f = $('input[name="'+team+'f"]').val();
                var l = $('input[name="'+team+'l"]').val();
                var a = $('input[name="'+team+'a"]').val();
                if(x && x.length == 5 && x > 12000 && x < 26000 && y && y.length == 5 && y > 41000 && y < 51000 && f && l && a){
                    $('.item'+team).removeClass('fail').addClass('success');
                    $('.save').removeClass('disabled');
                    x = x*10;
                    y = y*10;
                    teams[team] = new Array(x,y,f,l,a);
                }else{
                    $('.item'+team).removeClass('success').addClass('fail');
                    teams[team] = false;
                }
            });
        });
        
        $('.save').click(function(){
            if($(this).hasClass('disabled')) {
                console.log("nu-uh!");
            }else{
                logMessage('De volgende deelgebieden worden opgeslagen:');
                $('.save').addClass('disabled');
                var msg = '';
                var count = 0;
            
                //post correct values
                jQuery.each(teams, function(index, value){
                    team = index.toLowerCase();
                    if(value !== false) {
                        logMessage('team '+teamNames[index]);
                        count++;
                        var x = value[0];
                        var y = value[1];
                        var f = value[2];
                        var l = value[3];
                        var adres = value[4];
                        var startdatum = $('input[name="startdatum"]').val();
                        var starttijd = $('input[name="starttijd"]').val();
                        $.ajax({
    					    url: "<?=BASE_URL?>ajax/vos.ajax.php",
    					    type: "POST",
    					    data: {
    					        team: index,
    					        x: x,
    					        y: y,
    					        f: f,
    					        l: l,
    					        adres: adres,
    					        startdatum: startdatum,
    					        starttijd: starttijd
    					    }
    					}).done(function(data){
					        if(data == "succes"){ 
					            logMessage('team '+teamNames[index]+' succesvol ingevoerd');
					            $('.item'+index).addClass('blue');
					        }else{
					            logMessage('team '+teamNames[index]+' error tijdens invoeren: '+data);
					        }
					    });
                    }
                });
                logMessage(count+' locaties ingevoerd');
            }
        });
    });
</script>
<div class="invoerLeft">
    <h1>Vossenlocaties invoeren</h1>
    Via dit formulier kun je vossenlocaties voor alle deelgebied tegelijk invoeren. Indien je Chrome met de <a href="https://chrome.google.com/webstore/detail/roothaan-jotihunt-helper/ofgofkabgpfgoemeenoknhkjeefgofia" target="_blank">RoothaanJotihuntHelper-extensie</a> gebruikt, kun je de locaties die je hieronder hebt ingevoerd met één druk op de knop in het hint-inleverformulier zetten. 
    <hr id="hrheader">
    <div class="invoerForm">
        <div class="invoerItem tijd">
            <div class="invoerInputLabel">Tijd:     </div>
            <input type="text" name="startdatum" autocomplete="off" value="<?=$hint_instuur_tijd->format('d-m-Y')?>" class="invoerTijd datepicker" />
            <input type="text" name="starttijd" autocomplete="off" value="<?=$hint_instuur_tijd->format('H:i')?>" class="invoerTijd timepicker" /> 
            <small>(Vossenlocaties worden om xx:50 ingestuurd)</small>
        </div>
<?php foreach($deelgebieden as $deelgebied){ ?>
        <div class="invoerItem item<?= $deelgebied->getId() ?>">
            <div class="inner">
                <div class="invoerInputLabel"><?= $deelgebied->getName() ?>:</div>
                <input class="invoerRD" id="invoer<?= $deelgebied->getName() ?>x" type="text" name="<?= $deelgebied->getId() ?>x" autocomplete="off" placeholder="<?= $deelgebieden_locs[$deelgebied->getId()][0] ?>" maxlength="5" />
                <input class="invoerRD" id="invoer<?= $deelgebied->getName() ?>y" type="text" name="<?= $deelgebied->getId() ?>y" autocomplete="off" placeholder="<?= $deelgebieden_locs[$deelgebied->getId()][1] ?>" maxlength="5" />
            </div>
            <div class="invoerHidden">
                <input class="invoerLL" type="text" name="<?= $deelgebied->getId() ?>f" autocomplete="off" placeholder="lat" disabled="disabled" />
                <input class="invoerLL" type="text" name="<?= $deelgebied->getId() ?>l" autocomplete="off" placeholder="long" disabled="disabled" />
                <input class="invoerAdres" type="text" name="<?= $deelgebied->getId() ?>a" autocomplete="off" placeholder="adres" disabled="disabled" />
            </div>
        </div>

<?php } ?>
    </div>
    <div class="clear"></div>
    <div class="invoerSaveButtons"><div class="inner"><a href="javascript:void(0);" class="check">Check</a><a href="javascript:void(0);" class="save disabled">Save</a></div><div class="invoerTransferButton"><a href="javascript:void(0);">Kopieer ></a></div></div>
    <div class="invoerResponse"></div>
</div>
<div class="invoerRight">
    <iframe src="<?= $iframeUrl ?>" width="100%" height="480" style="border: 2px solid black;" id="jotihuntFrame"></iframe>
</div>
<div class="clear"></div>