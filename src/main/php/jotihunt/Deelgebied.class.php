<?php

class Deelgebied {
    
    private $id;
    private $event_id;
    private $name;
    private $linecolor;
    private $polycolor;
    
    public function __construct($id, $event_id, $name, $linecolor, $polycolor) {
        $this->id = $id;
        $this->event_id = $event_id;
        $this->name = $name;
        $this->linecolor = $linecolor;
        $this->polycolor = $polycolor;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getEventId() {
        return $this->event_id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getLineColor() {
        return $this->linecolor;
    }

    public function getPolyColor() {
        return $this->polycolor;
    }
    
    public function toArray() {
        return array(
            'id' => $this->id,
            'event_id' => $this->event_id,
            'name' => $this->name,
            'linecolor' => $this->linecolor,
            'polycolor' => $this->polycolor
            );
    }
}