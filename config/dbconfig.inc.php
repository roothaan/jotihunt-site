<?php

function parseDbUrl($dbUrl) {
    if (defined('DB_DEFINED')) return;
    $dbopts = parse_url($dbUrl);
    define('DB_DEFINED', true);
    define('DB_TYPE', 'postgresql');
    define('DB_SERVER', $dbopts['host']);
    define('DB_DATABASE', ltrim($dbopts['path'],'/'));
    define('DB_USERNAME', $dbopts['user']);
    define('DB_PASSWORD', $dbopts['pass']);
    define('DB_PORT', $dbopts['port']);
    define('DB_OPTS', getenv('DATABASE_OPTIONS') ? getenv('DATABASE_OPTIONS') : '');
}

function setDbByParsingEnv() {
    if (getenv('DATABASE_URL')) {
        $dbopts = getenv('DATABASE_URL');
        parseDbUrl($dbopts);
    }
}

setDbByParsingEnv();
?>