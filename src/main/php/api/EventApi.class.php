<?php
require_once CLASS_DIR . 'api/ApiException.class.php';
require_once CLASS_DIR . 'datastore/Datastore.class.php';
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';

class EventApi {
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
            $allEvents = $this->siteDriver->getMyEvents();
            
            $result = array ();
            foreach ( $allEvents as $event ) {
                $result [] = $event->toArray();
            }
            
            return $result;
        } else if ($this->requestedId == 'me') {
            $event = $this->siteDriver->getEventById($authMgr->getMyEventId());
            if ($event) {
                return $event->toArray();
            }
            header("HTTP/1.0 404 Not Found");
            return array (
                    'reason' => 'No event set',
                    'success' => false);
        } else if ($this->requestedId == 'set') {
            return $this->doPost();
        } else {
            $event = $this->siteDriver->getEventById($this->requestedId);
            return $event->toArray();
        }
    }

    public function doPost() {
        global $authMgr;
        $this->init();
        
        $data = $this->request->getRequestVars();
        $sessionCreated = false;

        if (isset($data['eventId'])) {
            $currentSession = $authMgr->getSessionInformation();
            $newSession = new Session($currentSession->getSessionId(), $currentSession->getUserId(), $currentSession->getOrganisationId(), $data['eventId']);
            $this->siteDriver->updateSession($newSession);
            $sessionCreated = true;
        }

        // Check for existing ID
        if ($sessionCreated) {
            header("HTTP/1.0 201 Created");
            return array (
                    'sessionId' => $currentSession->getSessionId(),
                    'eventId' => $data['eventId'],
                    'success' => true 
            );
        }
        header("HTTP/1.0 500 Internal Server Error");
        return array (
                'success' => false 
        );
    }
}

?>