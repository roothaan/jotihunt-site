<?php
class Speelhelft {
    private $id;
    private $event_id;
    private $starttime;
    private $endtime;
    
    public function __construct($id, $event_id, $starttime, $endtime) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->starttime = $starttime;
        $this->endtime = $endtime;
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
}