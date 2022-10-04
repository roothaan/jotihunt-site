<?php

require_once CLASS_DIR . 'jotihunt/helpers/DeelgebiedHelper.class.php';

class DeelgebiedHelper {
    private $siteDriver;
    
    public function __construct() {
        $this->siteDriver = Datastore::getSiteDriver();
    }

    /**
     * Sets the right headers and outputs the KmlOutput
     * Probably smart to call a "die()" after this..
     */
    public function outputAsKml($kmlOutput, string $filename = "deelgebiedInfo.kml") {
        header('Content-type: application/vnd.google-earth.kml+xml');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $kmlOutput;    
    }

    public function getKmlForEventId($eventId) {
        $event = $this->siteDriver->getEventById($eventId);
        
        if (!$event) {
            return false;
        }

        // Then grab all Deelgebieden
        $_allDeelgebieden = $this->siteDriver->getAllDeelgebiedenForEvent($eventId);
        $kml = array();
        $allDeelgebieden = array();
        
        // Then all Coordinates for that Deelgebied
        foreach ($_allDeelgebieden as $deelgebied) {
            $coordinates = $this->siteDriver->getAllCoordinatesForDeelgebied($deelgebied->getId());
            $kml[$deelgebied->getName()] = $coordinates;
            $allDeelgebieden[$deelgebied->getName()] = $deelgebied;
        }
        
        // Cool, we got here, let's go print stuff!
        // Creates the Document.
        $dom = new DOMDocument('1.0', 'UTF-8');
        
        // Creates the root KML element and appends it to the root document.
        $node = $dom->createElementNS('http://earth.google.com/kml/2.2', 'kml');
        $parNode = $dom->appendChild($node);
        
        // Creates a KML Document element and append it to the KML element.
        $dnode = $dom->createElement('Document');
        $docNode = $parNode->appendChild($dnode);
        
        // Add a Name (the event)
        $node = $dom->createElement('name');
        $node->appendChild($dom->createTextNode($event->getName()));
        $docNode->appendChild($node);
        
        // Add a Folder
        $folderNode = $dom->createElement('Folder');
        $docNode->appendChild($folderNode);
        
        $node = $dom->createElement('name');
        $node->appendChild($dom->createTextNode('Deelgebieden'));
        $folderNode->appendChild($node);
        
        // for each deelgebied
        foreach ($kml as $deelgebiedName => $coordinates) {
            // Generate styleUrl #ID-name
            $deelgebied = $allDeelgebieden[$deelgebiedName];
            $styleUrl = 'poly-'.$deelgebied->getId() . '-' . $deelgebiedName;
            
            $placemarkNode = $dom->createElement('Placemark');
            $folderNode->appendChild($placemarkNode);
        
            $node = $dom->createElement('styleUrl');
            $node->appendChild($dom->createTextNode('#'.$styleUrl));
            $placemarkNode->appendChild($node);
        
            $node = $dom->createElement('name');
            $node->appendChild($dom->createTextNode($deelgebied->getName()));
            $placemarkNode->appendChild($node);
        
            $node = $dom->createElement('ExtendedData');
            $placemarkNode->appendChild($node);
            
            // Polygon, outerBoundaryIs, LinearRing
            $polygonNode = $dom->createElement('Polygon');
            $placemarkNode->appendChild($polygonNode);
        
            $outerBoundaryIsNode = $dom->createElement('outerBoundaryIs');
            $polygonNode->appendChild($outerBoundaryIsNode);
        
            $linearRingNode = $dom->createElement('LinearRing');
            $outerBoundaryIsNode->appendChild($linearRingNode);
        
            //[tessellate=1, coordinates=..]
            $tessellateNode = $dom->createElement('tessellate', 1);
            $linearRingNode->appendChild($tessellateNode);
        
            $coordinatesNode = $dom->createElement('coordinates');
            $coordinatesNode->appendChild($dom->createTextNode($this->createKmlCoordinates($coordinates)));
            $linearRingNode->appendChild($coordinatesNode);
        }
        
        // Now, create Style items (inside the $folderNode node)
        foreach ($kml as $deelgebiedName => $coordinates) {
            // Generate styleUrl #ID-name
            $deelgebied = $allDeelgebieden[$deelgebiedName];
            $styleUrl = 'poly-'.$deelgebied->getId() . '-' . $deelgebiedName;
        
            $styleNode = $dom->createElement('Style');
            $styleNode->setAttribute('id', $styleUrl);
            $dnode->appendChild($styleNode);
        
            $linestyleNode = $dom->createElement('LineStyle');
            $styleNode->appendChild($linestyleNode);
        
            $node = $dom->createElement('color');
            $node->appendChild($dom->createTextNode($deelgebied->getLineColor()));
            $linestyleNode->appendChild($node);
        
            $node = $dom->createElement('width');
            $node->appendChild($dom->createTextNode('1'));
            $linestyleNode->appendChild($node);
            
            $polystyleNode = $dom->createElement('PolyStyle');
            $styleNode->appendChild($polystyleNode);
        
            $node = $dom->createElement('color');
            $node->appendChild($dom->createTextNode($deelgebied->getPolyColor()));
            $polystyleNode->appendChild($node);
        
            $node = $dom->createElement('fill');
            $node->appendChild($dom->createTextNode('1'));
            $polystyleNode->appendChild($node);
            
            $node = $dom->createElement('outline');
            $node->appendChild($dom->createTextNode('1'));
            $polystyleNode->appendChild($node);
        }
        
        $kmlOutput = $dom->saveXML();
        return $kmlOutput;
    }
    
    public function createKmlCoordinates($coordinates) {
        $result = '';
        if (!is_array($coordinates)) {
            return $result;
        }
        
        foreach ($coordinates as $coordinate) {
            $result .= $coordinate->getLongitude();
            $result .= ',';
            $result .= $coordinate->getLatitude();
            $result .= ',0.0 ';
        }
        return trim($result);
    }
}