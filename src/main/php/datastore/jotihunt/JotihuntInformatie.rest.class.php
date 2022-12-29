<?php

class JotihuntInformatieRest {
    
    var bool $debug = false;
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

    public function updateArticles() {
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
                    $type = '';
                    switch ($nieuwsitem->type) {
                        case 'assignment':
                            $type = 'opdracht';
                            break;
                        case 'hint':
                            $type = 'hint';
                            break;
                        case 'news':
                            $type = 'nieuws';
                            break;
                    }

                    $bericht = new Bericht();
                    $bericht->setType($type);
                    $bericht->setEventId($authMgr->getMyEventId());
                    $bericht->setBericht_id($nieuwsitem->id);
                    $bericht->setTitel($nieuwsitem->title);
                    $bericht->setDatum($nieuwsitem->publish_at);
                    $bericht->setLastupdate($nieuwsitem->publish_at);
                    $bericht->setInhoud($nieuwsitem->message->content);

                    // Unique to assignment
                    if ($nieuwsitem->type === 'assignment') {
                        $bericht->setEindtijd( $nieuwsitem->message?->end_time );
                        $bericht->setMaxpunten( $nieuwsitem->message?->max_points );
                    }

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

    /**
     * @return Deelnemer[]
     */
    public function getDeelnemers() {
        $subscriptions = $this->getJsonFromJotihunt( $this->apiBase . 'subscriptions', true );
        $deelnemers = array();

        if (isset($subscriptions->error) && ! empty($subscriptions->error)) {
            if ($this->debug) {
                echo "<br /><br /><span style='color:red;'>" .
                     $subscriptions->error .
                     "</span><br /><br />";
            }
        } else {
            if ( ! empty( $subscriptions ) && isset( $subscriptions->data ) && count( $subscriptions->data ) > 0 ) {
                foreach ( $subscriptions->data as $deelnemerData ) {
                    $deelnemer = new Deelnemer();
                    $deelnemer
                        ->setName( $deelnemerData->name )
                        ->setAccomodation( $deelnemerData->accomodation )
                        ->setStreet( $deelnemerData->street )
                        ->setHousenumber( $deelnemerData->housenumber )
                        ->setHousenumberAddition( $deelnemerData->housenumber_addition )
                        ->setPostcode( $deelnemerData->postcode )
                        ->setCity( $deelnemerData->city )
                        ->setLat( $deelnemerData->lat )
                        ->setLong( $deelnemerData->long );
                    $deelnemers[] = $deelnemer;
                }
            }
        }
        return $deelnemers;
    }

    // API 2.0 is:
    // - english instead of dutch
    // - has no "regio" (now deleted)
    // - has no "plaats" (???)
    public function getScorelijst() {
        $scorelijst = $this->getJsonFromJotihunt($this->apiBase . 'subscriptions', true);
        if (isset($scorelijst->error) && ! empty($scorelijst->error)) {
            if ($this->debug) {
            echo "<br /><br /><span style='color:red;'>" . 
                $scorelijst->error . 
                "</span><br /><br />";
            }
        } else {
            if (! empty($scorelijst) && isset($scorelijst->data) && count($scorelijst->data) > 0) {
                $collection = array ();

                $timestamp = time();
                if (!empty($scorelijst->last_update)) {
                    $timestamp = strtotime($scorelijst->last_update);
                }
                foreach ( $scorelijst->data as $scoreitem ) {
                    $score = new Score();
                    
                    // Check for the bare minimum
                    if (!isset($scoreitem->name) ||
                        !isset($scoreitem->hunt_points)
                    ) {
                        // If there is none of the items above, skip it.
                        continue;
                    }

                    $total = $score->getHunts()
                             - $score->getTegenhunts()
                             + $score->getOpdrachten()
                             + $score->getFotoopdrachten()
                             + $score->getHints();

                    $score->setPlaats($total ?? 0);
                    $score->setGroep($scoreitem->name);
                    $score->setWoonplaats($scoreitem->city);
                    $score->setRegio($scoreitem->area);
                    $score->setHunts((!empty($scoreitem->hunt_points))?$scoreitem->hunt_points:0);
                    $score->setTegenhunts((!empty($scoreitem->counter_hunt_points))?$scoreitem->counter_hunt_points:0);
                    $score->setOpdrachten((!empty($scoreitem->assignment_points))?$scoreitem->assignment_points:0);
                    $score->setFotoopdrachten((!empty($scoreitem->photo_assignment_points))?$scoreitem->photo_assignment_points:0);
                    $score->setHints((!empty($scoreitem->hint_points))?$scoreitem->hint_points:0);
                    $score->setTotaal((!empty($total))?$total:0);
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