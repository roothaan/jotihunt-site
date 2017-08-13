<?php
/**
 * Used for Mailgun
 */
 if (getenv('PROXIMO_USER') && getenv('PROXIMO_PASS') && getenv('PROXIMO_HOST')) {
    define('PROXIMO_ENABLED', true);
    define('PROXIMO_USER', getenv('PROXIMO_USER'));
    define('PROXIMO_PASS', getenv('PROXIMO_PASS'));
    define('PROXIMO_HOST', getenv('PROXIMO_HOST'));
} else {
    define('PROXIMO_ENABLED', false);
}
?>