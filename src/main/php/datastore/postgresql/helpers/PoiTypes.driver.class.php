<?php
require_once CLASS_DIR . 'jotihunt/PoiType.class.php';

class SiteDriverPostgresql_PoiType {
    // DataStore
    private $conn;

    public function setConn($conn) {
        $this->conn = $conn;
    }
    
    //--------
    
    public function removePoiType($poiTypeId) {
        $sqlName = 'removePoiType';
        $values = array (
                $poiTypeId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function getAllPoiTypesSu() {
        global $authMgr;
        $sqlName = 'getAllPoiTypesSu';
        $values = array (
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all poitypes (SU)');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $poitypes = new PoiType(
                            $row ['id'],
                            $row ['event_id'],
                            $row ['organisation_id'],
                            $row ['name'],
                            $row ['onmap'],
                            $row ['onapp'],
                            $row ['image']);
            $_result[] = $poitypes;
        }
        return $_result;
    }
    
    public function getAllPoiTypes() {
        global $authMgr;
        $sqlName = 'getAllPoiTypes';
        $values = array (
            $authMgr->getMyEventId(),
            $authMgr->getMyOrganisationId(),
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all pois');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $poitypes = new PoiType(
                            $row ['id'],
                            $row ['event_id'],
                            $row ['organisation_id'],
                            $row ['name'],
                            $row ['onmap'],
                            $row ['onapp'],
                            $row ['image']);
            
            $_result [] = $poitypes;
        }
        return $_result;
    }
    
    public function addPoiType($poitype) {
        global $authMgr;
        $sqlName = 'addPoiType';
        
        $values = array (
            $poitype->getEventId(),
            $poitype->getOrganisationId(),
            $poitype->getName(),
            $poitype->getOnmap(),
            $poitype->getOnapp(),
            $poitype->getImage()
            );
            
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return $row['id'];
        }
        return false;
    }

    public function updatePoiType($poi) {
        $sqlName = 'updatePoiType';
        
        $values = array (
                $poi->getId(),
                $poi->getEventId(),
                $poi->getName(),
                $poi->getOnmap(),
                $poi->getOnapp(),
                $poi->getImage()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $poi) {
                throw new DatastoreException('Could not update poitype ' . $poi->toArray());
            }
            throw new DatastoreException('Could not update poitype (poitype === null)');
        }
    }

    public function getPoiTypeById($poiTypeId) {
        global $authMgr;
        $sqlName = 'getPoiTypeById';
        $values = array (
            $authMgr->getMyEventId(),
            $poiTypeId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get Poitype by ID:' . $poiId);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $poitype = new PoiType(
                        $row ['id'],
                        $row ['event_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['onmap'],
                        $row ['onapp'],
                        $row ['image']);
            return $poitype;
        }
        return null;
    }
    
    public function getPoiTypeByName($poiTypeName) {
        global $authMgr;
        $sqlName = 'getPoiTypeByName';
        $values = array (
            $authMgr->getMyEventId(),
            $poiTypeName
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get Poitype by Name:' . $poiTypeName);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $poitype = new PoiType(
                        $row ['id'],
                        $row ['event_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['onmap'],
                        $row ['onapp'],
                        $row ['image']);
            return $poitype;
        }
        return null;
    }
}