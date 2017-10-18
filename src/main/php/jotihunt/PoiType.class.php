<?php

class PoiType {
    private $id;
    private $event_id;
    private $organisation_id;
    private $name;
    private $onmap;
    private $onapp;
    private $image;

    public function __construct($id, $event_id, $organisation_id, $name, $onmap, $onapp, $image) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->organisation_id = $organisation_id;
        $this->name = $name;
        $this->onmap = $onmap;
        $this->onapp = $onapp;
        $this->image = $image;
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

    public function getOrganisationId(){
        return $this->organisation_id;
    }

    public function setOrganisationId($organisation_id){
        $this->organisation_id = $organisation_id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getOnmap(){
        return $this->onmap;
    }

    public function setOnmap($onmap){
        $this->onmap = $onmap;
    }

    public function getOnapp(){
        return $this->onapp;
    }

    public function setOnapp($onapp){
        $this->onapp = $onapp;
    }

    public function getImage(){
        return $this->image;
    }

    public function setImage($image){
        $this->image = $image;
    }
    
    public function toArray() {
        return array(
            'id' => $this->getId(),
            'event_id' => $this->getEventId(),
            'name' => $this->getName(),
            'onmap' => $this->getOnmap(),
            'onapp' => $this->getOnapp(),
            'image' => $this->getImage(),
            'type' => $this->getType(),
            );
    }
}