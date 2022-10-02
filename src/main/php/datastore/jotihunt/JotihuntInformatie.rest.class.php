<?php

class JotihuntInformatieRest {
    
    var bool $debug;
    var $conn;
    var string $protocolPrefix = 'https://';
    var string $jhBase = 'https://jotihunt.nl/';
    var string $apiBase = 'https://jotihunt.nl/api/2.0/';

    public function __construct() {
        $conn = Datastore::getDatastore();
        $this->conn = $conn->getConnection();
    }
    
    public function setDebug($debug) {
        $this->debug = $debug;
    }
    
    public function fixUrls($original) {
        if ($this->debug) {
            echo "<br /><br /><span> BEFORE" . 
            $original . 
            "</span><br /><br />";
        }
        
        $patterns = array();
        
        // This is for relative URLs, we prefix those with the Jotihunt site
        $patterns[0] = '/src\="\/[^\/]/';
        $replacement = 'src="'.$this->jhBase;
        $result = preg_replace($patterns, $replacement, $original);
        
        // This is for protocol-less URLs, we assume https here
        $patterns[0] = '/src\="\/\//';
        $replacement = 'src="'.$this->protocolPrefix;
        $result = preg_replace($patterns, $replacement, $result);
        
        if ($this->debug) {
            echo "<br /><br /><span>AFTER: " . 
            $result . 
            "</span><br /><br />";
        }
        return $result;
    }
    
    public function fixHintUrls($original) {
        if ($this->debug) {
            echo "<br /><br /><span> [fixHintUrls] BEFORE" . 
            $original . 
            "</span><br /><br />";
        }
                // This is for protocol-less URLs, we assume https here
        $patterns[0] = '/\/\//';
        $replacement = $this->protocolPrefix;
        $result = preg_replace($patterns, $replacement, $original);
        
        if ($this->debug) {
            echo "<br /><br /><span>[fixHintUrls] AFTER: " . 
            $result . 
            "</span><br /><br />";
        }
        return $result;
    }

    public function updateOpdrachten() {
        global $authMgr;
        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'articles');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo '<br /><br /><span style="color:red;">' .
                    $nieuwitemlist->error . 
                    '</span><br /><br />';
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {
                    if ($nieuwsitem->type !== 'assignment') continue;

                    $bericht = new Bericht();
                    $bericht->setType('opdracht');
                    $bericht->setEventId($authMgr->getMyEventId());
                    $bericht->setBericht_id($nieuwsitem->id);
                    $bericht->setTitel($nieuwsitem->title);
                    $bericht->setDatum($nieuwsitem->publish_at);
                    $bericht->setLastupdate($nieuwsitem->publish_at);
                    $bericht->setInhoud($nieuwsitem->message->content);

                    // Unique to assignment
                    $bericht->setEindtijd($nieuwsitem->message->end_time);
                    $bericht->setMaxpunten($nieuwsitem->message->max_points);

                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    public function updateNieuws() {
        global $authMgr;
        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'articles');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo '<br /><br /><span style="color:red;">' . 
                    $nieuwitemlist->error . 
                    '</span><br /><br />';
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {
                    if ($nieuwsitem->type !== 'news') continue;

                    $bericht = new Bericht();
                    $bericht->setType('nieuws');
                    $bericht->setEventId($authMgr->getMyEventId());
                    $bericht->setBericht_id($nieuwsitem->id);
                    $bericht->setTitel($nieuwsitem->title);
                    $bericht->setDatum($nieuwsitem->publish_at);
                    $bericht->setLastupdate($nieuwsitem->publish_at);
                    $bericht->setInhoud($nieuwsitem->message->content);

                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    function getHintAsHTMLByDeelgebied($hint, $deelgebied) {
        $inhoud = "<div class='hintDeelgebied hintDeelgebied_".$deelgebied."'><div class='label'>".$deelgebied."</div><div class='hintContent'>";
        if(is_array($hint)) {
            foreach($hint as $hintPart) {
                $hintPart = $this->fixHintUrls($hintPart);
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
    public function updateHints() {
        global $authMgr;

        $collection = array ();
        $nieuwitemlist = $this->getJsonFromJotihunt($this->apiBase . 'articles');
        if (isset($nieuwitemlist->error) && ! empty($nieuwitemlist->error)) {
            if ($this->debug) {
                echo "<br /><br /><span style='color:red;'>" . 
                    $nieuwitemlist->error . 
                    "</span><br /><br />";
            }
        } else {
            if (! empty($nieuwitemlist) && isset($nieuwitemlist->data) && count($nieuwitemlist->data) > 0) {
                foreach ( $nieuwitemlist->data as $nieuwsitem ) {
                    if ($nieuwsitem->type !== 'hint') continue;

                    $bericht = new Bericht();
                    $bericht->setType('hint');
                    $bericht->setEventId($authMgr->getMyEventId());
                    $bericht->setBericht_id($nieuwsitem->id);
                    $bericht->setTitel($nieuwsitem->title);
                    $bericht->setDatum($nieuwsitem->publish_at);
                    $bericht->setLastupdate($nieuwsitem->publish_at);

                    $inhoud = "<div class='inhoud'>".$nieuwsitem->message->content."</div>";
                                
                    if(isset($nieuwsitem->Alpha)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Alpha, "Alpha");
                    }
                    if(isset($nieuwsitem->Bravo)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Bravo, "Bravo");
                    }
                    if(isset($nieuwsitem->Charlie)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Charlie, "Charlie");
                    }
                    if(isset($nieuwsitem->Delta)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Delta, "Delta");
                    }
                    if(isset($nieuwsitem->Echo)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Echo, "Echo");
                    }
                    if(isset($nieuwsitem->Foxtrot)) {
                        $inhoud .= $this->getHintAsHTMLByDeelgebied($nieuwsitem->Foxtrot, "Foxtrot");
                    }

                    $bericht->setInhoud($inhoud);

                    $collection [] = $bericht;
                }
            }
        }
        
        return $collection;
    }

    public function getVossenStatusen() {
        $vossenstatuslijst = $this->getJsonFromJotihunt($this->apiBase . 'areas');
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
                    $vossenteam->setName($vossenstatus->name);
                    $vossenteam->setDeelgebied($vossenstatus->name);
                    // Convert status
                    $status = 'rood';
                    switch ($vossenstatus->status) {
                        case 'red':
                            $status = 'rood';
                            break;
                        case 'green':
                            $status = 'groen';
                            break;
                        case 'orange':
                            $status = 'oranje';
                            break;
                    }
                    $vossenteam->setStatus($status);

                    $collection [] = $vossenteam;
                }
                return $collection;
            }
        }
        return false;
    }

    public function getScorelijst() {
        $scorelijst = $this->getJsonFromJotihunt($this->apiBase . 'scorelijst', true);
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

    private function getJsonFromJotihunt($url, $useAuthToken=false) {
        $header = "Accept-language: en\r\n";
        if ($useAuthToken && defined('API_TOKEN')) {
            $token = API_TOKEN;
            $header .= "token: $token\r\n";
        }
        $opts = array (
                'http' => array (
                        'method' => "GET",
                        'header' => $header
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