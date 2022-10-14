<?php

class KmlHelper {

    public function parseDeelgebiedenKml() {
        global $driver;
        if (isset($_FILES['kml_file']) && is_file($_FILES['kml_file']['tmp_name'])) {
            $kml_file = file_get_contents($_FILES['kml_file']['tmp_name']);
            $kml_file_parsed = simplexml_load_string($kml_file, "SimpleXMLElement", LIBXML_NOCDATA);
            $kml_file_parsed_encoded = json_encode($kml_file_parsed);
            $kml = json_decode($kml_file_parsed_encoded, true);
            
            $eventName = $kml['Document']['name'];
            echo 'Event (KML) Name: <strong>' . $eventName . '</strong><br />';
            
            // Collect the styles
            $colorInfo = array();
            if (isset($kml['Document']['Style'])) {
                echo 'Found ' . count($kml['Document']['Style']) . 
                ' styles for <strong>' .$eventName . '</strong><br />';
                foreach($kml['Document']['Style'] as $style) {
                    // ID =  / id
                    
                    // The str_replace is to compensate for Google Maps creator's normal naming schema
                    // Jotihunt uses a slightly different schema, so this normalizes it.
                    $styleName = str_replace('-normal', '', $style['@attributes']['id']);
                    $styleId = '#'.$styleName;
                    $colorInfo[$styleId] = array();
                    if (array_key_exists('LineStyle', $style)) {
                        $colorInfo[$styleId]['linecolor'] = $style['LineStyle']['color'];
                    }
                    if (array_key_exists('PolyStyle', $style)) {
                        $colorInfo[$styleId]['polycolor'] = $style['PolyStyle']['color'];
                    }
                }
            }else {
                // No style is set..
                echo 'No styles found for event <strong>'. $eventName . '</strong>...<br />';
            }
            
            foreach($kml['Document']['Folder'] as $folder) {
                if (isset ($folder['name']) && $folder['name'] == 'Deelgebieden') {
                    echo 'Found <strong>Deelgebieden</strong> layer with ' .
                         count($folder['Placemark']) .' Placemarks!<br />';
                    $deelgebiedCoordinates = array();
                    $deelgebiedColours = array();
                    foreach ($folder['Placemark'] as $placemark) {
                        
                        // Colours first
                        $deelgebiedColours[$placemark['name']] = $colorInfo[$placemark['styleUrl']];
                        
                        // Coordinates second
                        $split_coordinates = array();
                        $_coordinates = $placemark['Polygon']['outerBoundaryIs']['LinearRing']['coordinates'];
                        $_coordinates2 = trim($_coordinates);
                        $_split_coordinates = preg_split('/ +/', $_coordinates2);
                        $_counter = 0;
                        $_firstC = null;
                        foreach ($_split_coordinates as $split_coordinate) {
                            $c = explode(',', $split_coordinate);
                            if (null == $_firstC) {
                                $_firstC = $c;
                            }
                            $split_coordinates[] = new Coordinate(null, null, $c[0], $c[1], $_counter++);
                        }
                        // Add the first one again, to close the loop
                        if (count($split_coordinates) > 0) {
                            $split_coordinates[] = new Coordinate(null, null, $_firstC[0], $_firstC[1], $_counter++);
                        }
                        $deelgebiedCoordinates[$placemark['name']] = $split_coordinates;
                        
                        echo 'Deelgebied (Placemark) Name: <strong>' . $placemark['name'] . '</strong><br />';
                        $lineColor = $deelgebiedColours[$placemark['name']]['linecolor'];
                        $lineColorCss = substr($lineColor, -6);
                        echo 'Line: <code><span style="color:#'.$lineColorCss.'">' . $lineColor . '</span></code><br />';
                        $polyColor = $deelgebiedColours[$placemark['name']]['polycolor'];
                        $polyColorCss = substr($polyColor, -6);
                        echo 'Poly: <code><span style="color:#'.$polyColorCss.'">' . $polyColor . '</span></code><br />';
                        
                        echo '<ul>';
                        foreach ($split_coordinates as $split_coordinate) {
                            echo '<li>' . $split_coordinate->getOrderId() . ':' . $split_coordinate->getLongitude() . '/' . $split_coordinate->getLatitude() . '</li>';
                        }
                        echo '</ul>';
                    }
                    
                    if (isset($_POST['import']) && $_POST['import'] === 'on') {
                        $eventId = null;
                        if (!isset($_POST['event_id']) || $_POST['event_id'] == null) {
                            // Create Event with the $eventName
                            $event = new Event(null, $eventName, true, null, null);
                            $eventId = $driver->addEvent($event);
                        } else {
                            $eventId = $_POST['event_id'];
                        }
                        // Actually import
                        $createOrUpdate = '';
                        foreach ($deelgebiedCoordinates as $deelgebiedName => $split_coordinates) {
                            // replace
                            $deelgebied = $driver->getDeelgebiedByName($deelgebiedName);
                            if ($deelgebied) {
                                $deelgebiedId = $deelgebied->getId();
                                $driver->removeCoordinateForDeelgebied($deelgebiedId);
                                $createOrUpdate = 'Updated';
                            } else {
                                // TODO actually add colors
                                $deelgebied   = new Deelgebied(
                                    null,
                                    $eventId,
                                    $deelgebiedName,
                                    $deelgebiedColours[ $deelgebiedName ]['linecolor'],
                                    $deelgebiedColours[ $deelgebiedName ]['polycolor'] );
                                $deelgebiedId = $driver->addDeelgebied( $deelgebied );

                                // Add vos
                                $speelhelften = $driver->getAllSpeelhelftenForEvent($eventId);
                                if ($speelhelften && count($speelhelften) > 0) {
                                    $team = new VossenTeam();
                                    $team->setDeelgebied( $deelgebiedId );
                                    $team->setName( $deelgebiedName );
                                    $team->setStatus( 'rood' );

                                    $team->setSpeelhelftId( $speelhelften[0]->getId() );

                                    $driver->addTeam( $team );
                                }
                                $createOrUpdate = 'Created';
                            }
                            foreach ($split_coordinates as $split_coordinate) {
                                $split_coordinate->setDeelgebiedId($deelgebiedId);
                                $driver->addCoordinate($split_coordinate);
                            }
                            echo $createOrUpdate . ' <strong>'.$deelgebiedName.'</strong> (ID:'.$deelgebiedId.')
                                    with <strong>' . count($split_coordinates) . '</strong> coordinates <br />';
                        }
                    }
                }
            }
        }
    }
    
    public function parsePoiKml() {
        global $driver;
        global $authMgr;
        
        if (isset($_FILES['kml_file']) && is_file($_FILES['kml_file']['tmp_name'])) {
            $kml_file = file_get_contents($_FILES['kml_file']['tmp_name']);
            $kml_file_parsed = simplexml_load_string($kml_file, "SimpleXMLElement", LIBXML_NOCDATA);
            $kml_file_parsed_encoded = json_encode($kml_file_parsed);
            $kml = json_decode($kml_file_parsed_encoded, true);
            
            $eventName = $kml['Document']['name'];
            echo 'KML Name: <strong>' . $eventName . '</strong><br />';
            
            if (isset($kml['Document']['Folder'])) {
                foreach($kml['Document']['Folder'] as $folder) {
                    $this->importPoisTestFolder($folder);
                }
            } else {
                // No folders, try base layer
                $folder = $kml['Document'];
                $this->importPoisTestFolder($folder);
            }
        }
    }
    
    private function importPoisTestFolder($folder) {
        $folderLayer = $_POST['poi_folder_name'];
        $import = isset ($folder['name']) && $folder['name'] == $folderLayer;
        if ($import) {
            echo 'Importing layer <strong>'.$folderLayer.'</strong> with ' . count($folder['Placemark']) .' entries!<br />';
            $pois = $this->parsePoiKmlPlaceMarks($folder);
            $this->importPois($pois);
        } else {
            echo 'Skipping layer <strong>' . $folder['name'] . '</strong><br />';
        }
    }
    
    private function importPois($pois) {
        global $driver, $authMgr;
        if (isset($_POST['import']) && $_POST['import'] === 'on') {
            // Actually import
            foreach ($pois as $poi) {
                $poi = new Poi(
                        null,
                        $authMgr->getMyEventId(),
                        $poi['name'], 
                        $poi['data'],
                        $poi['coordinates']->getLatitude(),
                        $poi['coordinates']->getLongitude(),
                        $_POST['poi_type']);
                $poiId = $driver->addPoi($poi);
                echo 'Created <strong>'.$poi->getName().'</strong> (ID:'.$poiId .').<br />';
            }
        }
    }
    
    private function parsePoiKmlPlaceMarks($folder) {
        $pois = array();
        foreach ($folder['Placemark'] as $placemark) {
            // Coordinates
            $split_coordinates = array();
            $_coordinates = $placemark['Point']['coordinates'];
            $_c = explode(',', $_coordinates);
            $coordinates = new Coordinate(null, null, $_c[0], $_c[1], 0);

            $name = $placemark['name'];
            $placemarkExtendedData = '';
            if (isset($placemark['ExtendedData'])) {
                $placemarkExtendedData = '';
                if (is_array($placemark['ExtendedData'])) {
                    if (isset($placemark['ExtendedData']['Data'])) {
                        foreach ($placemark['ExtendedData']['Data'] as $dataPoint) {
                            $placemarkExtendedData .= $dataPoint['@attributes']['name'] . ': ' . $dataPoint['value'] . " \n";
                        }
                    }
                } else {
                    $placemarkExtendedData = $placemark['ExtendedData'];
                }
                $data = trim(
                    str_replace("\t", ' ', 
                        str_replace("\n", '', $placemarkExtendedData)
                    )
                );
            }
            echo 'POI Name: <strong>' . $name . '</strong><br />';
            $poi = array();
            $poi['name'] = $name;
            $poi['data'] = isset($data) ? $data : '';
            $poi['coordinates'] = $coordinates;
            $pois[] = $poi;
        }
        return $pois;
    }
}