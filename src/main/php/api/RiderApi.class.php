<?php
require_once CLASS_DIR . 'api/ApiException.class.php';

require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'jotihunt/Location.class.php';
require_once CLASS_DIR . 'jotihunt/Rider.class.php';
require_once CLASS_DIR . 'jotihunt/RiderLocation.class.php';

require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';

class RiderApi {
    private $request;
    private $apiParts;
    private $riderTeamName;
    private $riderTeam;
    private $siteDriver;
    private $operation;
    private $includeAll = false;

    public function setRequest($request) {
        $this->request = $request;
    }

    private function init() {
        // Check for size of ApiParts (need at least 1, the name)
        $apiParts = $this->request->getApiParts();
        $riderTeamName = array_shift($apiParts);
        $this->apiParts = $apiParts;
        
        $this->siteDriver = Datastore::getSiteDriver();
        
        error_log("riderTeamName=".$riderTeamName);
        if ('me' === $riderTeamName) {
            $authCode = $this->request->getAuthCode();
            error_log("getAuthCode:" . $authCode);
            if (null == $authCode && defined('DEV_MODE') && DEV_MODE == true) {
                // This is allowed if you use debug mode
                // So you can use "/api/rider/me" to debug
                global $authMgr;
                $authCode = $authMgr->getSessionId();
            error_log("getAuthCode reset via SessionId:" . $authCode);
            }
            $user = $this->siteDriver->getUser($authCode);
            if ($user) {
                error_log("user:" . print_r($user->toArray(), true));
                $this->riderTeam = $this->siteDriver->getRiderByNameHackForJotihunt2017($user->getUsername());
                error_log("riderTeam=" . print_r($this->riderTeam, true));
            } else {
                error_log('Cannot find the "me" rider!');
            }
        } else if (null != $riderTeamName) {
            $this->riderTeam = $this->siteDriver->getRiderByNameHackForJotihunt2017($riderTeamName);
        }
        
        if (count($this->apiParts) > 0) {
            $this->operation = array_shift($this->apiParts);
            if ('all' == $this->operation) {
                $this->includeAll = true;
            }
        }
    }

    public function doGet() {
        global $authMgr;
        $this->init();
        
        if (null == $this->riderTeam) {
            // no team specified, return all
            $result = $this->siteDriver->getAllRiders();
            error_log('# of riders found: ' . count($result));
            $locations = $this->siteDriver->getLastRiderLocations();
            error_log('# of locations found: ' . count($locations));
            foreach ( $locations as $hunterId => $location) {
                error_log('hunterID:' . $hunterId . ', location: [' . $location->getLongitude() . ',' . $location->getLatitude() .']');
            }
            $returnVal = array ();
            foreach ( $result as $rider ) {
                $riderInfo = $rider->toArray();
                error_log('Parsing Rider user_id: ' . $riderInfo['user_id'] . ', id: ' . $riderInfo['id']);
                if (array_key_exists($rider->getId(), $locations)) {
                    error_log(' - Found locations for this Hunter ID: ' . $rider->getId());
                    $location = $locations [$rider->getId()];
                    $timeLastLocation = strtotime($location->getTime());
                    $currentTime = time();
                    error_log('$currentTime     : ' . $currentTime);
                    error_log('$timeLastLocation: ' . $timeLastLocation);
                    
                    $diff = $currentTime - $timeLastLocation;
                    // If the rider hasn't been seen for 3600 seconds (an hour), we skip it
                    if ($diff > 3600) {
                        error_log(' - Found stale location [diff='.$diff.'] for this Rider user_id: ' . $riderInfo['user_id']);
                        continue;
                    }
                    $riderInfo ['displayname'] = $rider->getUser()->getDisplayName();
                    $riderInfo ['location'] = $location->toArray();
                    error_log(' - Adding location [' . $location->getLongitude() . ',' . $location->getLatitude() .'] Rider user_id: ' . $riderInfo['user_id']);
                } else {
                    error_log(' - Found NO locations for this Hunter user_id: ' . $rider->getId());
                }
                $returnVal [] = $riderInfo;
            }
            return $returnVal;
        } else if (count($this->apiParts) == 0) {
            // If there are no more requestVars, dump all info
            if ($this->includeAll) {
                $locations = $this->siteDriver->getRiderLocation($this->riderTeam->getId());
                $this->riderTeam->setLocations($locations);
            }
            $riderInfo = $this->riderTeam->toArray();
            $riderInfo ['displayname'] = $this->riderTeam->getUser()->getDisplayName();
            
            // Add event ID
            $riderInfo['event_id'] = $authMgr->getMyEventId();
            
            return $riderInfo;
        } else {
            throw new ApiException('Operation ' . $this->operation . ' not part of the RiderApi');
        }
    }

    public function doPost() {
        $this->init();
        $data = $this->request->getRequestVars();
        
        if (isset($data ['locations'])) {
            $result = $this->handleLocationData($data);
            if ($result) {
                header("HTTP/1.0 201 Created");
                return $result;
            }
        }
        
        // If all else fails..        
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'rowsChanged' => 0,
                'success' => false 
        );
    }
    
    private function handleLocationData($data) {
        $rowsChanged = 0;
        // This is almost always the default (Android sending multiple locations)
        if (isset($data ['locations'])) {
            $json = $data ['locations'];
            error_log('JSON received:' . $json);
            $locations = json_decode($json, true);
            if (null != $locations) {
                if (is_array($locations)) {
                    error_log('JSON decode success array (size):' . sizeof($locations));
                }else {
                    error_log('JSON decode succes:' . $locations);
                }

                foreach ( $locations as $data ) {
                    $rowsChanged += $this->addLocation($data);
                }
                error_log('Num rows added:' . $rowsChanged);
            } else {
                error_log('JSON decode failed, error code:' . json_last_error());
            }
        }
        
        if ($rowsChanged > 0) {
            $newRiderLocation = $this->siteDriver->getRiderLocation($this->riderTeam->getId());
            $gcmResult = 'No GCM results';
            if (count($newRiderLocation) > 0) {
                $allGcmIds = $this->siteDriver->getAllActiveGcms();
                $lastLocation = array_shift($newRiderLocation);
                $payload = array (
                        'hunterLocation' => $lastLocation->toArray() 
                );
                $gcmSender = new GcmSender();
                $gcmSender->setReceiverIds($allGcmIds);
                $gcmSender->setPayload($payload);
                $gcmResult = $gcmSender->send();
            }
            
            return array (
                    'rowsChanged' => $rowsChanged,
                    'gcmResult' => $gcmResult,
                    'success' => true 
            );
        }
        return null;
    }
    
    // $data should be an ARRAY!
    // Returns $numRows
    private function addLocation($data) {
        $location = new RiderLocation();
        $location->setRiderId($this->riderTeam->getId());
        
        $location->setLongitude($data ['longitude']);
        $location->setLatitude($data ['latitude']);
        $location->setTime(time());
        if (isset($data ['adres'])) {
            $location->setAdres($data ['adres']);
        }
        if (isset($data ['time'])) {
            $location->setTime($data ['time']);
        }
        if (isset($data ['accuracy'])) {
            $location->setAccuracy($data ['accuracy']);
        }
        if (isset($data ['provider'])) {
            $location->setProvider($data ['provider']);
        }
        
        return $this->siteDriver->addRiderLocation($location);
    }
}

?>