<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';

class DatabaseApi {
    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function doGet() {
        $apiParts = $this->request->getApiParts();
        if (count($apiParts) == 0) {
            return false;
        }
        
        $operation = $apiParts [0];
        switch ($operation) {
            case 'initDb' :
                $databaseDriver = Datastore::getDatabaseDriver();
                $databaseDriver->initDb();
            break;
        }
        return true;
    }
}