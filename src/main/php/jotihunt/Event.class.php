<?php

class Event {
    
    private $id;
    private $name;
    private $public;
    private $starttime;
    private $endtime;
    
    public function __construct($id, $name, $public, $starttime, $endtime) {
        $this->id = $id;
        $this->name = $name;
        $this->public = $public;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
    }
    
    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function isPublic(){
        return $this->public;
    }

    public function setPublic($public){
        $this->public = $public;
    }

    public function getStarttime(){
        return $this->starttime;
    }

    public function setStarttime($starttime){
        $this->starttime = $starttime;
    }

    public function getEndtime(){
        return $this->endtime;
    }

    public function setEndtime($endtime){
        $this->endtime = $endtime;
    }
    
    public function toArray() {
        return array (
                'id' => $this->id,
                'name' => $this->name,
                'public' => $this->public,
                'starttime' => $this->starttime,
                'endtime' => $this->endtime
        );
    }}