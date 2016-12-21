<?php

class Session {
    private $session_id;
    private $user_id;
    private $organisation_id;
    private $event_id;
    
    public function __construct($session_id, $user_id, $organisation_id, $event_id) {
        $this->session_id = $session_id;
        $this->user_id = $user_id;
        $this->organisation_id = $organisation_id;
        $this->event_id = $event_id;
    }
    
    public function getSessionId() {
        return $this->session_id;
    }
    
    public function getUserId() {
        return $this->user_id;
    }
    
    public function getOrganisationId() {
        return $this->organisation_id;
    }
    
    public function getEventId() {
        return $this->event_id;
    }
    
    public function toArray() {
        $result = array (
                'session_id' => $this->getSessionId(),
                'user_id' => $this->getUserId(),
                'organisation_id' => $this->getOrganisationId(),
                'event_id' => $this->getEventId()
        );
        return $result;
    }
}