<?php

class Gcm {
    private $id;
    private $gcmId;
    private $riderId;
    private $enabled;
    private $time;

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setGcmId($gcmId) {
        $this->gcmId = $gcmId;
    }

    public function getGcmId() {
        return $this->gcmId;
    }

    public function setRiderId($riderId) {
        $this->riderId = $riderId;
    }

    public function getRiderId() {
        return $this->riderId;
    }

    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    public function getEnabled() {
        return $this->enabled;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getTime() {
        return $this->time;
    }

    public function toArray() {
        return array (
                'id' => $this->id,
                'gcm_id' => $this->gcmId,
                'rider_id' => $this->riderId,
                'enabled' => $this->enabled,
                'time' => $this->time 
        );
    }
}