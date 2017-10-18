<?php

class JotihuntInformatieRest {
    
    var $debug;
    var $conn;
    var $jhBase = 'https://jotihunt.net/';
    var $apiBase = 'https://jotihunt.net/api/1.0/';

    public function __construct() {
        $conn = Datastore::getDatastore();
        $this->conn = $conn->getConnection();
    }
    
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    public function updateOpdrachten() {
        global $authMgr;
        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'opdracht');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo '<br /><br /><span style="color:red;">' .
                    $nieuwitemlist->error . 
                    '</span><br /><br />';
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {
                    $bericht = new Bericht();
                    $bericht->setEventId($authMgr->getMyEventId());

                    if(is_array($nieuwsitem->ID)) {
                        $bericht->setBericht_id(end($nieuwsitem->ID));
                    } else {
                        $bericht->setBericht_id($nieuwsitem->ID);
                    }
                    
                    $bericht->setTitel($nieuwsitem->titel);
                    $bericht->setDatum($nieuwsitem->datum);
                    $bericht->setEindtijd($nieuwsitem->eindtijd);
                    $bericht->setMaxpunten($nieuwsitem->maxpunten);
                    $bericht->setLastupdate($nieuwitemlist->last_update);
                    $bericht->setType('opdracht');
                    
                    $itemdetails = $this->getJsonFromJotihunt($this->apiBase . 'opdracht/' . $bericht->getBericht_id());
                    if (isset($itemdetails->error) && ! empty($itemdetails->error)) {
                        if ($this->debug) {
                            echo '<br /><br /><span style="color:red;">' .
                            $itemdetails->error .
                            '</span><br /><br />';
                        }
                    } else {
                        if (! empty($itemdetails) && isset($itemdetails->data) && count($itemdetails->data) > 0) {
                            foreach ( $itemdetails->data as $itemdetail ) {
                                $inhoud = str_replace("src=\"/","src=\"".$this->jhBase,$itemdetail->inhoud);
                                $bericht->setInhoud($inhoud);
                            }
                        }
                    }
                    
                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    public function updateNieuws() {
        global $authMgr;
        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'nieuws');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo '<br /><br /><span style="color:red;">' . 
                    $nieuwitemlist->error . 
                    '</span><br /><br />';
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {

                    $bericht = new Bericht();
                    $bericht->setEventId($authMgr->getMyEventId());
                    
                    if(is_array($nieuwsitem->ID)) {
                        $bericht->setBericht_id(end($nieuwsitem->ID));
                    } else {
                        $bericht->setBericht_id($nieuwsitem->ID);
                    }
                    
                    $bericht->setTitel($nieuwsitem->titel);
                    $bericht->setDatum($nieuwsitem->datum);
                    $bericht->setLastupdate($nieuwitemlist->last_update);
                    $bericht->setType('nieuws');

                    $itemdetails = $this->getJsonFromJotihunt($this->apiBase . 'nieuws/' . $bericht->getBericht_id());
                    if (isset($itemdetails->error) && ! empty($itemdetails->error)) {
                        if ($this->debug) {
                            echo "<br /><br /><span style='color:red;'>" . 
                                $itemdetails->error . 
                                    "</span><br /><br />";
                        }
                    } else {
                        if (! empty($itemdetails) && isset($itemdetails->data) && count($itemdetails->data) > 0) {
                            foreach ( $itemdetails->data as $itemdetail ) {
                                $inhoud = str_replace("src=\"/","src=\"".$this->jhBase,$itemdetail->inhoud);
                                $bericht->setInhoud($inhoud);
                            }
                        }
                    }
                    
                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    public function updateHints() {
        global $authMgr;
        function getHintAsHTMLByDeelgebied($hint, $deelgebied) {
            $inhoud = "<div class='hintDeelgebied hintDeelgebied_".$deelgebied."'><div class='label'>".$deelgebied."</div><div class='hintContent'>";
            if(is_array($hint)) {
                foreach($hint as $hintPart) {
                    if (filter_var($hintPart, FILTER_VALIDATE_URL) && is_array(getimagesize($hintPart))) { 
                        $hintPart = "<img src='".$hintPart."' />";
                    }
                    $inhoud .= "<div class='hintPart'>".$hintPart."</div>";
                }
            } else {
                $inhoud .= $hint;
            }
            $inhoud .= "</div> </div>";
            return $inhoud;
        }
        
        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'hint');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo "<br /><br /><span style='color:red;'>" . 
                    $nieuwitemlist->error . 
                    "</span><br /><br />";
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {
                    $bericht = new Bericht();
                    $bericht->setEventId($authMgr->getMyEventId());
                    
                    if(is_array($nieuwsitem->ID)) {
                        $bericht->setBericht_id(end($nieuwsitem->ID));
                    } else {
                        $bericht->setBericht_id($nieuwsitem->ID);
                    }
                    
                    $bericht->setTitel($nieuwsitem->titel);
                    $bericht->setDatum($nieuwsitem->datum);
                    $bericht->setLastupdate($nieuwitemlist->last_update);

                    $bericht->setType('hint');
                    
                    $itemdetails = $this->getJsonFromJotihunt($this->apiBase . 'hint/' . $bericht->getBericht_id());
                    if (isset($itemdetails->error) && ! empty($itemdetails->error)) {
                        if ($this->debug) {
                            echo "<br /><br /><span style='color:red;'>" . 
                                $itemdetails->error . 
                                "</span><br /><br />";
                        }
                    } else {
                        if (! empty($itemdetails) && isset($itemdetails->data) && count($itemdetails->data) > 0) {
                            foreach ( $itemdetails->data as $itemdetail ) {
                                $inhoud = str_replace("src=\"/","src=\"".$this->jhBase,$itemdetail->inhoud);
                                $inhoud = "<div class='inhoud'>".$inhoud."</div>";
                                
                                if(isset($nieuwsitem->Alpha)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Alpha, "Alpha");
                                }
                                if(isset($nieuwsitem->Bravo)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Bravo, "Bravo");
                                }
                                if(isset($nieuwsitem->Charlie)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Charlie, "Charlie");
                                }
                                if(isset($nieuwsitem->Delta)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Delta, "Delta");
                                }
                                if(isset($nieuwsitem->Echo)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Echo, "Echo");
                                }
                                if(isset($nieuwsitem->Foxtrot)) {
                                    $inhoud .= getHintAsHTMLByDeelgebied($nieuwsitem->Foxtrot, "Foxtrot");
                                }
                                
                                $bericht->setInhoud($inhoud);
                            }
                        }
                    }
                    
                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    public function getVossenStatusen() {
        $vossenstatuslijst = $this->getJsonFromJotihunt($this->apiBase . 'vossen');
        if (isset($vossenstatuslijst->error) && ! empty($vossenstatuslijst->error)) {
            if ($this->debug) {
                echo "<br /><br /><span style='color:red;'>" . 
                    $vossenstatuslijst->error . 
                    "</span><br /><br />";
            }
        } else {
            if (! empty($vossenstatuslijst) && isset($vossenstatuslijst->data) && count($vossenstatuslijst->data) > 0) {
                $collection = array ();
                foreach ( $vossenstatuslijst->data as $vossenstatus ) {
                    $vossenteam = new VossenTeam();
                    $vossenteam->setName($vossenstatus->team);
                    $vossenteam->setDeelgebied($vossenstatus->team);
                    $vossenteam->setStatus($vossenstatus->status);
                    
                    $collection [] = $vossenteam;
                }
                return $collection;
            }
        }
        return false;
    }

    public function getScorelijst() {
        $scorelijst = $this->getJsonFromJotihunt($this->apiBase . 'scorelijst');
        if (isset($scorelijst->error) && ! empty($scorelijst->error)) {
            if ($this->debug) {
            echo "<br /><br /><span style='color:red;'>" . 
                $scorelijst->error . 
                "</span><br /><br />";
            }
        } else {
            if (! empty($scorelijst) && isset($scorelijst->data) && count($scorelijst->data) > 0) {
                $collection = array ();
                
                // Sometimes (during development of 2017, for example)
                // the last_update stamp was not part of the API response.
                // This is dirty hack to have a fallback timestamp
                // '2017-10-21 08:00:00' == 1508572800
                $timestamp = 1508572800;

                if (!empty($scorelijst->last_update)) {
                    $timestamp = strtotime($scorelijst->last_update);
                }
                foreach ( $scorelijst->data as $scoreitem ) {
                    $score = new Score();
                    
                    // Check for the bare minimum
                    if (!isset($scoreitem->plaats) ||
                        !isset($scoreitem->groep) ||
                        !isset($scoreitem->woonplaats) ||
                        !isset($scoreitem->regio)
                    ) {
                        // If there is none of the items above, skip it.
                        continue;
                    }
                
                    $score->setPlaats($scoreitem->plaats);
                    $score->setGroep($scoreitem->groep);
                    $score->setWoonplaats($scoreitem->woonplaats);
                    $score->setRegio($scoreitem->regio);
                    $score->setHunts((!empty($scoreitem->hunts))?$scoreitem->hunts:0);
                    $score->setTegenhunts((!empty($scoreitem->tegenhunts))?$scoreitem->tegenhunts:0);
                    $score->setOpdrachten((!empty($scoreitem->opdrachten))?$scoreitem->opdrachten:0);
                    $score->setFotoopdrachten((!empty($scoreitem->fotoopdrachten))?$scoreitem->fotoopdrachten:0);
                    $score->setHints((!empty($scoreitem->hints))?$scoreitem->hints:0);
                    $score->setTotaal((!empty($scoreitem->totaal))?$scoreitem->totaal:0);
                    $score->setLastupdate($timestamp);
                    
                    $collection [] = $score;
                }
                return $collection;
            }
        }
        return false;
    }

    private function getJsonFromJotihunt($url) {
        $opts = array (
                'http' => array (
                        'method' => "GET",
                        'header' => "Accept-language: en\r\n" 
                ) 
        );
        
        $context = stream_context_create($opts);
        $fp = fopen($url, 'r', false, $context);

        $buffer = '';
        if ($fp) {
            while ( ! feof($fp) ) {
                $buffer .= fgets($fp, 5000);
            }
            
            fclose($fp);
        }
        if ($this->debug) {
            echo '<h1>getJsonFromJotihunt</h1><h2>'.$url.'</h2><pre>';
            echo '<h2>$opts</h2>';
            print_r($opts);
            echo '<h2>$context</h2>';
            print_r($context);
            echo '<h2>$fp</h2>';
            print_r($fp);
            echo '<h2>$buffer</h2>';
            print_r(htmlentities($buffer));
            echo '</pre>';
        }

        return json_decode(str_replace('"plaats":0', '"plaats":1', $buffer));
    }
}