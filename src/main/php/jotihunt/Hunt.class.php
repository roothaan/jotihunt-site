<?php

class Hunt {
    
    private $id;
    private $hunter_id;
    private $vossentracker_id;
    private $code;
    private $goedgekeurd;
    
    // Additional stuff
    private $time;
    private $username;
    private $user_id;
    private $adres;
    private $deelgebied;
    
    public function __construct($id, $hunter_id, $vossentracker_id, $code, $goedgekeurd, $time) {
        $this->id = $id;
        $this->hunter_id = $hunter_id;
        $this->vossentracker_id = $vossentracker_id;
        $this->code = $code;
        $this->goedgekeurd = $goedgekeurd;
        $this->time = $time;
    }
    
    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getHunterId(){
        return $this->hunter_id;
    }

    public function setHunterId($hunter_id){
        $this->hunter_id = $hunter_id;
    }
    
    public function getVossenTrackerId(){
        return $this->vossentracker_id;
    }

    public function setVossenTrackerId($vossentracker_id){
        $this->vossentracker_id = $vossentracker_id;
    }

    public function getCode(){
        return $this->code;
    }

    public function setCode($code){
        $this->code = $code;
    }

    public function getGoedgekeurd(){
        return $this->goedgekeurd;
    }

    public function setGoedgekeurd($goedgekeurd){
        $this->goedgekeurd = $goedgekeurd;
    } 

    // Additional stuff
    public function getTime(){
        return $this->time;
    }

    public function setTime($time){
        $this->time = $time;
    }

    public function getUsername(){
        return $this->username;
    }

    public function setUsername($username){
        $this->username = $username;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id){
        $this->user_id = $user_id;
    }

    public function getAdres(){
        return $this->adres;
    }

    public function setAdres($adres){
        $this->adres = $adres;
    }

    public function getDeelgebied(){
        return $this->deelgebied;
    }

    public function setDeelgebied($deelgebied){
        $this->deelgebied = $deelgebied;
    }
    
    public function toArray() {
        return array (
                'id' => $this->id,
                'hunter_id' => $this->hunter_id,
                'vossentracker_id' => $this->vossentracker_id,
                'code' => $this->code,
                'goedgekeurd' => $this->goedgekeurd,
                'time' => $this->time,
                'username' => $this->username,
        );
    }
}