<?php
require_once CLASS_DIR . 'jotihunt/Gcm.class.php';

class GcmSender {
    private $googleUri = 'https://fcm.googleapis.com/fcm/send';
    private $googleApiKey;
    private $proximoUser;
    private $proximoPass;
    private $proximoHost;
    private $receiverIds = array ();
    private $payload = array ();
    
    function __construct() {
        if (GOOGLE_GCM_ENABLED) {
            $this->googleApiKey = GOOGLE_GCM_API_KEY;
        }
        if ($this->useProxy()) {
            $this->proximoUser = PROXIMO_USER;
            $this->proximoPass = PROXIMO_PASS;
            $this->proximoHost = PROXIMO_HOST;
        }
    }

    public function setReceiverIds($receiverIds) {
        $this->receiverIds = array ();
        foreach ( $receiverIds as $gmc ) {
            $this->receiverIds [] = $gmc->getGcmId();
        }
    }

    public function setPayload($payload = array()) {
        $this->payload = $payload;
    }

    public function send() {

        if (defined('GOOGLE_GCM_DEBUG_KEY')) {
            $this->receiverIds = array ();
            $this->receiverIds [] = GOOGLE_GCM_DEBUG_KEY;
        }

        if (sizeof($this->receiverIds) == 0) {
            return 'No receiverIds, nothing to send, cancelling GCM request';
        }
        if (strlen($this->googleApiKey) === 0) {
            return 'No GCM API key set';
        }
        $data = array (
                'registration_ids' => $this->receiverIds 
        );
        $allData = array_merge($data, array (
                'data' => $this->payload 
        ));
        $postdata = json_encode($allData);
        
        $optsArr = array ();
        $requestHeaders = array (
                'Authorization: key=' . $this->googleApiKey,
                sprintf('Content-Length: %d', strlen($postdata)),
                'Content-type: application/json' 
        );
        
        if ($this->useProxy()) {
            $auth = base64_encode($this->proximoUser . ':' . $this->proximoPass);
            $requestHeaders = array_merge($requestHeaders, array (
                    'Proxy-Authorization: Basic ' . $auth 
            ));
            
            $optsArr = array_merge($optsArr, array (
                    'request_fulluri' => true,
                    'proxy' => 'tcp://' . $this->proximoHost 
            ));
        }
        
        $optsArr = array_merge($optsArr, array (
                'method' => 'POST',
                'header' => implode("\n", $requestHeaders),
                'content' => $postdata 
        ));
        
        $opts = array (
                'http' => $optsArr 
        );
        $context = stream_context_create($opts);
        
        $use_include_path = false;
        $result = file_get_contents($this->googleUri, $use_include_path, $context);
        
        return $result;
    }

    private function useProxy() {
        return PROXIMO_ENABLED;
    }
}