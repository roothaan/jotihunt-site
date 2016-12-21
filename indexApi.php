<?php
require_once 'config.inc.php';

require_once CLASS_DIR . 'request/RequestParser.class.php';
require_once CLASS_DIR . 'request/RequestHandler.class.php';

$requestParser = new RequestParser();
$request = $requestParser->parseRequest();

$requestHandler = new RequestHandler();
$requestHandler->handleRequest($request);

?>