<?php

class Rider {
    private $id;
    private $userId;
    private $deelgebied;
    private $bijrijder;
    private $tel;
    private $van;
    private $tot;
    private $auto;
    private $user;
    private $locations;

    public function __construct($userId = null, $deelgebied = null, $bijrijder = null, $tel = null, $van = null, $tot = null, $auto = null) {
        $this->userId = $userId;
        $this->deelgebied = $deelgebied;
        $this->bijrijder = $bijrijder;
        $this->tel = $tel;
        $this->van = $van;
        $this->tot = $tot;
        $this->auto = $auto;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setDeelgebied($deelgebied) {
        $this->deelgebied = $deelgebied;
    }

    public function getDeelgebied() {
        return $this->deelgebied;
    }

    public function setBijrijder($bijrijder) {
        $this->bijrijder = $bijrijder;
    }

    public function getBijrijder() {
        return $this->bijrijder;
    }

    public function setTel($tel) {
        $this->tel = $tel;
    }

    public function getTel() {
        return $this->tel;
    }

    public function setVan($van) {
        $this->van = $van;
    }

    public function getVan() {
        return $this->van;
    }

    public function setTot($tot) {
        $this->tot = $tot;
    }

    public function getTot() {
        return $this->tot;
    }
    
    public function setAuto($auto) {
        $this->auto = $auto;
    }

    public function getAuto() {
        return $this->auto;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    /**
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    public function setLocations($locations) {
        $this->locations = $locations;
    }

    public function getLocations() {
        return $this->locations;
    }

    public function toArray() {
        $result = array (
                'id' => $this->getId(),
                'user_id' => $this->getUserId(),
                'deelgebied' => $this->getDeelgebied(),
                'bijrijder' => $this->getBijrijder(),
                'tel' => $this->getTel(),
                'van' => $this->getVan(),
                'tot' => $this->getTot(),
                'auto' => $this->getAuto(),
                'locations' => array () 
        );
        if (isset($this->locations) && count($this->locations) > 0) {
            foreach ( $this->locations as $location ) {
                $result ['locations'] [] = $location->toArray();
            }
        }
        return $result;
    }
}