<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/Poi.class.php';
require_once CLASS_DIR . 'jotihunt/PoiType.class.php';

class PoitypeApi {
    private $request;
    private $apiParts;
    private $siteDriver;
    private $requestedId;

    public function setRequest($request) {
        $this->request = $request;
    }

    private function init() {
        // Check for size of ApiParts (need at least 1, the name)
        $apiParts = $this->request->getApiParts();
        $this->requestedId = array_shift($apiParts);
        $this->apiParts = $apiParts;
        
        $this->siteDriver = Datastore::getSiteDriver();
    }

    public function doGet() {
        global $authMgr;
        $this->init();
        
        if (null == $this->requestedId) {
            $allPoiTypes = $this->siteDriver->getAllPoiTypes();
            $result = array();
            foreach ( $allPoiTypes as $poiType ) {
                $result [] = $poiType->toArray();
            }
            return $result;
        } else {
            $poi = $this->siteDriver->getPoiTypeById($this->requestedId);
            if ($poi) {
                return $poi->toArray();
            }
        }
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'success' => false,
                'reason' => 'no_poi_found'
        );
    }
}