<?php
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'datastore/DatastoreException.class.php';

require_once CLASS_DIR . 'datastore/postgresql/PostgresqlDatastore.class.php';

require_once CLASS_DIR . 'jotihunt/VossenTeam.class.php';
require_once CLASS_DIR . 'jotihunt/Location.class.php';
require_once CLASS_DIR . 'jotihunt/Rider.class.php';
require_once CLASS_DIR . 'jotihunt/RiderLocation.class.php';
require_once CLASS_DIR . 'jotihunt/Bericht.class.php';
require_once CLASS_DIR . 'jotihunt/Score.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';
require_once CLASS_DIR . 'jotihunt/GcmSender.class.php';
require_once CLASS_DIR . 'jotihunt/Highscore.class.php';
require_once CLASS_DIR . 'jotihunt/Deelgebied.class.php';
require_once CLASS_DIR . 'jotihunt/Coordinate.class.php';
require_once CLASS_DIR . 'jotihunt/Event.class.php';
require_once CLASS_DIR . 'jotihunt/Speelhelft.class.php';
require_once CLASS_DIR . 'jotihunt/Poi.class.php';
require_once CLASS_DIR . 'jotihunt/Counterhuntrondje.class.php';
require_once CLASS_DIR . 'jotihunt/Image.class.php';
require_once CLASS_DIR . 'jotihunt/Opziener.class.php';
require_once CLASS_DIR . 'jotihunt/Hunt.class.php';

require_once CLASS_DIR . 'datastore/jotihunt/JotihuntInformatie.rest.class.php';

require_once CLASS_DIR . 'user/User.class.php';
require_once CLASS_DIR . 'user/Organisation.class.php';
require_once CLASS_DIR . 'user/Session.class.php';


class SiteDriverPostgresql {
    
    // DataStore
    private $conn;

    public function __construct() {
        $conn = Datastore::getDatastore();
        $this->conn = $conn->getConnection();
    }

    public function isReady() {
        $conn = Datastore::getDatastore();
        if (null !== $conn) {
            return $conn->isReady();
        }
        return false;
    }

    public function removeOpziener($id) {
        $opziener = $this->getOpzienerById($id);
        if (!$opziener) {
            throw new DatastoreException('Not a valid opziener ID ' . $id);
        }

        $sqlName = 'removeOpziener';
        $values = array (
            $opziener->getId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove opziener ' . $id);
        }
    }

    public function removeHunt($id) {
        $hunt = $this->getHunt($id);

        if (!$hunt) {
            throw new DatastoreException('Not a valid hunt ID ' . $id);
        }
        
        $sqlName = 'removeHunt';
        $values = array (
                $hunt->getId() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove hunt ' . $id);
        }
    }

    public function removeRider($id) {
        $rider = $this->getRider($id);
        
        if (!$rider) {
            throw new DatastoreException('Not a valid rider ID ' . $id);
        }

        $sqlName = 'removeRider';
        $values = array (
                $rider->getId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove rider ' . $id);
        }
    }
    
    public function removeRiderViaUserId($userId) {
        // XXX protect via getuserbyid
        

        $sqlName = 'removeRiderViaUserId';
        $values = array (
                $userId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove rider via User Id' . $id);
        }
        return true;
    }
    

    public function removePhonenumber($id) {
        $sqlName = 'removePhonenumber';
        $values = array (
                $id 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove phonenumber ' . $id);
        }
    }

    public function addHunt($hunter_id, $vossentracker_id = null, $code) {
        $sqlName = 'addHunt';
        
        $values = array (
                $hunter_id,
                $vossentracker_id,
                $code
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add hunt, hunter_id:' . $hunter_id . ',code:' . $code . ',vossentracker_id:' . $vossentracker_id);
        }
    }
    
    public function updateHunt($hunt) {
        $sqlName = 'updateHunt';
        
        $values = array (
                $hunt->getId(),
                $hunt->getHunterId(),
                $hunt->getVossenTrackerId(),
                $hunt->getCode(),
                $hunt->getGoedgekeurd()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $hunt) {
                throw new DatastoreException('Could not update hunt ' . $hunt->toArray());
            }
            throw new DatastoreException('Could not update hunt (hunt === null)');
        }
    }
    
    public function getHunt($id) {
        global $authMgr;
        $sqlName = 'getHunt';
        
        $values = array (
            $id,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        $row = pg_fetch_assoc($result);
        
        if (!$row) {
            throw new DatastoreException('Could not find hunt with id: ' . $id);
        }
        return $this->createHunt($row);        
    }

    public function addGcm($gcm) {
        $sqlName = 'addGcm';
        
        // Find Rider to ensure it's safe
        $rider = $this->getRider($gcm->getRiderId());
        if ($rider === null || !$rider) {
            throw new DatastoreException('Invalid Rider ID ['.$gcm->getRiderId().'] for this Org.');
        }
        
        $_time = self::psqlDateFromTime($gcm->getTime());
        $values = array (
                $gcm->getGcmId(),
                $gcm->getRiderId(),
                $_time
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add gcm, gcm_id:' . $gcm->getId() . ',hunter_id:' . $gcm->getRiderId() . ',time:' . $gcm->getTime());
        }
        
        $row = pg_fetch_assoc($result);
        if ($row === false) {
            throw new DatastoreException('Result is NOT a GCM ID, something went wrong :(');
        }
        
        return $this->getGcm($row ['id']);
    }

    public function updateGcm($gcm) {
        global $authMgr;
        $sqlName = 'updateGcm';
        
        $enabled = ($gcm->getEnabled() ? 't' : 'f');
        $values = array (
                $gcm->getId(),
                $gcm->getGcmId(),
                $gcm->getRiderId(),
                $enabled,
                $gcm->getTime(),
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $gcm) {
                throw new DatastoreException('Could not update gcm ' . $gcm->toArray());
            }
            throw new DatastoreException('Could not update gcm (gcm === null)');
        }
    }

    public function removeGcm($gcmId) {
        global $authMgr;
        $sqlName = 'removeGcm';
        
        $values = array (
                $gcmId,
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not remove gcm with id:' . $gcmId);
        }
    }

    public function getGcm($id) {
        global $authMgr;
        $sqlName = 'getGcm';
        
        $values = array (
                $id,
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not find gcm with id:' . $id);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $gcm = new Gcm();
            $gcm->setId($row ['id']);
            $gcm->setGcmId($row ['gcm_id']);
            $gcm->setRiderId($row ['hunter_id']);
            $gcm->setEnabled($row ['enabled'] === 't');
            $gcm->setTime($row ['time']);
            return $gcm;
        }
        return null;
    }

    public function getGcmByGcmId($gcmId, $riderId) {
        global $authMgr;
        $sqlName = 'getGcmByGcmId';
        
        $values = array (
                $gcmId,
                $riderId,
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not find gcm with gcm_id:' . $gcmId . ', hunter_id' . $riderId);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $gcm = new Gcm();
            $gcm->setId($row ['id']);
            $gcm->setGcmId($row ['gcm_id']);
            $gcm->setRiderId($row ['hunter_id']);
            $gcm->setEnabled($row ['enabled'] === 't');
            $gcm->setTime($row ['time']);
            return $gcm;
        }
        return null;
    }

    public function getAllGcms() {
        global $authMgr;
        $sqlName = 'getAllGcms';
        
        $values = array (
            $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get gcms');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $gcm = new Gcm();
            $gcm->setId($row ['id']);
            $gcm->setGcmId($row ['gcm_id']);
            $gcm->setRiderId($row ['hunter_id']);
            $gcm->setEnabled($row ['enabled'] === 't');
            $gcm->setTime($row ['time']);
            $_result [] = $gcm;
        }
        return $_result;
    }
    public function getAllGcmsSU() {
        $sqlName = 'getAllGcmsSU';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get gcms');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $gcm = new Gcm();
            $gcm->setId($row ['id']);
            $gcm->setGcmId($row ['gcm_id']);
            $gcm->setRiderId($row ['hunter_id']);
            $gcm->setEnabled($row ['enabled'] === 't');
            $gcm->setTime($row ['time']);
            $_result [] = $gcm;
        }
        return $_result;
    }

    public function getAllActiveGcms() {
        global $authMgr;
        $sqlName = 'getAllActiveGcms';
        $values = array (
            $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all active gcms');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $gcm = new Gcm();
            $gcm->setId($row ['id']);
            $gcm->setGcmId($row ['gcm_id']);
            $gcm->setRiderId($row ['hunter_id']);
            $gcm->setEnabled($row ['enabled'] === 't');
            $gcm->setTime($row ['time']);
            $_result [] = $gcm;
        }
        return $_result;
    }

    public function getAllHunts() {
        global $authMgr;
        $sqlName = 'getAllHunts';
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all hunts');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = $this->createHunt($row);
        }
        return $_result;
    }

    private function createHunt($result) {
        $hunt = new Hunt($result['id'], $result['hunter_id'], $result['vossentracker_id'], $result['code'], $result['goedgekeurd'], $result['time']);

        // Get the user as well!
        $user = $this->getUserById($result ['user_id']);
        $hunt->setUsername($user->getDisplayName());

        $hunt->setUserId($result ['user_id']);
        $hunt->setAdres($result ['adres']);
        $hunt->setDeelgebied($result ['deelgebied']);

        
        return $hunt;

    }
    /**
     * 
     * @throws DatastoreException
     * @return multitype:Highscore
     */
    public function getHunterHighscore() {
        global $authMgr;
        $sqlName = 'getHunterHighscore';
        
        $values = array (
            $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get high scores');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $user = $this->getUserById($row['user_id']);
            $highscore = new Highscore();
            $highscore->user = $user;
            $highscore->score = $row['score'];
            $_result [] = $highscore;
        }
        return $_result;
    }

    public function getScore() {
        global $authMgr;
        $orgId = $authMgr->getMyOrganisationId();
        $org = $this->getOrganisationById($orgId);
        return $this->getScoreByGroep($org->getName());
    }
    
    public function getScoreByGroep($groepnaam) {
        $sqlName = 'getScoreByGroep';
        $values = array (
                $groepnaam 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get score for group ' . $groepnaam);
        }
        
        $row = pg_fetch_assoc($result);
        
        $score = new Score();
        $score->setGroep($row ['groep']);
        $score->setPlaats($row ['plaats']);
        $score->setWoonplaats($row ['woonplaats']);
        $score->setRegio($row ['regio']);
        $score->setHunts($row ['hunts']);
        $score->setTegenhunts($row ['tegenhunts']);
        $score->setOpdrachten($row ['opdrachten']);
        $score->setFotoopdrachten($row ['fotoopdrachten']);
        $score->setHints($row ['hints']);
        $score->setTotaal($row ['totaal']);
        $score->setLastupdate($row ['lastupdate']);
        
        return $score;
    }

    public function getScoreCollection() {
        $sqlName = 'getScoreCollection';
        $values = array ();
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get score collection');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $score = new Score();
            $score->setGroep($row ['groep']);
            $score->setPlaats($row ['plaats']);
            $score->setWoonplaats($row ['woonplaats']);
            $score->setRegio($row ['regio']);
            $score->setHunts($row ['hunts']);
            $score->setTegenhunts($row ['tegenhunts']);
            $score->setOpdrachten($row ['opdrachten']);
            $score->setFotoopdrachten($row ['fotoopdrachten']);
            $score->setHints($row ['hints']);
            $score->setTotaal($row ['totaal']);
            $score->setLastupdate($row ['lastupdate']);
            
            $_result [$score->getGroep()] [$score->getLastupdate()] = $score;
        }
        
        return $_result;
    }

    public function addScore($score) {
        $sqlName = 'addScore';
        
        // TODO Sinds de JotihuntSync ook al een check op de score doet, is deze check zo te zien wat overbodig?
        $oudeScore = $this->getScoreByGroep($score->getGroep());
        if (empty($oudeScore) || $oudeScore->getLastupdate() != '' || $oudeScore->getLastupdate() != $score->getLastupdate()) {
            $values = array (
                    $score->getPlaats(),
                    $score->getGroep(),
                    $score->getWoonplaats(),
                    $score->getRegio(),
                    $score->getHunts(),
                    $score->getTegenhunts(),
                    $score->getOpdrachten(),
                    $score->getFotoopdrachten(),
                    $score->getHints(),
                    $score->getTotaal(),
                    $score->getLastupdate() 
            );
            
            $result = pg_execute($this->conn, $sqlName, $values);
            
            if (! $result) {
                throw new DatastoreException('Could not add score');
            }
        }
    }

    public function addOpziener($userId, $deelgebiedId, $type) {
        $sqlName = 'addOpziener';
        $values = array (
                $userId,
                $deelgebiedId,
                $type 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add opziener ' . $name);
        }
    }

    public function addRider($rider) {
        $sqlName = 'addRider';
        
        $_van = self::psqlDateFromTime($rider->getVan());
        $_tot = self::psqlDateFromTime($rider->getTot());
        $values = array (
                $rider->getUserId(),
                $rider->getDeelgebied(),
                $_van,
                $_tot,
                $rider->getAuto()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add rider with ID ' . $rider->getUserId());
        }
    }

    public function getOpzienerById($id) {
        global $authMgr;
        $sqlName = 'getOpziener';
        $values = array (
            $id,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get opziener with ID ' . $id);
        }
        
        if ( $row = pg_fetch_assoc($result) ) {
            return new Opziener($row['id'], $row['user_id'],$row['displayname'], $row['deelgebied_id'], $row['type']);
        }
        return null;
    }
    public function getAllOpzieners() {
        global $authMgr;
        $sqlName = 'getAllOpzieners';
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get opzieners');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $opziener = new Opziener($row['id'], $row['user_id'],$row['displayname'], $row['deelgebied_id'], $row['type']);
            $_result [] = $opziener;
        }
        return $_result;
    }

    public function updateStatus($vos) {
        $sqlName = 'updateStatus';
        
        $values = array (
                $vos->getId(),
                $vos->getStatus() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add phonenumber ' . $name . ', number:' . $phonenumber);
        }
    }

    public function addPhonenumber($userId, $phonenumber) {
        $sqlName = 'addPhonenumber';
        
        $values = array (
                $userId,
                $phonenumber 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add phonenumber for user ' . $userId . ', number:' . $phonenumber);
        }
    }

    public function getAllPhonenumbers() {
        global $authMgr;
        $sqlName = 'getAllPhonenumbers';
        $values = array (
            $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all phonenumbers');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = $row;
        }
        return $_result;
    }
    
    public function getPhonenumbersForUserId($userId) {
        $sqlName = 'getPhonenumbersForUserId';
        $values = array (
            $userId);
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all phonenumbers');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = $row;
        }
        return $_result;
    }
    

    public function getTotalAmountOfRiderLocations() {
        global $authMgr;
        $sqlName = 'getTotalAmountOfRiderLocations';
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of rider locations');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['ridercount']);
        }
        return 0;
    }

    public function getLastRiderLocations() {
        $sqlName = 'getLastRiderLocations';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get riderlocations');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $riderlocation = new RiderLocation();
            $riderlocation->setId($row ['id']);
            $riderlocation->setRiderId($row ['hunter_id']);
            $riderlocation->setLongitude($row ['longitude']);
            $riderlocation->setLatitude($row ['latitude']);
            $riderlocation->setTime($row ['time']);
            
            $_result [$row ['hunter_id']] = $riderlocation;
        }
        return $_result;
    }

    public function getRiderLocation($hunter_id, $gcm_id = null) {
        $sqlName = 'getRiderLocation';
        $values = array (
                $hunter_id 
        );
        
        if (null !== $gcm_id) {
            $sqlName = 'getRiderLocationWithGcm';
            $values = array (
                    $hunter_id,
                    $gcm_id 
            );
        }
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get riderlocations');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $riderlocation = new RiderLocation();
            $riderlocation->setId($row ['id']);
            $riderlocation->setRiderId($row ['hunter_id']);
            $riderlocation->setLongitude($row ['longitude']);
            $riderlocation->setLatitude($row ['latitude']);
            $riderlocation->setAccuracy($row ['accuracy']);
            $riderlocation->setProvider($row ['provider']);
            $riderlocation->setTime($row ['time']);
            
            $_result [] = $riderlocation;
        }
        return $_result;
    }

    public function getRiderLocationWithDateRange($hunter_id, $from = 0, $to = null) {
        $sqlName = 'getRiderLocationWithDateRange';
        $to = (null === $to ? time() : $to);
        $values = array (
                $hunter_id,
                self::psqlDateFromTime($from),
                self::psqlDateFromTime($to) 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get riderlocations with date range');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $riderlocation = new RiderLocation();
            $riderlocation->setId($row ['id']);
            $riderlocation->setRiderId($row ['hunter_id']);
            $riderlocation->setLongitude($row ['longitude']);
            $riderlocation->setLatitude($row ['latitude']);
            $riderlocation->setAccuracy($row ['accuracy']);
            $riderlocation->setProvider($row ['provider']);
            $riderlocation->setTime($row ['time']);
            
            $_result [] = $riderlocation;
        }
        return $_result;
    }

    public function addRiderLocation($riderlocation) {
        $sqlName = 'addRiderLocation';
        
        $_time = self::psqlDateFromTime($riderlocation->getTime());
        $values = array (
                $riderlocation->getRiderId(),
                $riderlocation->getLongitude(),
                $riderlocation->getLatitude(),
                $riderlocation->getAccuracy(),
                $riderlocation->getProvider(),
                $_time 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null != $riderlocation) {
                throw new DatastoreException('Could not add riderlocation for rider ' . $riderlocation->getRiderId());
            }
            throw new DatastoreException('Could not add riderlocation for rider  (riderlocation == null)');
        }
        return pg_affected_rows($result);
    }

    public function getBericht($bericht_id) {
        global $authMgr;
        $sqlName = 'getBericht';
        
        $values = array (
                $bericht_id,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $row = pg_fetch_assoc($result);
            if (! empty($row)) {
                $bericht = new Bericht();
                $bericht->setId($row ['id']);
                $bericht->setBericht_id($row ['bericht_id']);
                $bericht->setTitel($row ['titel']);
                $bericht->setDatum($row ['datum']);
                $bericht->setEindtijd($row ['eindtijd']);
                $bericht->setMaxpunten($row ['maxpunten']);
                $bericht->setInhoud($row ['inhoud']);
                $bericht->setLastupdate($row ['lastupdate']);
                $bericht->setType($row ['type']);
                
                return $bericht;
            }
        }
        
        return false;
    }

    public function getLastBericht() {
        global $authMgr;
        $sqlName = 'getLastBericht';
        
        $values = array (
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $row = pg_fetch_assoc($result);
            if (! empty($row)) {
                $bericht = new Bericht();
                $bericht->setId($row ['id']);
                $bericht->setBericht_id($row ['bericht_id']);
                $bericht->setTitel($row ['titel']);
                $bericht->setDatum($row ['datum']);
                $bericht->setEindtijd($row ['eindtijd']);
                $bericht->setMaxpunten($row ['maxpunten']);
                $bericht->setInhoud($row ['inhoud']);
                $bericht->setLastupdate($row ['lastupdate']);
                $bericht->setType($row ['type']);
                
                return $bericht;
            }
        }
        
        return false;
    }
    
    public function getLastBerichtByType($type) {
        global $authMgr;
        $sqlName = 'getLastBerichtByType';
        $values = array (
                $type,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $row = pg_fetch_assoc($result);
            if (! empty($row)) {
                $bericht = new Bericht();
                $bericht->setId($row ['id']);
                $bericht->setBericht_id($row ['bericht_id']);
                $bericht->setTitel($row ['titel']);
                $bericht->setDatum($row ['datum']);
                $bericht->setEindtijd($row ['eindtijd']);
                $bericht->setMaxpunten($row ['maxpunten']);
                $bericht->setInhoud($row ['inhoud']);
                $bericht->setLastupdate($row ['lastupdate']);
                $bericht->setType($row ['type']);
                
                return $bericht;
            }
        }
        
        return false;
    }
    
    public function getLastHunt() {
        global $authMgr;
        $sqlName = 'getLastHunt';
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $row = pg_fetch_assoc($result);
            if (! empty($row)) {
                return $row;
            }
        }
        
        return false;
    }

    public function addBericht($bericht) {
        $sqlName = 'addBericht';
        
        $db_bericht = $this->getBericht($bericht->getBericht_id());
        
        $zelfde = true;
        if ($db_bericht !== false && ($bericht->getTitel() != $db_bericht->getTitel() || $bericht->getInhoud() != $db_bericht->getInhoud() || $bericht->getMaxpunten() != $db_bericht->getMaxpunten())) {
            $zelfde = false;
        }
        
        if ($db_bericht === false || ! $zelfde) {
        } else {
            return true;
        }
        
        $values = array (
                $bericht->getBericht_id(),
                $bericht->getEventId(),
                $bericht->getTitel(),
                $bericht->getDatum(),
                strlen($bericht->getEindtijd()) > 0 ? $bericht->getEindtijd() : null,
                $bericht->getMaxpunten(),
                $bericht->getInhoud(),
                strlen($bericht->getLastupdate()) > 0 ? $bericht->getLastupdate() : null,
                $bericht->getType() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add bericht ' . $bericht->getBericht_id());
        }
    }

    public function getBerichtGeschiedenis($bericht_id) {
        global $authMgr;
        $sqlName = 'getBerichtGeschiedenis';
        
        $values = array (
                $bericht_id,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $collection = array ();
            while ( $row = pg_fetch_assoc($result) ) {
                if (! empty($row)) {
                    $bericht = new Bericht();
                    $bericht->setId($row ['id']);
                    $bericht->setBericht_id($row ['bericht_id']);
                    $bericht->setTitel($row ['titel']);
                    $bericht->setDatum($row ['datum']);
                    $bericht->setEindtijd($row ['eindtijd']);
                    $bericht->setMaxpunten($row ['maxpunten']);
                    $bericht->setInhoud($row ['inhoud']);
                    $bericht->setLastupdate($row ['lastupdate']);
                    $bericht->setType($row ['type']);
                    
                    $collection [] = $bericht;
                }
            }
            return $collection;
        }
        
        return false;
    }

    public function getBerichtCollectionByType($type) {
        global $authMgr;
        $sqlName = 'getBerichtCollectionByType';
        $values = array (
                $type,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $collection = array ();
            while ( $row = pg_fetch_assoc($result) ) {
                if (! empty($row)) {
                    $bericht = new Bericht();
                    $bericht->setId($row ['id']);
                    $bericht->setBericht_id($row ['bericht_id']);
                    $bericht->setTitel($row ['titel']);
                    $bericht->setDatum($row ['datum']);
                    $bericht->setEindtijd($row ['eindtijd']);
                    $bericht->setMaxpunten($row ['maxpunten']);
                    $bericht->setInhoud($row ['inhoud']);
                    $bericht->setLastupdate($row ['lastupdate']);
                    $bericht->setType($row ['type']);
                    
                    $collection [] = $bericht;
                }
            }
            return $collection;
        }
        
        return false;
    }

    public function getBerichtCollection() {
        global $authMgr;
        $sqlName = 'getBerichtCollection';
        
        $values = array (
                $authMgr->getMyEventId()
            );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! empty($result)) {
            $collection = array ();
            while ( $row = pg_fetch_assoc($result) ) {
                if (! empty($row)) {
                    $bericht = new Bericht();
                    $bericht->setId($row ['id']);
                    $bericht->setEventId($row ['event_id']);
                    $bericht->setBericht_id($row ['bericht_id']);
                    $bericht->setTitel($row ['titel']);
                    $bericht->setDatum($row ['datum']);
                    $bericht->setEindtijd($row ['eindtijd']);
                    $bericht->setMaxpunten($row ['maxpunten']);
                    $bericht->setInhoud($row ['inhoud']);
                    $bericht->setLastupdate($row ['lastupdate']);
                    $bericht->setType($row ['type']);
                    
                    $collection [] = $bericht;
                }
            }
            return $collection;
        }
        
        return false;
    }

    public function getAllRidersSu() {
        global $authMgr;
        $sqlName = 'getAllRidersSu';
        
        $values = array (
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get riders');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $rider = $this->_getRider($row);
            
            $_result [] = $rider;
        }
        return $_result;
    }

    public function getAllRiders() {
        global $authMgr;
        $sqlName = 'getAllRiders';
        
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get riders');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $rider = $this->_getRider($row);
            
            $_result [] = $rider;
        }
        return $_result;
    }

    public function getActiveRiders($deelgebiedName) {
        global $authMgr;
        
        $sqlName = 'getActiveRiders';
        $_result = array ();
        
        // Eerst deelgebied ID vinden
        $deelgebied = $this->getDeelgebiedByName($deelgebiedName);
        if (!$deelgebied) {
            return $_result;
        }
        
        $values = array (
            $deelgebied->getId(),
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if ($result) {
            while ( $row = pg_fetch_assoc($result) ) {
                $rider = $this->_getRider($row);
                $_result [] = $rider;
            }
        }
        return $_result;
    }

    public function getRider($id) {
        global $authMgr;
        $sqlName = 'getRider';
        $values = array (
                $id,
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        while ( $row = pg_fetch_assoc($result) ) {
            $rider = $this->_getRider($row);
            return $rider;
        }
        return null;
    }

    /**
     *
     * @param String $name
     *            username (not displayname!)
     * @return Rider|NULL
     */
    public function getRiderByName($name) {
        $sqlName = 'getRiderByName';
        $values = array (
                $name 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        error_log("RESULT getRiderByName");
        //var_dump($result);
        while ( $row = pg_fetch_assoc($result) ) {
            //var_dump("ROW getRiderByName");
            //var_dump($row);
            $rider = $this->_getRider($row);
            //var_dump("RIDER getRiderByName");
            //var_dump($rider);
            return $rider;
        }
        error_log("ERROR getRiderByName return null");
        return null;
    }

    private function _getRider($row) {
        $user = $this->getUserById($row ['user_id']);
        $deelgebied = $this->getDeelgebiedById($row ['deelgebied_id']);
        $rider = new Rider();
        $rider->setId($row ['id']);
        $rider->setUserId($row ['user_id']);
        $rider->setUser($user);
        
        if ($deelgebied) {
            $rider->setDeelgebied($deelgebied->getName());
        }
        $rider->setVan(strtotime($row ['van']));
        $rider->setTot(strtotime($row ['tot']));
        $rider->setAuto($row ['auto']);
        
        return $rider;
    }

    public function getTotalAmountOfVossenLocations() {
        global $authMgr;
        $sqlName = 'getTotalAmountOfVossenLocations';
        
        $values = array (
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not execute ' . $sqlName);
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['vossencount']);
        }
        return 0;
    }

    public function getVosIncludingLocations($deelgebied) {
        $vos = $this->getVosXYByDeelgebied($deelgebied);
        if (!$vos) {
            return false;
        } else {
            $locations = $this->getLocations($vos);
            $vos->setLocations($locations);
            return $vos;
        }
    }

    public function addVosLocation($deelgebied, $x, $y, $latitude, $longitude, $address, $counterhuntrondje_id, $time = null, $type = 0) {
        if (null == $time) {
            $time = time();
        }
        $vos = $this->getVosXYByDeelgebied($deelgebied);
        $location = new Location();
        $location->setX($x);
        $location->setY($y);
        $location->setLatitude($latitude);
        $location->setLongitude($longitude);
        $location->setAddress($address);
        $location->setDate($time);
        $location->setType($type);
        $location->setCounterhuntrondjeId($counterhuntrondje_id);
        $newLocation = $this->addLocation($vos, $location);
        if ($newLocation) {
            return $this->getLocation($newLocation->getId());
        }
        return false;
    }

    public function getVosXYById($id) {
        return $this->getLocation($id);
    }

    public function getVosXYByDeelgebied($deelgebied) {
        return $this->getTeamByDeelgebiedName($deelgebied);
    }

    /**
     *
     * @param Rider $rider            
     * @throws DatastoreException
     */
    public function updateRider($rider) {
        $sqlName = 'updateRider';

        $deelgebiedId = $rider->getDeelgebied();
        // If, for some reason it's already a number, skip all this
        if (!is_numeric($deelgebiedId)) {
            $deelgebied = $this->getDeelgebiedByName($deelgebiedId);
            if (null != $deelgebied) {
                $deelgebiedId = $deelgebied->getId();
            }
        }
        
        $van = self::psqlDateFromTime($rider->getVan());
        $tot = self::psqlDateFromTime($rider->getTot());
        $values = array (
                $rider->getId(),
                $deelgebiedId,
                $rider->getUser()->getId(),
                //$rider->getBijrijder(),
                //$rider->getTel(),
                $van,
                $tot,
                $rider->getAuto()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not updateRider ' . $rider->getId());
        }
    }

    public function sendStatusChange($vossenTeam) {
        $siteDriver = DataStore::getSiteDriver();
        $allGcmIds = $siteDriver->getAllGcms();
        
        $payload = array (
                'status' => $vossenTeam->getStatus(),
                'teamName' => $vossenTeam->getName() 
        );
        $gcmSender = new GcmSender();
        $gcmSender->setReceiverIds($allGcmIds);
        $gcmSender->setPayload($payload);
        return $gcmSender->send();
    }

    public function getMyTeams() {
        global $authMgr;
        $sqlName = 'getMyTeams';
        $values = array (
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not execute ' . $sqlName);
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $vossenTeam = new VossenTeam();
            $vossenTeam->setId($row ['id']);
            $vossenTeam->setDeelgebied($row ['deelgebied_id']);
            $vossenTeam->setSpeelhelftId(intval($row ['speelhelft_id']));
            $vossenTeam->setName($row ['name']);
            $vossenTeam->setStatus($row ['status']);
            $_result [] = $vossenTeam;
        }
        return $_result;
    }
    
    public function getAllTeams() {
        $sqlName = 'getAllTeams';
        $values = array ();
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not execute ' . $sqlName);
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $vossenTeam = new VossenTeam();
            $vossenTeam->setId($row ['id']);
            $vossenTeam->setDeelgebied($row ['deelgebied_id']);
            $vossenTeam->setSpeelhelftId(intval($row ['speelhelft_id']));
            $vossenTeam->setName($row ['name']);
            $vossenTeam->setStatus($row ['status']);
            $_result [] = $vossenTeam;
        }
        return $_result;
    }
    
    
    public function getAllTeamsCount() {
        $sqlName = 'getAllTeamsCount';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL teams');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['teamcount']);
        }
        return 0;
    }
    public function getTeam($name) {
        return $this->getTeamByName($name);
    }

    public function getTeamByName($name) {
        global $authMgr;
        $sqlName = 'getTeamByName';
        $values = array (
                $name,
                $authMgr->getMyEventId()
        );
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get team ' . $name);
        }
        
        $numRows = pg_num_rows($result);
        if ($numRows > 0) {
            $row = pg_fetch_assoc($result);
            $vossenTeam = new VossenTeam();
            $vossenTeam->setId($row ['id']);
            $vossenTeam->setDeelgebied($row ['deelgebied_id']);
            $vossenTeam->setSpeelhelftId($row ['speelhelft_id']);
            $vossenTeam->setName($row ['name']);
            $vossenTeam->setStatus($row ['status']);
            
            return $vossenTeam;
        } else {
            return false;
        }
    }
    
    public function getTeamById($vossenId) {
        $sqlName = 'getTeamById';
        $values = array (
                $vossenId 
        );
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get team by ID: ' . $vossenId);
        }
        
        $numRows = pg_num_rows($result);
        if ($numRows > 0) {
            $row = pg_fetch_assoc($result);
            $vossenTeam = new VossenTeam();
            $vossenTeam->setId($row ['id']);
            $vossenTeam->setDeelgebied($row ['deelgebied_id']);
            $vossenTeam->setSpeelhelftId(intval($row ['speelhelft_id']));
            $vossenTeam->setName($row ['name']);
            $vossenTeam->setStatus($row ['status']);
            
            return $vossenTeam;
        } else {
            return false;
        }
    }
    
    public function addDeelgebied($deelgebied) {
        global $authMgr;
        $sqlName = 'addDeelgebied';
        
        $values = array (
            $deelgebied->getEventId(),
            $deelgebied->getName(),
            $deelgebied->getLinecolor(),
            $deelgebied->getPolycolor(),
            );
            
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $activecounterhuntrondje = true;
            for ($i=0;$i<4;$i++) {
                $this->addCounterhuntrondje(
                    new CounterhuntRondje(
                        0,
                        $row['id'],
                        1,
                        $i+1,
                        $activecounterhuntrondje)
                );
                $activecounterhuntrondje = false;
            }
            return $row['id'];
        }
        return false;
    }
    
    public function getDeelgebiedByName($deelgebied) {
        global $authMgr;
        $sqlName = 'getDeelgebiedByName';
        $deelgebied = urldecode($deelgebied);
        
        $values = array (
                $deelgebied,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get deelgebied by name: ' . $deelgebied);
        }
        
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Deelgebied($row ['id'], $row ['event_id'], $row ['name'], $row ['linecolor'], $row['polycolor']);
        }
        return false;
    }
    
    public function getDeelgebiedById($deelgebiedId) {
        global $authMgr;
        $sqlName = 'getDeelgebiedById';
        
        $values = array (
                $deelgebiedId,
                $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get deelgebied by ID: ' . $deelgebiedId);
        }
        
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Deelgebied(
                $row ['id'], 
                $row ['event_id'], 
                $row ['name'], 
                $row ['linecolor'], 
                $row['polycolor']);
        }
        return false;
    }
    
    public function getDeelgebiedByIdSu($deelgebiedId) {
        global $authMgr;
        $sqlName = 'getDeelgebiedByIdSu';
        
        $values = array (
                $deelgebiedId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('SU - Could not get deelgebied by ID: ' . $deelgebiedId);
        }
        
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Deelgebied($row ['id'], $row ['event_id'], $row ['name'], $row ['linecolor'], $row['polycolor']);
        }
        return false;
    }
    
    public function getAllDeelgebiedenCount() {
        $sqlName = 'getAllDeelgebiedenCount';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL deelgebieden');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['deelgebiedcount']);
        }
        return 0;
    }

    public function getAllDeelgebieden() {
        global $authMgr;
        return $this->getAllDeelgebiedenForEvent($authMgr->getMyEventId());
    }
    
    public function getAllDeelgebiedenForEvent($event_id) {
        $sqlName = 'getAllDeelgebieden';
        
        $values = array (
            $event_id
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get deelgebieden');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $deelgebied = new Deelgebied($row ['id'], $row ['event_id'], $row ['name'], $row ['linecolor'], $row['polycolor']);
            $_result[] = $deelgebied;
        }
            
        return $_result;
    }
    
    public function removeDeelgebied($deelgebiedId) {
        $sqlName = 'removeDeelgebied';
        $values = array (
                $deelgebiedId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function addCoordinate($coordinate) {
        $sqlName = 'addCoordinate';
        
        $values = array (
                $coordinate->getDeelgebiedId(),
                $coordinate->getLongitude(),
                $coordinate->getLatitude(),
                $coordinate->getOrderId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add hunt, hunter_id:' . $hunter_id . ',code:' . $code . ',vossentracker_id:' . $vossentracker_id);
        }
    }
    public function getAllCoordinatesForDeelgebied($deelgebied_id) {
        $sqlName = 'getAllCoordinatesForDeelgebied';
        
        $values = array (
            $deelgebied_id
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get coordinates');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $coordinate = new Coordinate(
                $row ['id'], 
                $row ['deelgebied_id'], 
                $row ['longitude'],
                $row ['latitude'],
                $row ['order_id']);
            $_result[] = $coordinate;
        }
            
        return $_result;
    }

    public function getTeamByDeelgebiedName($deelgebiedName) {
        $sqlName = 'getTeamByDeelgebiedId';
        
        // Eerst deelgebied ID vinden
        $deelgebied = $this->getDeelgebiedByName($deelgebiedName);
        if (!$deelgebied) {
            return false;
        }
        
        $values = array (
                $deelgebied->getId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get team by deelgebied ' . $deelgebied);
        }
        
        $numRows = pg_num_rows($result);
        if ($numRows > 0) {
            $row = pg_fetch_assoc($result);
            return $this->getTeamByName($row['name']);
        } else {
            return false;
        }
    }
    
    public function getTeamByDeelgebiedId($deelgebiedId) {
        $sqlName = 'getTeamByDeelgebiedId';
        
        $values = array (
                $deelgebiedId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get team by deelgebied ID ' . $deelgebiedId);
        }
        
        $numRows = pg_num_rows($result);
        if ($numRows > 0) {
            $row = pg_fetch_assoc($result);
            return $this->getTeamByName($row['name']);
        } else {
            return false;
        }
    }

    public function addTeam($vossenteam) {
        $sqlName = 'addTeam';
        $values = array (
            $vossenteam->getDeelgebied(),
            $vossenteam->getSpeelhelftId(),
            $vossenteam->getName(),
            $vossenteam->getStatus()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add team');
        }
        
        return true;
    }
    
    public function updateTeam($vossenteam) {
        $sqlName = 'updateTeam';
        $values = array (
                $vossenteam->getId(),
                $vossenteam->getName(),
                $vossenteam->getDeelgebied(),
                $vossenteam->getStatus(),
                $vossenteam->getSpeelhelftId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        } else {
            return true;
        }
    }

    public function removeTeam($vossenId) {
        $sqlName = 'removeTeam';
        $values = array (
                $vossenId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        } else {
            return true;
        }
    }
    
    public function getLocations($team) {
        global $authMgr;
        $sqlName = 'getLocations';
        
        $values = array (
                $team->getId(),
                $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $loc = new Location();
            $loc->setId($row ['id']);
            $loc->setLongitude($row ['longitude']);
            $loc->setLatitude($row ['latitude']);
            $loc->setX($row ['x']);
            $loc->setY($row ['y']);
            $loc->setDate($row ['time']);
            $loc->setType(intval($row ['type']));
            $loc->setRiderId(intval($row ['hunter_id']));
            $loc->setAddress($row ['adres']);
            $loc->setCounterhuntRondjeId(intval($row ['counterhuntrondje_id']));
            $_result [] = $loc;
        }
        return $_result;
    }

    public function getLocation($id) {
        $sqlName = 'getLocation';
        $values = array (
                $id 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $loc = new Location();
            $loc->setId($row ['id']);
            $loc->setVossenId($row ['vossen_id']);
            $loc->setLongitude($row ['longitude']);
            $loc->setLatitude($row ['latitude']);
            $loc->setX($row ['x']);
            $loc->setY($row ['y']);
            $loc->setDate($row ['time']);
            $loc->setType(intval($row ['type']));
            $loc->setRiderId(intval($row ['hunter_id']));
            $loc->setAddress($row ['adres']);
            $loc->setCounterhuntRondjeId($row ['counterhuntrondje_id']);
            return $loc;
        }
        return null;
    }
    // Add the location to the database, update the vossenteam
    // Returns the number of rows updated (should be 1, I guess?)
    public function addLocation($vossenteam, $location) {
        global $authMgr;
        
        if (null == $location->getDate()) {
            $location->setDate(time());
        }
        
        $sqlName = 'addLocation';
        $values = array (
                $vossenteam->getId(),
                $location->getX(),
                $location->getY(),
                $location->getLongitude(),
                $location->getLatitude(),
                $location->getAddress(),
                $location->getType(),
                date('r', $location->getDate()),
                $authMgr->getMyOrganisationId(),
                $location->getCounterhuntrondjeId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            return $this->getLocation($row ['id']);
        }
        
        // If all else fails
        return pg_affected_rows($result);
    }

    public function removeVossenLocation($id) {
        $sqlName = 'removeVossenLocation';
        $values = array (
                $id 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        return pg_affected_rows($result);
    }

    public function getRiderLocationGraph($riderId) {
        $sqlName = 'getRiderLocationGraph2';
        $values = array (
                $riderId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = array (
                    'hour_slice' => $row ['hour_slice'],
                    'points' => $row ['running_ct'] 
            );
        }
        return $_result;
    }
    
    public function getAllUsersSuperAdmin() {
        $sqlName = 'authAllUsersSU';
        $values = array (
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new User($row ['id'], $row ['username'], $row ['displayname'], $row ['pw_hash']);
        }
        return $_result;
    }

    public function getAllUsers() {
        global $authMgr;
        $sqlName = 'authAllUsers';
        $values = array (
            $authMgr->getMyOrganisationId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new User($row ['id'], $row ['username'], $row ['displayname'], $row ['pw_hash']);
        }
        return $_result;
    }

    /**
     * Returns a User
     */
    public function login($username, $password) {
        $sqlName = 'authLogin';
        error_log("[SiteDriverPostgresql->login] DEBUG username " . $username);
        $values = array (
                $username
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            error_log("[SiteDriverPostgresql->login] ERROR Query failed");
            return false;
        }
        
        if (pg_num_rows($result) == 1) {
            error_log("[SiteDriverPostgresql->login] INFO User " . $username . " found");
            $row = pg_fetch_assoc($result);
            // Verify password
            if (password_verify($password, $row ['pw_hash'])) {
                return new User($row ['id'], $row ['username'], $row ['displayname'], $row ['pw_hash']);
            }
        }
        error_log("[SiteDriverPostgresql->login] ERROR User " . $username . " not found");
        return false;
    }

    public function addSessionId($sessionId, $user, $organisation) {
        if (null == $user) {
            return false;
        }
        $sqlName = 'authAddSessionId';
        $values = array (
                $sessionId,
                $user->getId(),
                $organisation ? $organisation->getId() : NULL,
                NULL
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function updateSession($session) {
        $sqlName = 'authUpdateSession';
        
        $values = array (
                $session->getSessionId(),
                $session->getEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $gcm) {
                throw new DatastoreException('Could not update gcm ' . $gcm->toArray());
            }
            throw new DatastoreException('Could not update gcm (gcm === null)');
        }
    }

    public function removeSessionId($sessionId) {
        $sqlName = 'authRemoveSessionId';
        $values = array (
                $sessionId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function removeAllSessionIdsForUserId($sessionId) {
        $sqlName = 'authRemoveAllSessionIdsForUserId';
        $values = array (
                $sessionId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }

    public function getGroupsOfUser($sessionId) {
        $user = $this->getUser($sessionId);
        if (! $user) {
            return false;
        }
        return $this->getGroupsOfUserViaUser($user);
    }

    public function getGroupsOfUserViaUser($user) {
        $sqlName = 'authGroupsByUserId';
        $values = array (
                $user->getId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Group($row ['id'], $row ['name']);
        }
        return $_result;
    }

    public function addUser($user) {
        $sqlName = 'addUser';
        $values = array (
                $user->getUsername(),
                $user->getDisplayName(),
                $user->getPwHash(),
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return $this->getUserById($row['id']);
        }
        return false;
    }

    public function updateUser($user) {
        $sqlName = 'updateUser';
        $values = array (
                $user->getId(),
//                $user->getUsername(),
                $user->getDisplayName(),
                $user->getPwHash(),
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function removeUser($userId) {
        $result = $this->removeAllGroupsOfUser($userId);
        if (!$result) {
            return false;
        }
        
        $result = $this->removeUserFromAllOrganisations($userId);
        if (!$result) {
            return false;
        }
        
        $result = $this->removeRiderViaUserId($userId);
        if (!$result) {
            return false;
        }
        
        $result = $this->removeAllSessionIdsForUserId($userId);
        if (!$result) {
            return false;
        }

        $sqlName = 'removeUser';
        $values = array (
                $userId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function removeAllGroupsOfUser($userId) {
        $sqlName = 'removeAllGroupsOfUser';
        $values = array (
                $userId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function removeUserFromAllOrganisations($userId) {
        $sqlName = 'removeUserFromAllOrganisations';
        $values = array (
                $userId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }

    public function addUserToGroup($userId, $groupId) {
        $sqlName = 'addUserToGroup';
        $values = array (
                $userId,
                $groupId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function addUserToOrganisation($userId, $organisationId) {
        $sqlName = 'addUserToOrganisation';
        $values = array (
                $userId,
                $organisationId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function getOrganisationForUser($user) {
        $sqlName = 'getOrganisationForUser';
        $values = array (
                $user->getId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Organisation($row['id'], $row['name']);
        }

        return false;
    }
    
    public function addOrganisation($organisation) {
        $sqlName = 'addOrganisation';
        $values = array (
                $organisation->getName()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return $this->getOrganisationById($row['id']);
        }
        return false;
    }
    
    public function updateOrganisation($organisation) {
        $sqlName = 'updateOrganisation';
        $values = array (
            $organisation->getId(),
            $organisation->getName()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return $this->getOrganisationById($row['id']);
        }
        return false;
    }
    
    public function getOrganisationById($organisationId) {
        $sqlName = 'getOrganisationById';
        $values = array (
            $organisationId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            return new Organisation($row ['id'], $row ['name']);
        }
        return false;
    }

    public function getAllOrganisationsCount() {
        $sqlName = 'getAllOrganisationsCount';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL organisations');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['organisationscount']);
        }
        return 0;
    }
    
    public function getAllOrganisations() {
        $sqlName = 'getAllOrganisations';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Organisation($row ['id'], $row ['name']);
        }
        return $_result;
    }
    
    public function removeOrganisation($organisationId) {
        $sqlName = 'removeOrganisation';
        $values = array (
                $organisationId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    
    public function addOrganisationToEvent($eventId, $organisationId) {
        $sqlName = 'addOrganisationToEvent';
        $values = array (
                $eventId,
                $organisationId
                
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function removeOrganisationFromEvent($eventId, $organisationId) {
        $sqlName = 'removeOrganisationFromEvent';
        $values = array (
                $eventId,
                $organisationId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }

    public function getSessionInformation($sessionId) {
        $sqlName = 'authGetSessionInformation';
        $values = array (
                $sessionId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Session($row ['session_id'], $row ['user_id'], $row ['organisation_id'], $row ['event_id']);
        }
        return false;
    }

    public function getUser($sessionId) {
        $sqlName = 'authUserBySession';
        $values = array (
                $sessionId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new User($row ['id'], $row ['username'], $row ['displayname'], $row ['pw_hash']);
        }
        return false;
    }

    public function getUserById($userId) {
        // XXX Protect
        $sqlName = 'authUserById';
        $values = array (
                $userId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new User($row ['id'], $row ['username'], $row ['displayname'], $row ['pw_hash']);
        }
        return false;
    }
    
    public function getEventById($eventId) {
        $sqlName = 'getEventById';
        $values = array (
                $eventId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            return new Event($row['id'], $row['name'], $row['public'], $row['starttime'], $row['endtime']);
        }
        return false;
    }

    public function getAllEventsCount() {
        $sqlName = 'getAllEventsCount';
        $values = array (
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL events');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['eventcount']);
        }
        return 0;
    }
    
    public function getAllEvents() {
        $sqlName = 'getAllEvents';
        $values = array (
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Event($row['id'], $row['name'], $row['public'], $row['starttime'], $row['endtime']);
        }
        return $_result;
    }
    
    public function getEventsForOrganisation($organisationId) {
        global $authMgr;
        $sqlName = 'getMyEvents';
        $values = array (
            $organisationId
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Event($row['id'], $row['name'], $row['public'], $row['starttime'], $row['endtime']);
        }
        return $_result;
    }
    
    public function getMyEvents() {
        global $authMgr;
        return $this->getEventsForOrganisation($authMgr->getMyOrganisationId());
    }
    
    public function addEvent($event) {
        $sqlName = 'addEvent';
        
        $values = array (
                $event->getName(),
                $event->isPublic(),
                $event->getStarttime() != "" ? $event->getStarttime() : null,
                $event->getEndtime() != "" ? $event->getEndtime() : null
        );        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add event, name:' . $event->getName() .
                        ',public:' . $event->isPublic() . 
                        ',starttime:' . $event->getStarttime() .
                        ',endtime:' . $event->getEndtime());
        }

        $row = pg_fetch_assoc($result);
        if ($row === false) {
            throw new DatastoreException('Result is NOT a Event ID, something went wrong :(');
        }
        
        return intval($row ['id']);
    }
    
    public function removeEvent($eventId) {
        $sqlName = 'removeEvent';
        $values = array (
                $eventId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function updateEvent($event) {
        $sqlName = 'updateEvent';
        
        $values = array (
                $event->getId(),
                $event->getName(),
                $event->isPublic(),
                $event->getStarttime(),
                $event->getEndtime() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $event) {
                throw new DatastoreException('Could not update event ' . $event->toArray());
            }
            throw new DatastoreException('Could not update event (event === null)');
        }
    }
    
    public function getAllSpeelhelften() {
        $sqlName = 'getAllSpeelhelften';
        $values = array (
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Speelhelft($row['id'], $row['event_id'], $row['starttime'], $row['endtime']);
        }
        return $_result;
    }
    
    public function getAllSpeelhelftenCount() {
    $sqlName = 'getAllSpeelhelftenCount';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL speelhelften');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['speelhelftcount']);
        }
        return 0;
    }
    
    public function getAllSpeelhelftenForEvent($eventId) {
        $sqlName = 'getAllSpeelhelftenForEvent';
        $values = array (
            $eventId
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result [] = new Speelhelft($row['id'], $row['event_id'], $row['starttime'], $row['endtime']);
        }
        return $_result;
    }
    
    public function getSpeelhelftById($speelhelftId) {
        $sqlName = 'getSpeelhelftById';
        $values = array (
            $speelhelftId
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }

        $_result = array ();
        if ($row = pg_fetch_assoc($result)) {
            return new Speelhelft($row['id'], $row['event_id'], $row['starttime'], $row['endtime']);
        }
        return false;
    }
    
    public function addSpeelhelft($speelhelft) {
        $sqlName = 'addSpeelhelft';
        
        $values = array (
                $speelhelft->getEventId(),
                $speelhelft->getStarttime() != "" ? $speelhelft->getStarttime() : null,
                $speelhelft->getEndtime() != "" ? $speelhelft->getEndtime() : null
        );        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not add speelhelft, 
                          event_id:' . $speelhelft->getEventId() .
                        ',starttime:' . $speelhelft->getStarttime() .
                        ',endtime:' . $speelhelft->getEndtime());
        }
        return $result;
    }

    public function removeSpeelhelft($speelhelftId) {
        $sqlName = 'removeSpeelhelft';
        $values = array (
                $speelhelftId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function updateSpeelhelft($speelhelft) {
        $sqlName = 'updateSpeelhelft';
        
        $values = array (
                $speelhelft->getId(),
                $speelhelft->getEventId(),
                $speelhelft->getStarttime(),
                $speelhelft->getEndtime() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $speelhelft) {
                throw new DatastoreException('Could not update speelhelft ' . $speelhelft->toArray());
            }
            throw new DatastoreException('Could not update $speelhelft (speelhelft === null)');
        }
    }

    public function removePoi($poiId) {
        $sqlName = 'removePoi';
        $values = array (
                $poiId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function getAllPoisSu() {
        global $authMgr;
        $sqlName = 'getAllPoisSu';
        $values = array (
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all pois (SU)');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $poi = new Poi(
                            $row ['id'],
                            $row ['event_id'],
                            $row ['name'],
                            $row ['data'],
                            $row ['latitude'],
                            $row ['longitude'],
                            $row ['type']);
            $_result [] = $poi;
        }
        return $_result;
    }
    
    public function getAllPois() {
        global $authMgr;
        $sqlName = 'getAllPois';
        $values = array (
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all pois');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $poi = new Poi(
                            $row ['id'],
                            $row ['event_id'],
                            $row ['name'],
                            $row ['data'],
                            $row ['latitude'],
                            $row ['longitude'],
                            $row ['type']);
            
            $_result [] = $poi;
        }
        return $_result;
    }
    
    public function addPoi($poi) {
        global $authMgr;
        $sqlName = 'addPoi';
        
        $values = array (
            $poi->getEventId(),
            $poi->getName(),
            $poi->getData(),
            $poi->getLatitude(),
            $poi->getLongitude(),
            $poi->getType()
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

    public function updatePoi($poi) {
        $sqlName = 'updatePoi';
        
        $values = array (
                $poi->getId(),
                $poi->getEventId(),
                $poi->getName(),
                $poi->getData(),
                $poi->getLatitude(),
                $poi->getLongitude(),
                $poi->getType() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $poi) {
                throw new DatastoreException('Could not update poi ' . $poi->toArray());
            }
            throw new DatastoreException('Could not update poi (poi === null)');
        }
    }

    public function getPoiById($poiId) {
        global $authMgr;
        $sqlName = 'getPoiById';
        $values = array (
            $authMgr->getMyEventId(),
            $poiId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get Poi by ID:' . $poiId);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $poi = new Poi(
                        $row ['id'],
                        $row ['event_id'],
                        $row ['name'],
                        $row ['data'],
                        $row ['latitude'],
                        $row ['longitude'],
                        $row ['type']);
            return $poi;
        }
        return null;
    }
    
    public function getAllCounterhuntrondjesCount() {
        $sqlName = 'getAllCounterhuntrondjesCount';
        $values = array ();
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        
        if (! $result) {
            throw new DatastoreException('Could not get total amount of ALL counterhuntrondjes');
        }
        while ( $row = pg_fetch_assoc($result) ) {
            return intval($row ['count']);
        }
        return 0;
    }
    
    public function getAllCounterhuntrondjesSu() {
        global $authMgr;
        $sqlName = 'getAllCounterhuntrondjesSu';
        $values = array (
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get all pois');
        }
        
        $_result = array ();
        while ( $row = pg_fetch_assoc($result) ) {
            $counterhuntrondje = new CounterhuntRondje(
                        $row ['id'],
                        $row ['deelgebied_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['active']
                        );
            
            $_result [] = $counterhuntrondje;
        }
        return $_result;
    }
    
    public function getCounterhuntrondjeByIdSu($counterhuntrondje_id) {
        global $authMgr;
        $sqlName = 'getCounterhuntrondjeByIdSu';

        $values = array (
            $counterhuntrondje_id
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get CounterhuntRondje by DeelgebiedName:' . $deelgebiedName);
        }
        
        $_result = array();
        while ( $row = pg_fetch_assoc($result) ) {
            $counterhuntrondje = new CounterhuntRondje(
                        $row ['id'],
                        $row ['deelgebied_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['active']
                        );
            return $counterhuntrondje;
        }
        return null;
    }
    
    public function updateCounterhuntrondje($counterhuntRondje) {
        $sqlName = 'updateCounterhuntrondje';
        
        $values = array (
                $counterhuntRondje->getId(),
                $counterhuntRondje->getDeelgebiedId(),
                $counterhuntRondje->getOrganisationId(),
                $counterhuntRondje->getName(),
                $counterhuntRondje->getActive()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $image) {
                throw new DatastoreException('Could not update counterhuntrondje ' . $counterhuntRondje->toArray());
            }
            throw new DatastoreException('Could not update counterhuntrondje ($counterhuntRondje === null)');
        }
    }
    
    public function addCounterhuntrondje($counterhuntrondje) {
        global $authMgr;
        $sqlName = 'addCounterhuntrondje';
        
        $enabled = ($counterhuntrondje->getActive() ? 't' : 'f');
        $values = array (
            $counterhuntrondje->getDeelgebiedId(),
            $counterhuntrondje->getOrganisationId(),
            $counterhuntrondje->getName(),
            $enabled,
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
    
    public function removeCounterhuntrondje($counterhuntrondjeId) {
        $sqlName = 'removeCounterhuntrondje';
        $values = array (
                $counterhuntrondjeId 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        if (! $result) {
            return false;
        }
        return true;
    }
    
    public function getActiveCounterhuntRondje($deelgebiedName) {
        global $authMgr;
        $sqlName = 'getActiveCounterhuntRondjeByDeelgebiedName';

        $values = array (
            $deelgebiedName,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get Active CounterhuntRondje by DeelgebiedName:' . $deelgebiedName);
        }
        
        if ( $row = pg_fetch_assoc($result) ) {
            return new CounterhuntRondje(
                        $row ['id'],
                        $row ['deelgebied_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['active']
                        );
        }
        return null;
    }
    
    public function getCounterhuntrondjeForDeelgebied($deelgebiedName) {
        global $authMgr;
        $sqlName = 'getCounterhuntrondjeForDeelgebiedByName';

        $values = array (
            $deelgebiedName,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );

        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get CounterhuntRondje by DeelgebiedName:' . $deelgebiedName);
        }
        
        $_result = array();
        while ( $row = pg_fetch_assoc($result) ) {
            $_result[] = new CounterhuntRondje(
                        $row ['id'],
                        $row ['deelgebied_id'],
                        $row ['organisation_id'],
                        $row ['name'],
                        $row ['active']
                        );
        }
        return $_result;
    }

    public function setActiveCounterhuntrondjeId($deelgebiedName, $counterhuntrondjeId) {
        global $authMgr;
        $sqlName = 'setActiveCounterhuntrondjeId';
        
        $values = array (
            $counterhuntrondjeId,
            $deelgebiedName,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );

        $this->setAllCounterhuntrondjesAsInactive($deelgebiedName);
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException(
                'Could not set active counterhuntrondje to ' . $counterhuntrondjeId
                . ' for Deelgebied ' . $deelgebiedName);
        }
    }
    
    private function setAllCounterhuntrondjesAsInactive($deelgebiedName) {
        global $authMgr;
        $sqlName = 'setAllCounterhuntrondjesAsInactive';
        
        $values = array (
            $deelgebiedName,
            $authMgr->getMyOrganisationId(),
            $authMgr->getMyEventId()
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException(
                'Could not remove active flag counterhuntrondje for Deelgebied ' . $deelgebiedName);
        }
    }

    /**
     * Public static functions, not part of the queries (but may be used by 'm)
     */
    public static function psqlDateFromTime($time) {
        $dateFormat = 'Y-m-d H:i:s';
        return date($dateFormat, $time);
    }
    
    public function imageGetBySha($imageSha) {
        $sqlName = 'imageGetBySha';
        
        $values = array ($imageSha);
        
        $result = pg_execute($this->conn, $sqlName, $values);
   
        if (!empty($result) && $row = pg_fetch_assoc($result) ) {
            $image = new Image();
            $image->addValuesfromArray($row);
            return $image;
        }
        
        return false;
    }
    
    public function addImage(Image $image) {
        global $authMgr;
        $sqlName = 'addImage';
        
        $values = array (
            $image->getEncodedData(),
            $image->getName(),
            $image->getExtension(),
            $image->getSha1(),
            $image->getFileSize(),
            $image->getLastModified()
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

    public function updateImage($image) {
        $sqlName = 'updateImage';
        
        $values = array (
                $image->getId(),
                $image->getEncodedData(),
                $image->getName(),
                $image->getExtension(),
                $image->getSha1(),
                $image->getFileSize(),
                $image->getLastModified() 
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            if (null !== $image) {
                throw new DatastoreException('Could not update image ' . $image->toArray());
            }
            throw new DatastoreException('Could not update image (image === null)');
        }
    }

    public function getImageById($imageId) {
        $sqlName = 'getImageById';
        $values = array (
            $imageId
        );
        
        $result = pg_execute($this->conn, $sqlName, $values);
        
        if (! $result) {
            throw new DatastoreException('Could not get Image by ID:' . $imageId);
        }
        
        while ( $row = pg_fetch_assoc($result) ) {
            $row['data'] = pg_unescape_bytea($row['data']);
            $image = new Image();
            $image->addValuesfromArray($row);
            return $image;
        }
        return null;
    }
}
