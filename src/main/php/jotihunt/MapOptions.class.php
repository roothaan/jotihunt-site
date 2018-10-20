<?php

class MapOptions {
    public $x = 189898;
    public $y = 460709;
    public $marker_x = null;
    public $marker_y = null;
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
    public $centerOnCrosshair = false;
    /**
     *  true = toon alle vossen
     * false = toon specifiek team
     **/
    public $special = false;
    public $zoom = 15;
    public $type = 'ROADMAP';
    
    public function getEventId() {
        global $authMgr;
        return $authMgr->getMyEventId();
    }
}
?>