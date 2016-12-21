<?php
/**
 * Used for Mailgun
 */
if (getenv('MAILGUN_API_KEY') && getenv('MAINGUN_API_DOMAIN')) {
    define('MAILGUN_API_KEY', getenv('MAILGUN_API_KEY'));
    define('MAINGUN_API_DOMAIN', getenv('MAINGUN_API_DOMAIN'));
}

if (getenv('MAILGUN_FROM_EMAIL')) {
    define('MAILGUN_FROM_EMAIL', getenv('MAILGUN_FROM_EMAIL'));
}

?>