<?php

class Poi {
    private $id;
    private $event_id;
    private $name;
    private $data;
    private $latitude;
    private $longitude;
    private $type;

    public function __construct($id, $event_id, $name, $data, $latitude, $longitude, $type) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->name = $name;
        $this->data = $data;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->type = $type;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getEventId(){
        return $this->event_id;
    }

    public function setEventId($event_id){
        $this->event_id = $event_id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getData(){
        return $this->data;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function getLatitude(){
        return $this->latitude;
    }

    public function setLatitude($latitude){
        $this->latitude = $latitude;
    }

    public function getLongitude(){
        return $this->longitude;
    }

    public function setLongitude($longitude){
        $this->longitude = $longitude;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }
    
    public function toArray() {
        return array(
            'id' => $this->getId(),
            'event_id' => $this->getEventId(),
            'name' => $this->getName(),
            'data' => $this->getData(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude(),
            'type' => $this->getType(),
            );
    }
}