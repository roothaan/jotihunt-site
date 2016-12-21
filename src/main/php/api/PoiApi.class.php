<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/Poi.class.php';

class PoiApi {
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
            $allPois = $this->siteDriver->getAllPois();
            
            $result = array ();
            $myOrganisationName = $this->siteDriver->getOrganisationById($authMgr->getMyOrganisationId())->getName();
            foreach ( $allPois as $poi ) {
                if ($poi) {
                    if (stripos($poi->getName(), $myOrganisationName) !== false) {
                        $poi->setType('homebase');
                    }
                    $result [] = $poi->toArray();
                }
            }
            return $result;
        } else {
            $poi = $this->siteDriver->getPoiById($this->requestedId);
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