<?php

class Request {
    private $request_vars;
    private $data;
    private $http_accept;
    private $method;
    private $authCode;

    public function __construct() {
        $this->request_vars = array ();
        $this->data = '';
        $this->http_accept = (isset($_SERVER ['HTTP_ACCEPT']) && strpos($_SERVER ['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml';
        $this->method = 'get';
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function setMethod($method) {
        $this->method = $method;
    }

    public function setRequestVars($request_vars) {
        $this->request_vars = $request_vars;
    }

    public function getData() {
        return $this->data;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setHttpAccept($httpAccept) {
        $this->http_accept = $httpAccept;
    }

    public function getHttpAccept() {
        return $this->http_accept;
    }

    public function getRequestVars() {
        return $this->request_vars;
    }

    public function getAuthCode() {
        return $this->authCode;
    }
    
    /**
     * Only to be called through AuthMgr!
     * @param string $sessionId
     */
    public function setAuthCode($sessionId) {
        $this->authCode = $sessionId;
    }

    public function isApiCall() {
        $requestVars = self::getRequestVars();
        return $requestVars ['apiCall'];
    }

    public function getApiClass() {
        $requestVars = self::getRequestVars();
        return $requestVars ['apiClass'];
    }

    public function getApiParts() {
        $requestVars = self::getRequestVars();
        return $requestVars ['apiParts'];
    }
    
    public function isPreFlightCall() {
        if ($this->getMethod() == 'options') {
            return true;
        }
        return false;
    }
}
?>