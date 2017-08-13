<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();

require_once BASE_DIR . 'includes/make_map_js.include.php';
require_once CLASS_DIR . 'jotihunt/MapOptions.class.php';

$headerOptions = array();
$headerOptions['title'] = 'Fullscreen Kaart';
$headerOptions['includeBody'] = false;
require_once BASE_DIR . 'header.php'; ?>

<?php if(GOOGLE_MAPS_ENABLED) { ?>
<script type="text/javascript">
var markersArray = [];
var infowindow;	
var vossen = 0;
function clearOverlays() {
  for (var i = 0; i < markersArray.length; i++ ) {
	markersArray[i].setMap(null);
  }
}

function fetchVossen(){
		//Set a timeout for 15 seconds
		setTimeout("fetchVossen()",15000);
			
		updateVossen('');
		updateHunters();	
	}
	$(document).ready(function(){
		setTimeout("fetchVossen()",5000);
	});
</script>
    <div id="map" style="float: left; width: 100%; height: 100%;"></div>
<?php
$mapOptions = new MapOptions();
$mapOptions->special = true;
$mapOptions->team = '';
$mapOptions->zoom = 10;
make_map($mapOptions);

?>
<?php } else {?>
<em>Configureer <strong><code>google-js-api-key</code></strong> om Google Maps te gebruiken</em>
<?php } ?>