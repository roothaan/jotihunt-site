<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

JotihuntUtils::requireLogin();
require_once CLASS_DIR . 'jotihunt/MapOptions.class.php';


$removeHunterAfter = 3600;
$staleHunterAfter = 1800;

function make_map($mapOptions) {
    global $driver;
    global $removeHunterAfter, $staleHunterAfter;

    global $authMgr;
    $organisation = $driver->getOrganisationById($authMgr->getMyOrganisationId());
    ?>

<?php if(GOOGLE_MAPS_ENABLED) { ?>
<script type="text/javascript" src="<?= GOOGLE_MAPS_URI ?>"></script>
<script type="text/javascript"> 
        var vossenmarkersArray = [];
        var huntermarkersArray = [];
        var vossenlinemarkersArray = [];
        
        function updateVossen(team) {
    		<?php if ($mapOptions->showVos) { ?>

    	    $.ajax({
                url: "<?= BASE_URL ?>getVossen.ajax.php",
                type: "POST",
                data: {
                    team: team
                },
                success: function(response) {
                    clearVossenOverlays();
                    
                    vossenLocatiesDeelgebieden = $.parseJSON(response);
                    for (var deelgebiedName in vossenLocatiesDeelgebieden) { // alle deelgebieden
                       var vossenLocaties = vossenLocatiesDeelgebieden[deelgebiedName];
                       
                       for (var prop in vossenLocaties) { // alle locaties
                          if(vossenLocaties.hasOwnProperty(prop)){
                              addVosLocatie(
                                  map, 
                                  vossenLocaties[prop].type, 
                                  vossenLocaties[prop].new_coord, 
                                  vossenLocaties[prop].adres, 
                                  vossenLocaties[prop].last_in_line, 
                                  vossenLocaties[prop].old_coord, 
                                  vossenLocaties[prop].naam, 
                                  vossenLocaties[prop].formatted_datetime, 
                                  vossenLocaties[prop].active_counterhuntrondje_id, 
                                  vossenLocaties[prop].locatie_counterhuntrondje_id);

                          }
                       }
                    }
                    
                    <?php if (isset($mapOptions->team) && !empty($mapOptions->team) && !$mapOptions->centerOnCrosshair) { ?>
                    if (vossenLocatiesDeelgebieden['<?= $mapOptions->team ?>'] && vossenLocatiesDeelgebieden['<?= $mapOptions->team ?>'][0]) { 
                        coord_array = vossenLocatiesDeelgebieden['<?= $mapOptions->team ?>'][0].new_coord.split(',');
                        map.setZoom(13);
                        map.panTo(new google.maps.LatLng(coord_array[0],coord_array[1]));
                    }
                    <?php } ?>
                }
            });
    	    
    	    <?php } ?>
        }
        
        function updateHunters() {
    		<?php if ($mapOptions->showHuntersLastLocation) { ?>
    	    
    	    $.ajax({
                url: "<?= BASE_URL;?>getHunter.ajax.php",
                type: "POST",
                data: {},
                success: function(response) {
                    
                    clearHunterOverlays();
                    
                    hunterLocatiesDeelgebieden = $.parseJSON(response);
                    for (var key in hunterLocatiesDeelgebieden) { // alle deelgebieden
                       var obj = hunterLocatiesDeelgebieden[key];
                       
                       addHunter(
                           map,
                           hunterLocatiesDeelgebieden[key].latitude,
                           hunterLocatiesDeelgebieden[key].longitude,
                           hunterLocatiesDeelgebieden[key].naam, 
                           hunterLocatiesDeelgebieden[key].formatted_date, 
                           hunterLocatiesDeelgebieden[key].tel,
                           hunterLocatiesDeelgebieden[key].stale,
                           hunterLocatiesDeelgebieden[key].auto);
                    }
                }
            });
    	    
    	    <?php } ?>
        }
        
        function clearVossenOverlays() {
            
            for (var i = 0; i < vossenmarkersArray.length; i++ ) {
                vossenmarkersArray[i].setMap(null);
            }
            vossenmarkersArray = [];
        }
        
        function clearHunterOverlays() {
            for (var i = 0; i < huntermarkersArray.length; i++ ) {
                huntermarkersArray[i].setMap(null);
            }
            huntermarkersArray = [];
        }
        
        var infowindow = new google.maps.InfoWindow();
		infowindow.setZIndex(1000);
    	<?php
    if ($mapOptions->special === true) {
        ?>
    	    var winW = 630, winH = 460;
    	    if (document.body && document.body.offsetWidth) {
    	        winH = document.body.offsetHeight;
    	    }
        	if (document.compatMode=='CSS1Compat' &&
        		document.documentElement &&
        		document.documentElement.offsetWidth ) {
    	            winH = document.documentElement.offsetHeight;
    	    }
        	if (window.innerHeight) {
        	 winH = window.innerHeight;
        	}
        	var minlat = 51.787072714802065;
        	var maxlat = 52.39602534138044;
        	var minlng = 5.217498689889908;
        	var maxlng = 6.468519258467154;
        	var ctrlng = 5.843008974178531;
        	var ctrlat = 52.086414543226855;
        	var mapdisplay = winH;
        	var interval = 0;
        
        	if ((maxlat - minlat) > (maxlng - minlng)) {
            	interval = (maxlat - minlat) / 2;
            	minlng = ctrlng - interval;
            	maxlng = ctrlng + interval;
    	    } else {
            	interval = (maxlng - minlng) / 2;
            	minlat = ctrlat - interval;
            	maxlat = ctrlat + interval;
    	    }
    
        	var dist = (6371 * Math.acos(Math.sin(minlat / 57.2958) * Math.sin(maxlat / 57.2958) + (Math.cos(minlat / 57.2958) * Math.cos(maxlat / 57.2958) * Math.cos((maxlng / 57.2958) - (minlng / 57.2958)))));
        
        	var mapzoom = Math.floor(7 - Math.log(1.6446 * dist / Math.sqrt(2 * (mapdisplay * mapdisplay))) / Math.log (2));
    	    <?php
    } else {
        echo "var mapzoom = " . $mapOptions->zoom . ";";
    }
    ?>
    	
    	var a = [];
    	var b = [];
    	var i = 0;
    	var map; 
    	
    	function initialize() {
    		var myOptions = {
    			zoom: mapzoom,
    			center: new google.maps.LatLng(<?= JotihuntUtils::convert($mapOptions->x,$mapOptions->y); ?>),
    			mapTypeId: google.maps.MapTypeId.<?= $mapOptions->type; ?>,
    			scaleControl: true,
    		    mapTypeControl: true,
    		    mapTypeControlOptions: {
    		      style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
    		    },
    			navigationControl: true,
    			navigationControlOptions: {
    				style: google.maps.NavigationControlStyle.ZOOM_PAN
    			}
    		};
    		map = new google.maps.Map(document.getElementById("map"), myOptions);
    
            <?php if ($mapOptions->showPlayground) {
                $eventId = 0;
                if ($mapOptions->getEventId()) {
                    $eventId = $mapOptions->getEventId();
                }
    		    ?>
    		    //Gebieden
    		    var kmlLayer = drawDeelgebieden(map, <?= $eventId ?>);

    		    //Letters op de kaart
    		    addLetters(map);
            <?php } ?>
    		
    		<?php if ($mapOptions->showGroups) { ?>
    		    //POIs op de kaart
    		    addPois(map);
    		<?php } ?>
            
    		//Vossen
    		<?php if ($mapOptions->showVos) { ?>
    	    addVossen(map);
    	    <?php } ?>
    	    
    	    <?php if ($mapOptions->showHuntersLastLocation) { ?>
    	    addHunters(map);
    	    <?php } ?>

    		<?php if ($mapOptions->showHunter) { ?>
    		    // Voeg hunters toe
    	        addAllHuntersByKml(map);
    	    <?php } ?>
    	    
    		<?php
            if ($mapOptions->crosshair == true) {
                echo "var marker_center = new google.maps.Marker({position: new google.maps.LatLng(" . JotihuntUtils::convert($mapOptions->x, $mapOptions->y) . "), map: map, title:\"\", icon: new google.maps.MarkerImage(\"" . BASE_URL . "images/crosshair.png\",null,null,new google.maps.Point(10,10)), zIndex:1000 });";
            }
            if ($mapOptions->marker_x && $mapOptions->marker_y) {
                echo "var checkMarker = new google.maps.Marker({position: new google.maps.LatLng(" . JotihuntUtils::convert($mapOptions->marker_x, $mapOptions->marker_y) . "), map: map, title:\"".$mapOptions->marker_x.",".$mapOptions->marker_y."\", zIndex:1000 });";
                if($mapOptions->centerOnCrosshair) { ?>
                console.log("<?= JotihuntUtils::convert($mapOptions->marker_x, $mapOptions->marker_y) ?>");
                map.setCenter(new google.maps.LatLng(<?= JotihuntUtils::convert($mapOptions->marker_x, $mapOptions->marker_y) ?>));
                google.maps.event.addListener(checkMarker, "click", function() { 
			    var info_text = "<b>Marker locatie</b><br /><?= substr($mapOptions->marker_x,0,5) . "-" . substr($mapOptions->marker_y,0,5) ?>";
			    infowindow.setContent("<div style=\"width:200px; height:100px\">"+info_text+"</div>"); 
                infowindow.open(map,this); 
            });
                <?php
                }
            }
            ?>
    	}
    	initialize();
    	
    	<?php if ($mapOptions->showHuntersLastLocation) { ?>
	    function addHunter(map, latitude, longitude, naam, formatted_time, tel_nr, stale, auto) {
	        var img = "<?= BASE_URL;?>images/hunter_small.png";
	        
	        if(auto !== "" && auto != undefined) {
	            var img = "<?= BASE_URL."images/cars/small/";?>"+auto;
	        }
	        
	        if (stale == 'true') {
	            img = "<?= BASE_URL;?>images/hunter_small_stale.png";
	        }
			
			/*
			* @todo: icons netjes de juiste grootte geven met onderstaande code.
			var image = {
                url: img,
                // This marker is 20 pixels wide by 32 pixels tall.
                size: new google.maps.Size(, 32),
                // The origin for this image is 0,0.
                origin: new google.maps.Point(0,0),
                // The anchor for this image is the base of the flagpole at 0,32.
                anchor: new google.maps.Point(10, 10)
            };
            
            var image = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };
            */
			
			var marker_hunter = new google.maps.Marker({
			    position: new google.maps.LatLng(latitude,longitude), 
			    map: map, 
			    title:"", 
			    html: "Wordt nog vervangen", 
			    icon: new google.maps.MarkerImage(img,null,null,new google.maps.Point(10,10)), 
			    zIndex:50
		    }); 
			huntermarkersArray.push(marker_hunter);
			google.maps.event.addListener(marker_hunter, "click", function() { 
			    var info_text = "<b>"+naam+"</b><br />";
			    if (stale == 'true') {
			        info_text = "<b>"+naam+"</b> (oud!)<br />";
			    }
			    
			    info_text += formatted_time+"<br />";
			    info_text += "Tel: "+tel_nr+"<br />";
			    
			    infowindow.setContent("<div style=\"width:200px; height:100px\">"+info_text+"</div>"); 
                infowindow.open(map,this); 
            });
	    }
	    
	    function addHunters(map) {

    	    <?php
        $hunterlocationcollection = $driver->getLastRiderLocations();
        $i = 0;
        foreach ( $hunterlocationcollection as $riderlocation ) {
            $timeLastLocation = strtotime($riderlocation->getTime());
            $currentTime = time();
            $diff = $currentTime - $timeLastLocation;
            if ($diff > $removeHunterAfter) {
                continue;
            }
            $hunter = $driver->getRider($riderlocation->getRiderId());
            if (null == $hunter) {
                continue;
            }
            
            ?>
			    addHunter(
			        map, 
			        <?= $riderlocation->getLatitude(); ?>, 
			        <?= $riderlocation->getLongitude(); ?>, 
			        "<?= $hunter->getUser()->getDisplayName(); ?>", 
			        "<?= strftime('%R, %a, %d %b',strtotime($riderlocation->getTime())); ?>", 
			        "<?= $hunter->getTel(); ?>",
			        "<?= ($diff > $staleHunterAfter ? 'true' : 'false'); ?>",
			        "<?= $hunter->getAuto(); ?>");
			    <?php
            $i++;
        }
        ?>
    	}
	    <?php } // Ends "showHuntersLastLocation" ?>
    	
    	<?php if ($mapOptions->showHunter) { ?>
        function addAllHuntersByKml(map) {

            <?php
            // This is a hack needed to make Google Maps (it needs the protocol, "//" doesn't work)
        $protocol = 'https:';
        $baseUrl = $protocol . WEBSITE_URL . 'kml?riderId=';
        $baseUrl .= $mapOptions->hunter;
        $baseUrl .= '&sessionId=' . urlencode($authMgr->getSessionId());
        
        if (null != $mapOptions->hunterFrom) {
            $baseUrl .= '&from=' . intval($mapOptions->hunterFrom);
        }
        if (null != $mapOptions->hunterFrom) {
            $baseUrl .= '&to=' . intval($mapOptions->hunterTo);
        }
        ?>
            // By KML
            var ctaLayer = new google.maps.KmlLayer({
                url: '<?= $baseUrl; ?>',
                map: map
            });
            google.maps.event.addListener(ctaLayer, 'status_changed', function () {
                if (ctaLayer.getStatus() != 'OK') {
                    console.log('[' + ctaLayer.getStatus() + '] Google Maps could not load the layer. Please try again later.');
                    console.log(ctaLayer);
                }
            });
        }

	    <?php } // Ends "showHunter" ?>
    	
    	<?php include BASE_DIR . 'includes/map_deelgebieden.js'; ?>
    	
    	function addLetters(map) {
        	<?php include BASE_DIR . 'kml/deelgebieden_markers.js.php'; ?>
        	for (var areaName in areaCenters) {
        	    var point = areaCenters[areaName];
        	    var firstLetter = areaName.charAt(0).toLowerCase();
        	    var markerImage = "<?= BASE_URL; ?>images/maps-vossen-"+firstLetter+".png";
        	    var cm = new google.maps.Marker({
        			position: new google.maps.LatLng(point.y,point.x),
        			map: map,
        			title: '',
        			icon: new google.maps.MarkerImage(markerImage, null, null, new google.maps.Point(14, 14))
        		});
            }
    	}
    	
    	function addHomebaseCirkel(map, position) {
    		var circleHomebase = new google.maps.Circle({
    		    map: map,
    			center: position,
    			radius: 500,
    			strokeColor: "#FFFFFF",
    			strokeOpacity: 0.8,
    			strokeWeight: 2,
    			fillColor: "#FFFFFF",
    			fillOpacity: 0.25
    		});
    	}
    	
    	function addVosLocatie(map, type, location_coordinaat, adres, last_in_line, prev_location_coordinaat, vos_naam, vos_formatted_datetime, active_counterhuntrondje_id, locatie_counterhuntrondje_id) {
    	    var location_coordinaat_array = location_coordinaat.split(",");
    	    var location_coordinaat_x = location_coordinaat_array[0];
    	    var location_coordinaat_y = location_coordinaat_array[1];
    	    
    	    var img_extra = "";
    	    if(active_counterhuntrondje_id != locatie_counterhuntrondje_id) {
    	        var img_extra = "_old";
            } else if(last_in_line && last_in_line != 'false') {
    	        var img_extra = "_active";
    	    }
    	    switch(type) {
                case 'hunt':
                    var img = "<?= BASE_URL; ?>images/crosshair"+img_extra+".png";
                    break;
                case 'spot':
                    var img = "<?= BASE_URL; ?>images/eye"+img_extra+".png";
                    break;
                default:
                    var img = "<?= BASE_URL; ?>images/vos"+img_extra+".png";
                    break;
            }
    	    
    	    if(prev_location_coordinaat != '') {
        	    var prev_location_coordinaat_array = prev_location_coordinaat.split(",");
        	    var prev_location_coordinaat_x = prev_location_coordinaat_array[0];
        	    var prev_location_coordinaat_y = prev_location_coordinaat_array[1];
        	    
    	        var line = [new google.maps.LatLng(prev_location_coordinaat_x,prev_location_coordinaat_y), new google.maps.LatLng(location_coordinaat_x,location_coordinaat_y)]; 
    	        if(active_counterhuntrondje_id == locatie_counterhuntrondje_id) {
        	        var path = new google.maps.Polyline({ 
        	            path: line, 
        	            strokeColor: "#FF0000", 
        	            strokeOpacity: 0.7, 
        	            strokeWeight: 2
    	            }); 
    	        } else {
    	            var path = new google.maps.Polyline({ 
        	            path: line, 
        	            strokeColor: "#676767", 
        	            strokeOpacity: 0.4, 
        	            strokeWeight: 2
    	            }); 
    	        }
	            path.setMap(map);			
    	    }
    		
    		var marker_vos = new google.maps.Marker({
    		    position: new google.maps.LatLng(location_coordinaat_x,location_coordinaat_y), 
    		    map: map, 
    		    html: "Wordt overschreven",
    		    title:"", 
    		    icon: new google.maps.MarkerImage(img,null,null,new google.maps.Point(10,10)), 
    		    zIndex:50
		    });
    				
    		vossenmarkersArray.push(marker_vos);
			vossenlinemarkersArray.push(line);
			vossenlinemarkersArray.push(path);
			
			google.maps.event.addListener(marker_vos, "click", function() { 
			    var infotext = "<b>Vossenteam "+vos_naam+"</b><br />";
			    infotext += vos_formatted_datetime+"<br />";
			    infotext += adres+"<br />";
			    
			    infowindow.setContent("<div>"+infotext+"</div>"); 
			    infowindow.open(map,this); 
		    });
    	}
    	
    	<?php if ($mapOptions->showVos) { ?>
    	function addVossen(map) {
    	    <?php

        function drawVossenByTeam($deelgebiedName) {
            global $driver;
            
            $old_coord = "";
            $vos_locations = array();
            $vos = $driver->getVosIncludingLocations($deelgebiedName);
            if ($vos) {
                $vos_locations = $vos->getLocations();
            }
            
            if (! empty($vos_locations)) {
                $i = 0;
                foreach ( $vos_locations as $location ) {
                    if ($i === 0) {
                        $last_in_line = 'true';
                    } else {
                        $last_in_line = 'false';
                    }
                    
                    $new_coord = JotihuntUtils::convert($location->getX(), $location->getY());
                    $formatted_datetime = strftime('%a, %d %b %R', strtotime($location->getDate()));
                    $counterhuntrondjeId = 0;
                    $counterhuntrondje = $driver->getActiveCounterhuntRondje($deelgebiedName);
                    if ($counterhuntrondje) {
                        $counterhuntrondjeId = $counterhuntrondje->getId();
                    }
                    $riderLine = '';
                    if ($location->getRiderId() > 0) {
                        $riderUser = $driver->getRider($location->getRiderId())->getUser();
                        if ($riderUser) {
                            $riderLine = '<br /><br />Gehunt door <strong>' . $riderUser->getDisplayName() . '</strong>';
                        }
                    }
                    if ($location->getTypeName() == 'hunt' && empty($riderLine)) {
                        $riderLine = '<br /><br />Onbekende hunt of geen huntcode doorgegeven';
                    }
                    
                    $addressLine = $location->getAddress();
                    if (empty($addressLine)) {
                        $addressLine = 'Onbekend/leeg adres (of bug in de Jotihunt app)';
                    }
                    ?>
        			    
        			    addVosLocatie(
        			        map,
        			        "<?= $location->getTypeName() ?>", 
        			        "<?= $new_coord ?>", 
        			        "<?= $addressLine ?> <?= $riderLine ?>", 
        			        <?= $last_in_line ?>, 
        			        "<?= $old_coord ?>", 
        			        "<?= $vos->getName() ?>", 
        			        "<?= $formatted_datetime ?>", 
        			        <?= $counterhuntrondjeId ?>, 
        			        <?= $location->getCounterhuntrondjeId() ?>);
        				
        				<?php
                    $old_coord = $new_coord;
                    $i ++;
                }
                
                if (isset($mapOptions->team) && ! empty($mapOptions->team)) {
                    ?>
                    if(vossenLocatiesDeelgebieden['<?= strtolower($mapOptions->team);?>'] && vossenLocatiesDeelgebieden['<?= strtolower($mapOptions->team);?>'][0]) {
                        coord_array = vossenLocatiesDeelgebieden['<?= strtolower($mapOptions->team);?>'][0].new_coord.split(',');
                        map.setZoom(15);
                        map.panTo(new google.maps.LatLng(<?= $vos_locations[0];?>));
                    }
                        <?php
                }
            }
        }
        
        if ($mapOptions->special == false) {
            drawVossenByTeam($mapOptions->team);
        } else {
            $allDeelgebieden = $driver->getAllDeelgebieden();
    		foreach($allDeelgebieden as $deelgebied) {
                drawVossenByTeam($deelgebied->getName());
    		}
        }
        ?>
    	}
    	<?php } // Ends "showVos" ?>
    	
    	function addPois(map) {
            <?php require BASE_DIR . 'includes/groepen.php'; ?>
    		for (var i = 0; i < aMarkers.length; i++) {
    		    var position = aMarkers[i][0];
    		    var groepnaam = aMarkers[i][1];
    		    var poiType = aMarkers[i][2];
    		    var poiUrl = aMarkers[i][3];

                // Default image
    		    var img = poiUrl ? poiUrl : null;
    		    var scaledSize = new google.maps.Size(20, 20);

    		    switch (poiType) {
    		        case 'group':
    		            if (!img) {
    		                img = "<?= BASE_URL; ?>images/maps-scoutinggroep.png";
    		            }
            			break;
            		case 'homebase':
    		            if (!img) {
                		    img = "<?= BASE_URL; ?>images/maps-scoutinggroep-home.png";
    		            }
            		    break;
    		    }
    		    
    		    var image = {
                  url: img,
                  size: null,
                  origin: null,
                  anchor: new google.maps.Point(10, 10),
                  scaledSize: scaledSize
                };

			    a[i] = new google.maps.Marker({
        			position: position,
        			map: map,
        			html: groepnaam,
        			icon: image,
        			zIndex: 100
        		});
        		if(poiType == 'homebase') {
        		    addHomebaseCirkel(map, position);
        		}
    		}
    		
    		for (var i = 0; i < a.length; i++) {
    			var marker = a[i];
    			google.maps.event.addListener(marker, 'click', function () {
    					infowindow.setContent(this.html);
    					infowindow.open(map, this);
    			});
    		}
    	}
    </script>
<?php } else { ?>
<script type="text/javascript">
$('#map').html('<em>Configureer <strong><code>google-js-api-key</code></strong> om Google Maps te gebruiken</em>');
</script>
<?php } //end of if GOOGLE_MAPS_ENABLED ?>
<?php } // end of function make_map ?>
