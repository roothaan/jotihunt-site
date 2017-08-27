<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/helpers/DeelgebiedHelper.class.php';

class DeelgebiedApi {
    private $request;
    private $apiParts;
    private $siteDriver;
    private $requestedId;

    public function setRequest($request) {
        $this->request = $request;
    }

    /**
     *  /api/deelgebied
     *  /api/deelgebied/10
     *  /api/deelgebied/kml
     */
    private function init() {
        // Check for size of ApiParts (need at least 1, the name)
        $apiParts = $this->request->getApiParts();
        $this->requestedId = array_shift($apiParts);
        $this->apiParts = $apiParts;
        
        $this->siteDriver = Datastore::getSiteDriver();
    }

    public function doGet() {
        $this->init();
        if ('kml' === $this->requestedId) {
            return $this->doGetKml();
        }
        return $this->doGetJson();
    }

    public function doGetKml() {
        global $authMgr;
        $deelgebiedHelper = new DeelgebiedHelper();
        $kmlOutput = $deelgebiedHelper->getKmlForEventId($authMgr->getMyEventId());
        $deelgebiedHelper->outputAsKml($kmlOutput);
        die();
    }
    
    public function doGetJson() {
        if (null == $this->requestedId) {
            $allAreas = $this->siteDriver->getAllDeelgebieden();
            
            $result = array ();
            foreach ( $allAreas as $area ) {
                if ($area) {
                    $result [] = $area->toArray();
                }
            }
            return $result;
        } else {
            $area = $this->siteDriver->getDeelgebiedById($this->requestedId);
            if ($area) {
                $result = $area->toArray();
                // Add coordinates if available
                $coords = $this->siteDriver->getAllCoordinatesForDeelgebied($area->getId());
                if ($coords) {
                    foreach ($coords as $coord) {
                        $result['coords'][] = $coord->toArray();
                    }
                }
                return $result;
            }
        }
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'success' => false,
                'reason' => 'no_area_found'
        );
    }

    public function doPost() {

        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'success' => false,
                'reason' => 'invalid_operation'
        );
    }
}

?>