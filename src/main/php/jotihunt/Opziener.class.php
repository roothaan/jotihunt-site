<?php

class Opziener {
    
    private $id;
    private $user_id;
    private $display_name;
    private $deelgebied_id;
    private $type;
    
    public function __construct($id, $user_id, $display_name, $deelgebied_id, $type) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->display_name = $display_name;
        $this->deelgebied_id = $deelgebied_id;
        $this->type = $type;
    }
    
    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id){
        $this->user_id = $user_id;
    }

    public function getDisplayName(){
        return $this->display_name;
    }

    public function getDeelgebiedId(){
        return $this->deelgebied_id;
    }

    public function setDeelgebiedId($deelgebied_id){
        $this->deelgebied_id = $deelgebied_id;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }
    
    public function toArray() {
        return array (
                'id' => $this->id,
                'user_id' => $this->user_id,
                'deelgebied_id' => $this->deelgebied_id,
                'type' => $this->type
        );
    }
}