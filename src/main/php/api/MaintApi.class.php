<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'jotihunt/Location.class.php';

class MaintApi {
    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function doGet() {
        $apiParts = $this->request->getApiParts();
        $firstPart = array_shift($apiParts);
    }
}

?>