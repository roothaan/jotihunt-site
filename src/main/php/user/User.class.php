<?php

class User {
    private $id;
    private $username;
    private $displayname;
    private $pw_hash;

    public function __construct($id, $username, $displayname, $pw_hash) {
        $this->id = $id;
        $this->username = $username;
        $this->displayname = $displayname;
        $this->pw_hash = $pw_hash;
    }

    public function getId() {
        return (int) $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function getDisplayName() {
        return $this->displayname;
    }
    
    public function setDisplayName($displayname) {
        $this->displayname = $displayname;
    }

    public function getPwHash() {
        return $this->pw_hash;
    }

    public function setPwHash($pw_hash) {
        $this->pw_hash = $pw_hash;
    }

    public function toArray() {
        $result = array (
                'id' => $this->getId(),
                'user_name' => $this->getUsername(),
                'display_name' => $this->getDisplayName(),
                'pw_hash' => $this->getPwHash()
        );
        return $result;
    }
}
?>