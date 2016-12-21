<?php

class Coordinate {
    
    private $id;
    private $deelgebied_id;
    private $longitude;
    private $latitude;
    private $order_id;
    
    public function __construct($id, $deelgebied_id, $longitude, $latitude, $order_id) {
        $this->id = $id;
        $this->deelgebied_id = $deelgebied_id;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->order_id = $order_id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getDeelgebiedId() {
        return $this->deelgebied_id;
    }

    public function setDeelgebiedId($deelgebied_id) {
        return $this->deelgebied_id = $deelgebied_id;
    }
    
    public function getLongitude() {
        return $this->longitude;
    }
    
    public function getLatitude() {
        return $this->latitude;
    }

    public function getOrderId() {
        return $this->order_id;
    }
    
    public function toArray() {
        return array(
            'id' => $this->id,
            'deelgebied_id' => $this->deelgebied_id,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'order_id' => $this->order_id,
            );
    }
}