<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';

class GcmApi {
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
        $this->init();
        
        if (null == $this->requestedId) {
            $allGcm = $this->siteDriver->getAllActiveGcms();
            
            $result = array ();
            foreach ( $allGcm as $gcm ) {
                $result [] = $gcm->toArray();
            }
            
            return $result;
        } else {
            $gcm = $this->siteDriver->getGcm($this->requestedId);
            return $gcm->toArray();
        }
    }

    public function doPost() {
        $this->init();
        
        $data = $this->request->getRequestVars();
        
        // Check for existing ID
        $existingGcm = $this->siteDriver->getGcmByGcmId($data ['gcmId'], $data ['riderId']);
        if (null != $existingGcm) {
            header("HTTP/1.0 200 OK");
            return array (
                    'status' => 'GcmId already exists',
                    'success' => true 
            );
        }
        $gcm = new Gcm();
        $gcm->setGcmId($data ['gcmId']);
        $gcm->setRiderId($data ['riderId']);
        $gcm->setTime(time());
        
        $gcm = $this->siteDriver->addGcm($gcm);
        if (null != $gcm) {
            header("HTTP/1.0 201 Created");
            return array (
                    'gcmId' => $gcm->getId(),
                    'success' => true 
            );
        }
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'rowsChanged' => $rowsChanged,
                'success' => false 
        );
    }
    
    public function doDelete() {
        $this->init();
        
        $data = $this->request->getRequestVars();
        
        // Check for existing ID
        $existingGcm = $this->siteDriver->getGcmByGcmId($data ['gcmId'], $data ['riderId']);
        if (null != $existingGcm) {
            // Found one, delete it
            $this->siteDriver->removeGcm($existingGcm->getId());
            header("HTTP/1.0 204 No Content");
            return array('success' => true);
        } else {
            header("HTTP/1.0 404 Not Found");
            return array(
                'gcmId' => $data['gcmId'],
                'success' => false
            );
        }
    }
}

?>