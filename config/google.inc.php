<?php
/**
 * Used for Google Maps, GCM, etc
 */
if (getenv('GOOGLE_JS_API_KEY')) {
    define('GOOGLE_MAPS_ENABLED', true);
    define('GOOGLE_JS_API_KEY', getenv('GOOGLE_JS_API_KEY'));
    define('GOOGLE_MAPS_URI', '//maps.google.com/maps/api/js?key='.GOOGLE_JS_API_KEY.'&region=NL');
} else {
    define('GOOGLE_MAPS_ENABLED', false);
}

if (getenv('GOOGLE_GCM_API_KEY')) {
    define('GOOGLE_GCM_ENABLED', true);
    define('GOOGLE_GCM_API_KEY', getenv('GOOGLE_GCM_API_KEY'));
} else {
    define('GOOGLE_GCM_ENABLED', false);
}

if (getenv('GOOGLE_GCM_DEBUG_KEY')) {
    define('GOOGLE_GCM_DEBUG_KEY', getenv('GOOGLE_GCM_DEBUG_KEY'));
}

if (getenv('GOOGLE_ANALYTICS_KEY')) {
    define('GOOGLE_ANALYTICS_KEY', getenv('GOOGLE_ANALYTICS_KEY'));
}
?>