<?php

class RiderLocation {
    private $id;
    private $rider_id;
    private $longitude;
    private $latitude;
    private $adres;
    private $time;
    private $accuracy;
    private $provider;

    public function __construct() {
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setRiderId($rider_id) {
        $this->rider_id = $rider_id;
    }

    public function getRiderId() {
        return $this->rider_id;
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

    public function setAdres($adres) {
        $this->adres = $adres;
    }

    public function getAdres() {
        return $this->adres;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getTime() {
        return $this->time;
    }

    public function setAccuracy($accuracy) {
        $this->accuracy = $accuracy;
    }

    public function getAccuracy() {
        return $this->accuracy;
    }

    public function setProvider($provider) {
        $this->provider = $provider;
    }

    public function getProvider() {
        return $this->provider;
    }

    public function toArray() {
        return array (
                'id' => $this->getId(),
                'rider_id' => $this->getRiderId(),
                'longitude' => $this->getLongitude(),
                'latitude' => $this->getLatitude(),
                'adres' => $this->getAdres(),
                'time' => $this->getTime(),
                'accuracy' => $this->getAccuracy(),
                'provider' => $this->getProvider() 
        );
    }
}

?>