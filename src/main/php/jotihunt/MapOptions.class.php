<?php

class MapOptions {
    public $x = 189898;
    public $y = 460709;
    public $team = 'A';
    public $showVos = true;
    public $showPlayground = true;
    public $showGroups = true;
    public $hunter;
    public $hunterFrom;
    public $hunterTo;
    public $showHunter = false;
    public $showHuntersLastLocation = true;
    public $crosshair = false;
    public $special = false;
    public $zoom = 15;
    public $type = 'ROADMAP';
    
    public function getEventId() {
        global $authMgr;
        return $authMgr->getMyEventId();
    }
}
?>