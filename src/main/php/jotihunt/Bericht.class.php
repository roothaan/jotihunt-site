<?php

class Bericht {
    private $id;
    private $event_id;
    private $bericht_id;
    private $titel;
    private $datum;
    private $eindtijd;
    private $maxpunten;
    private $inhoud;
    private $lastupdate;
    private $type;

    public function __construct() {
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setEventId($event_id) {
        $this->event_id = $event_id;
    }

    public function getEventId() {
        return $this->event_id;
    }

    public function setBericht_id($bericht_id) {
        $this->bericht_id = $bericht_id;
    }

    public function getBericht_id() {
        return $this->bericht_id;
    }

    public function setTitel($titel) {
        $this->titel = $titel;
    }

    public function getTitel() {
        return $this->titel;
    }

    public function setDatum($datum) {
        $this->datum = $datum;
    }

    public function getDatum() {
        return $this->datum;
    }

    public function getFormattedDatum() {
        return date("d-m-Y H:i", strtotime($this->getDatum()));
    }

    public function setEindtijd($eindtijd) {
        $this->eindtijd = $eindtijd;
    }

    public function getEindtijd() {
        return $this->eindtijd;
    }

    public function setMaxpunten($maxpunten) {
        $this->maxpunten = $maxpunten;
    }

    public function getMaxpunten() {
        return $this->maxpunten;
    }

    public function setInhoud($inhoud) {
        $this->inhoud = $inhoud;
    }

    public function getInhoud() {
        return $this->inhoud;
    }

    public function setLastupdate($lastupdate) {
        $this->lastupdate = $lastupdate;
    }

    public function getLastupdate() {
        return $this->lastupdate;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function toArray() {
        return array (
                'id' => $this->getId(),
                'event_id' => $this->getEventId(),
                'bericht_id' => $this->getBericht_id(),
                'titel' => $this->getTitel(),
                'datum' => $this->getDatum(),
                'eindtijd' => $this->getEindtijd(),
                'maxpunten' => $this->getMaxpunten(),
                'inhoud' => $this->getInhoud(),
                'lastupdate' => $this->getLastupdate(),
                'type' => $this->getType() 
        );
    }
}
?>