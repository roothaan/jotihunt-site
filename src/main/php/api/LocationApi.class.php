<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'jotihunt/Location.class.php';

class LocationApi {
    private $request;
    private $locationId;
    private $siteDriver;
    private $apiParts;

    public function setRequest($request) {
        $this->request = $request;
    }

    private function init() {
        // Check for size of ApiParts (need at least 1, the name)
        $apiParts = $this->request->getApiParts();
        $locationId = array_shift($apiParts);
        $this->apiParts = $apiParts;
        
        $this->siteDriver = Datastore::getSiteDriver();
        
        if (null != $locationId) {
            $this->locationId = $locationId;
        }
    }

    public function doGet() {
        $this->init();
        http_response_code(500);
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'message' => 'doGet not allowed',
                'success' => false 
        );
    }

    public function doPost() {
        $this->init();
        http_response_code(500);
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'message' => 'doPost not allowed',
                'success' => false 
        );
    }

    public function doDelete() {
        $this->init();
        
        $affectedRows = $this->siteDriver->removeVossenLocation($this->locationId);
        
        return array (
                'affectedRows' => $affectedRows 
        );
    }
}

?>