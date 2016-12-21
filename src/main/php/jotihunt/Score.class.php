<?php

class Score {
    private $id;
    private $plaats;
    private $groep;
    private $woonplaats;
    private $regio;
    private $hunts;
    private $tegenhunts;
    private $opdrachten;
    private $fotoopdrachten;
    private $hints;
    private $totaal;
    private $lastupdate;

    public function __construct() {
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setPlaats($plaats) {
        $this->plaats = $plaats;
    }

    public function getPlaats() {
        return $this->plaats;
    }

    public function setGroep($groep) {
        $this->groep = $groep;
    }

    public function getGroep() {
        return $this->groep;
    }

    public function setWoonplaats($woonplaats) {
        $this->woonplaats = $woonplaats;
    }

    public function getWoonplaats() {
        return $this->woonplaats;
    }

    public function setRegio($regio) {
        $this->regio = $regio;
    }

    public function getRegio() {
        return $this->regio;
    }

    public function setHunts($hunts) {
        $this->hunts = $hunts;
    }

    public function getHunts() {
        return $this->hunts;
    }

    public function setTegenhunts($tegenhunts) {
        $this->tegenhunts = $tegenhunts;
    }

    public function getTegenhunts() {
        return $this->tegenhunts;
    }

    public function setOpdrachten($opdrachten) {
        $this->opdrachten = $opdrachten;
    }

    public function getOpdrachten() {
        return $this->opdrachten;
    }

    public function setFotoopdrachten($fotoopdrachten) {
        $this->fotoopdrachten = $fotoopdrachten;
    }

    public function getFotoopdrachten() {
        return $this->fotoopdrachten;
    }

    public function setHints($hints) {
        $this->hints = $hints;
    }

    public function getHints() {
        return $this->hints;
    }

    public function setTotaal($totaal) {
        $this->totaal = $totaal;
    }

    public function getTotaal() {
        return $this->totaal;
    }

    public function setLastupdate($lastupdate) {
        $this->lastupdate = $lastupdate;
    }

    public function getLastupdate() {
        return $this->lastupdate;
    }

    public function toArray() {
        return array (
                'id' => $this->getId(),
                'plaats' => $this->getPlaats(),
                'groep' => $this->getGroep(),
                'woonplaats' => $this->getWoonplaats(),
                'regio' => $this->getRegio(),
                'hunts' => $this->getHunts(),
                'tegenhunts' => $this->getTegenhunts(),
                'opdrachten' => $this->getOpdrachten(),
                'fotoopdrachten' => $this->getFotoopdrachten(),
                'hints' => $this->getHints(),
                'totaal' => $this->getTotaal(),
                'lastupdate' => $this->getLastupdate() 
        );
    }
}

?>