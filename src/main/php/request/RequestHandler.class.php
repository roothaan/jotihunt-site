<?php
require_once CLASS_DIR . 'request/Request.class.php';
require_once CLASS_DIR . 'user/AuthMgr.class.php';

class RequestHandler {
    private $request;
    private $apiClass;

    public function __construct() {
        spl_autoload_register('RequestHandler::autoload');
    }

    public function handleRequest($request) {
        $this->request = $request;

        if ($request->isPreFlightCall()) {
            error_log("[RequestHandler->handleRequest] PREFLIGHT call w/ OPTIONS");
            header('HTTP/1.1 200 OK');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: authenticationToken, authenticationUsername, authenticationPassword, Content-Type');
            header('Access-Control-Max-Age: 86400');
            header('Content-Length: 0');
            return;
        }
        if ($request->isApiCall()) {
            header('Access-Control-Allow-Origin: *');
            global $authMgr;
            $authMgr = new AuthMgr();
            $newSessionId = $authMgr->loginViaAPI($request);
            if (null != $newSessionId) {
                error_log("[RequestHandler->handleRequest] INFO new token received: " . $newSessionId);
                header('HTTP/1.1 200 OK');
                header('Content-type: application/json');
                print json_encode(array (
                        'token' => $newSessionId 
                ));
                return;
            }
            if (! $authMgr->attemptAuthViaAPI($request)) {
                error_log("[RequestHandler->handleRequest] FATAL auth failed");
                header('HTTP/1.1 401 Unauthorized');
                header('Content-type: application/json');
                print json_encode(array (
                        'error' => 'authentication token missing' 
                ));
                return;
            }
            $classname = self::getFullClassName($request->getApiClass());
            
            if (! class_exists($classname)) {
                header('HTTP/1.1 501 Not Implemented');
                header('Content-type: application/json');
                print json_encode(array (
                        'error' => 'unknown class [' . $classname . ']' 
                ));
                return;
            }
            
            $this->apiClass = new $classname();
            $this->apiClass->setRequest($request);
            
            spl_autoload_unregister('RequestHandler::autoload');
            switch ($request->getMethod()) {
                case 'get' :
                    $this->handleGet();
                break;
                case 'post' :
                    $this->handlePost();
                break;
                case 'delete' :
                    $this->handleDelete();
                break;
                default :
                    header('HTTP/1.0 501 Not Implemented');
                    print json_encode(array (
                            'error' => 'unknown method [' . $request->getMethod() . ']' 
                    ));
                break;
            }
        }
    }

    private function handlePost() {
        $result = $this->apiClass->doPost();
        
        $this->request->setHttpAccept('json');
        header('Content-type: application/json');
        print json_encode($result);
    }

    private function handleDelete() {
        $result = $this->apiClass->doDelete();
        
        $this->request->setHttpAccept('json');
        header('Content-type: application/json');
        print json_encode($result);
    }

    private function handleGet() {
        $result = $this->apiClass->doGet();
        
        $this->request->setHttpAccept('json');
        switch ($this->request->getHttpAccept()) {
            // case 'xml' :
            // header('Content-type: text/xml');
            // print self::asXML($result, $request->getApiClass());
            // break;
            case 'json' :
                header('Content-type: application/json');
                print json_encode($result);
            break;
            case 'html' :
                print '<pre>';
                var_dump($result);
                print '</pre>';
            break;
            default :
                print 'HttpAccept ' . $this->request->getHttpAccept() . ' unknown';
        }
    }

    private function getFullClassName($className) {
        return ucfirst(strtolower($className)) . 'Api';
    }

    public function autoload($classname) {
        $filename = CLASS_DIR . 'api/' . $classname . '.class.php';
        if (is_file($filename)) {
            require_once $filename;
        }
    }

    private function asXml($array, $elementName) {
        // creating object of SimpleXMLElement
        $xml = new SimpleXMLElement('<?xml version="1.0"?><' . $elementName . '></' . $elementName . '>');
        
        // function call to convert array to xml
        self::toXml($array, $xml);
        
        return $xml->asXml();
    }
    
    // function defination to convert array to xml
    private function toXml($result, &$xml) {
        foreach ( $result as $key => $value ) {
            if (is_array($value)) {
                if (! is_numeric($key)) {
                    $subnode = $xml->addChild($key);
                    self::toXml($value, $subnode);
                } else {
                    $subnode = $xml->addChild('item' . $key);
                    self::toXml($value, $subnode);
                }
            } else {
                $xml->addChild($key, $value);
            }
        }
    }
}
?>