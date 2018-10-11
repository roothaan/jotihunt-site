<?php
/**
 * <p>This is the Location for a vossenteam.</p>
 */
class Location {
    private $id;
    private $vossen_id;
    private $longitude;
    private $latitude;
    private $x;
    private $y;
    private $date;
    private $rider_id;
    private $type;   // ""= hint  2= hunt  3= spot
    private $address;
    private $counterhuntrondjeId;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setVossenId($vossenId) {
        $this->vossen_id = $vossenId;
    }

    public function getVossenId() {
        return $this->vossen_id;
    }

    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    public function getLongitude() {
        return $this->longitude;
    }

    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    public function getLatitude() {
        return $this->latitude;
    }

    public function setX($x) {
        $this->x = $x;
    }

    public function getX() {
        return $this->x;
    }

    public function setY($y) {
        $this->y = $y;
    }

    public function getY() {
        return $this->y;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function getDate() {
        return $this->date;
    }

    public function setRiderId($rider_id) {
        $this->rider_id = $rider_id;
    }

    public function getRiderId() {
        return $this->rider_id;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getAddress() {
        return $this->address;
    }
    
    public function setCounterhuntrondjeId($counterhuntrondjeId) {
        $this->counterhuntrondjeId = $counterhuntrondjeId;
    }

    public function getCounterhuntrondjeId() {
        return $this->counterhuntrondjeId;
    }
    
    public function getTypeName() {
        switch ($this->getType()) {
            case 2 :
                return 'hunt';
            case 3 :
                return 'spot';
            default :
                return 'hint';
        }
    }

    public function toArray() {
        return array (
                'id' => $this->getId(),
                'longitude' => $this->getLongitude(),
                'latitude' => $this->getLatitude(),
                'x' => $this->getX(),
                'y' => $this->getY(),
                'address' => $this->getAddress(),
                'riderId' => $this->getRiderId(),
                'type' => $this->getType(),
                'date' => $this->getDate(),
                'counterhuntrondje_id' => $this->getCounterhuntrondjeId()
        );
    }
}