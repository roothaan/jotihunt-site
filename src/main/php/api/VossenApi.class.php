<?php
require_once CLASS_DIR . 'api/ApiException.class.php';

require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'jotihunt/Location.class.php';

require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';

class VossenApi {
    private $request;
    private $team;
    private $siteDriver;
    private $apiParts;
    
    public function setRequest($request) {
        $this->request = $request;
    }

    private function init() {
        // Check for size of ApiParts (need at least 1, the name)
        $apiParts = $this->request->getApiParts();
        $vossenTeam = array_shift($apiParts);
        $this->apiParts = $apiParts;
        $vossenTeam = urldecode($vossenTeam);

        $this->siteDriver = Datastore::getSiteDriver();
        
        if (null != $vossenTeam) {
            $this->team = $this->siteDriver->getTeamByDeelgebiedName($vossenTeam);
            $counterhuntRondje = $this->siteDriver->getActiveCounterhuntRondje($vossenTeam);
            if ($counterhuntRondje) {
                $this->team->setCounterhuntRondjeId($counterhuntRondje->getId());
            }
            $locations = $this->siteDriver->getLocations($this->team);
            $this->team->setLocations($locations);
        }
    }

    public function doGet() {
        $this->init();
        
        if (null == $this->team) {
            // no team specified, return all
            $allAreas = $this->siteDriver->getAllDeelgebieden();
            $result = array ();
            foreach ( $allAreas as $area ) {
                $team = $this->siteDriver->getTeamByDeelgebiedName($area->getName());
                if ($team) {
                    $counterhuntRondje = $this->siteDriver->getActiveCounterhuntRondje($area->getName());
                    if ($counterhuntRondje) {
                        $team->setCounterhuntRondjeId($counterhuntRondje->getId());
                    }
                    $locations = $this->siteDriver->getLocations($team);
                    $team->setLocations($locations);
                    $result [] = $team->toArray();
                }
            }
            return $result;
        } else if (count($this->apiParts) == 0) {
            // If there are no more requestVars, dump all info
            return $this->team->toArray();
        } else {
            $operation = $this->apiParts [0];
            switch ($operation) {
                case 'name' :
                    return array (
                            $operation => $this->team->getName() 
                    );
                case 'status' :
                    return array (
                            $operation => $this->team->getStatus() 
                    );
                case 'locations' :
                    return array (
                            $operation => $this->team->getLocations() 
                    );
                default :
                    throw new ApiException('Operation ' . $operation . ' not part of the VossenApi');
            }
        }
    }

    public function doPost() {
        $this->init();
        $data = $this->request->getRequestVars();
        
        $date = time();
        if (isset($data ['date'])) {
            $date = intval($data ['date']);
        }
        
        $location = new Location();
        $location->setLongitude($data ['longitude']);
        $location->setLatitude($data ['latitude']);
        $location->setX($data ['x']);
        $location->setY($data ['y']);
        $location->setAddress($data ['address']);
        $location->setType(intval($data ['type']));
        $location->setRiderId(intval($data ['riderId']));
        $location->setDate($date);
        // $this->team komt uit URL /vossen/DEELGEBIEDNAME
        $location->setCounterhuntRondjeId($this->siteDriver->getActiveCounterhuntRondje($this->team->getName())->getId());
        
        
        // If it's a hunt, check if it hasn't been added before
        if (isset($data ['code']) && ! empty($data ['code']) && $data ['code'] != "null") {
            $existingHunt = $this->siteDriver->getHuntByCode($data ['code']);
            if ($existingHunt) {
                // Already exists, skip!
                header("HTTP/1.0 406 Not Acceptable");
                return array (
                        'error' => 'Hunt already exists',
                        'success' => false 
                );
            }
        }
        
        $newLocation = $this->siteDriver->addLocation($this->team, $location);
        
        if (null != $newLocation) {
            $allGcmIds = $this->siteDriver->getAllActiveGcms();
            $payload = array (
                    'location' => $newLocation->toArray(),
                    'teamName' => $this->team->getName() 
            );
            $gcmSender = new GcmSender();
            $gcmSender->setReceiverIds($allGcmIds);
            $gcmSender->setPayload($payload);
            $result = $gcmSender->send();
            
            // Hey, we'll add a hunt as well, sweet!
            if (isset($data ['code']) && ! empty($data ['code']) && $data ['code'] != "null") {
                $this->siteDriver->addHunt($data ['riderId'], $newLocation->getId(), $data ['code']);
            }
            
            header("HTTP/1.0 201 Created");
            return array (
                    'newLocation' => $newLocation->toArray(),
                    'success' => true 
            );
        }
        
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'newLocation' => $newLocation,
                'success' => false 
        );
    }
}

?>