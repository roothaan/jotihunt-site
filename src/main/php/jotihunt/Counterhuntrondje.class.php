<?php

class Counterhuntrondje {
    private $id;
    private $deelgebied_id;
    private $organisation_id;
    private $name;
    private $active;

    public function __construct($id, $deelgebied_id, $organisation_id, $name, $active) {
        $this->id = $id;
        $this->deelgebied_id = $deelgebied_id;
        $this->organisation_id = $organisation_id;
        $this->name = $name;
        $this->active = $active;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }
    
    public function getDeelgebiedId(){
        return $this->deelgebied_id;
    }

    public function setDeelgebiedId($deelgebied_id){
        $this->deelgebied_id = $deelgebied_id;
    }

    public function getOrganisationId(){
        return $this->organisation_id;
    }

    public function setOrgansationId($organisation_id){
        $this->organisation_id = $organisation_id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getActive(){
        return $this->active;
    }

    public function setActive($active){
        $this->active = $active;
    }

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'deelgebied_id' => $this->getDeelgebiedId(),
            'organisation_id' => $this->getOrganisationId(),
            'name' => $this->getName(),
            'active' => $this->getActive()
            );
    }
}