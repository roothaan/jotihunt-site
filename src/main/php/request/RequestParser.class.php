<?php
require_once CLASS_DIR . 'request/Request.class.php';

class RequestParser {

    public function parseRequest() {
        $request = new Request();
        
        // we'll store our data here
        $data = array ();
        
        // get our verb
        $request_method = strtolower($_SERVER ['REQUEST_METHOD']);
        
        switch ($request_method) {
            // gets are easy...
            case 'get' :
                $data = $_GET;
            break;
            // so are posts
            case 'post' :
                $data = $_POST;
            break;
            case 'delete' :
            case 'put' :
                // basically, we read a string from PHP's special input location,
                // and then parse it out into an array via parse_str... per the PHP docs:
                // Parses str as if it were the query string passed via a URL and sets
                // variables in the current scope.
                parse_str(file_get_contents('php://input'), $put_vars);
                $data = $put_vars;
            break;
        }
        
        // API Parsing
        $_parts = explode('/', $_SERVER ['REQUEST_URI']);
        
        $apiCall = false;
        $partsCounter = 0;
        $parts = array ();
        for($i = 0; $i < count($_parts); $i ++) {
            // skip until we reach 'api'
            if ($apiCall) {
                $part = $_parts [$i];
                if ($part !== null && strlen($part) > 0) {
                    $parts [$partsCounter ++] = $part;
                    continue;
                }
            }
            
            if ($_parts [$i] == 'api') {
                $apiCall = true;
            }
        }
        
        $data ['apiCall'] = $apiCall;
        if ($apiCall) {
            $data ['apiClass'] = array_shift($parts);
            $data ['apiParts'] = $parts;
        }
        // END API Parsing
        
        // store the method
        $request->setMethod($request_method);
        
        // set the raw data, so we can access it if needed (there may be
        // other pieces to your requests)
        $request->setRequestVars($data);
        
        if (isset($data ['data'])) {
            // translate the JSON to an Object for use however you want
            $request->setData(json_decode($data ['data']));
        }
        
        return $request;
    }
}
?>