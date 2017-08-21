<?php

function parseJotihuntIniFile($user_config) {
    //Database
    if (array_key_exists('database', $user_config)) {
        $database_config = $user_config['database'];
        if (array_key_exists('database_url', $database_config)) {
            putenv('DATABASE_URL=' . $database_config['database_url']);
        }
        if (array_key_exists('database-options', $database_config)) {
            putenv('DATABASE_OPTIONS=' . $database_config['database-options']);
        }
    }

    //Google
    if (array_key_exists('google', $user_config)) {
        $google_config = $user_config['google'];
        if (array_key_exists('google-js-api-key', $google_config)) {
            putenv('GOOGLE_JS_API_KEY=' . $google_config['google-js-api-key']);
        }
        if (array_key_exists('google-analytics-key', $google_config)) {
            putenv('GOOGLE_ANALYTICS_KEY=' . $google_config['google-analytics-key']);
        }
        if (array_key_exists('google-gcm-api-key', $google_config)) {
            putenv('GOOGLE_GCM_API_KEY=' . $google_config['google-gcm-api-key']);
        }
    }

    //Mailgun
    if (array_key_exists('mailgun', $user_config)) {
        $mailgun_config = $user_config['mailgun'];
        if (array_key_exists('mailgun-api-key', $mailgun_config)) {
            putenv('MAILGUN_API_KEY=' . $mailgun_config['mailgun-api-key']);
        }
        if (array_key_exists('mailgun-api-domain', $mailgun_config)) {
            putenv('MAINGUN_API_DOMAIN=' . $mailgun_config['mailgun-api-domain']);
        }
        if (array_key_exists('mailgun-from-email', $mailgun_config)) {
            putenv('MAILGUN_FROM_EMAIL=' . $mailgun_config['mailgun-from-email']);
        }
    }

    //Proximo
    if (array_key_exists('proximo', $user_config)) {
        $proximo_config = $user_config['proximo'];
        if (array_key_exists('promimo-user', $proximo_config)) {
            putenv('PROXIMO_USER=' . $proximo_config['promimo-user']);
        }
        if (array_key_exists('promimo-pass', $proximo_config)) {
            putenv('PROXIMO_PASS=' . $proximo_config['promimo-pass']);
        }
        if (array_key_exists('promimo-host', $proximo_config)) {
            putenv('PROXIMO_HOST=' . $proximo_config['promimo-host']);
        }
    }

    // Jotihunt
    if (array_key_exists('jotihunt', $user_config)) {
        $jotihunt_config = $user_config['jotihunt'];
        if (array_key_exists('proxy-server-port', $jotihunt_config)) {
            putenv('PROXY_SERVER_PORT=' . $jotihunt_config['proxy-server-port']);
        }
        if (array_key_exists('proxy-base-url', $jotihunt_config)) {
            putenv('PROXY_BASE_URL=' . $jotihunt_config['proxy-base-url']);
        }
        if (array_key_exists('site-show-errors', $jotihunt_config)) {
            putenv('SITE_SHOW_ERRORS=' . $jotihunt_config['site-show-errors']);
        }
        if (array_key_exists('dev-mode', $jotihunt_config)) {
            putenv('DEV_MODE=' . $jotihunt_config['dev-mode']);
        }
    }
    // Themes
    if (array_key_exists('theme', $user_config)) {
        $theme_config = $user_config['theme'];
        if (array_key_exists('name', $theme_config)) {
            putenv('THEME_NAME=' . $theme_config['name']);
        }
        if (array_key_exists('logos', $theme_config)) {
            putenv('THEME_LOGOS=' . $theme_config['logos']);
        }
    }
}

if (file_exists(ROOT_DIR . 'config/user.ini')) {
    $user_config = parse_ini_file(ROOT_DIR . 'config/user.ini', true);
    parseJotihuntIniFile($user_config);
}
