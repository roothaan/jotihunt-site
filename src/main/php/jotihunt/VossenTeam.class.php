<?php

class VossenTeam {
    private $id;
    private $name;
    private $deelgebied;
    private $status;
    private $speelhelftId;
    // Voor de API is het handig om ook het counterhuntrondje mee te geven
    private $counterhuntRondjeId;
    
    private $locations = array ();

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function setDeelgebied($deelgebied) {
        $this->deelgebied = $deelgebied;
    }

    public function getDeelgebied() {
        return $this->deelgebied;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getStatus() {
        return $this->status;
    }
    
    public function setSpeelhelftId($speelhelftId) {
        $this->speelhelftId = $speelhelftId;
    }

    public function getSpeelhelftId() {
        return $this->speelhelftId;
    }

    public function addLocation($location) {
        $this->locations [] = $location;
    }

    public function setLocations($locations) {
        $this->locations = $locations;
    }

    public function getLocations() {
        return $this->locations;
    }
    public function setCounterhuntRondjeId($counterhuntRondjeId) {
        $this->counterhuntRondjeId = $counterhuntRondjeId;
    }

    public function toArray() {
        $result = array ();
        $result ['id'] = $this->id;
        $result ['name'] = $this->name;
        $result ['deelgebied'] = $this->deelgebied;
        $result ['status'] = $this->status;
        $result ['speelhelft_id'] = $this->speelhelftId;
        // Locations need to be converted as well (for PHP 5.3)
        for($i = 0; $i < count($this->locations); $i ++) {
            $result ['locations'] [] = $this->locations [$i]->toArray();
        }
        if (isset($this->counterhuntRondjeId)) {
            $result['counterhuntRondjeId'] = $this->counterhuntRondjeId;
        }
        
        return $result;
    }
    
    public function getLastLocation()
    {
        return $this->locations[0];
    }
    
    public function getLastHuntLocation()
    {
        foreach($this->locations as $location) {
            if($location->getType() == 2) {
                return $location;
            }
        }
        return false;
    }
}