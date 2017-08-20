<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
$authMgr->requireSuperAdmin();
?>
<h1>Config</h1>
<h2>Base Config</h2>
<pre>
ROOT_DIR : <?= ROOT_DIR ?>

CLASS_DIR : <?= CLASS_DIR ?>

TEST_CLASS_DIR : <?= TEST_CLASS_DIR ?>

BASE_URL : <?= BASE_URL ?>

TEST_URL : <?= TEST_URL ?>

$_SERVER ['HTTP_HOST'] = <?= $_SERVER ['HTTP_HOST'] ?>
</pre>

<h2>Database Config</h2>
<pre>
DB_TYPE : <?= DB_TYPE ?>

DEV_MODE : <?php if (defined('DEV_MODE')) { echo DEV_MODE; } else {echo 'no DEV_MODE defined'; } ?>

DB_SERVER : <?= DB_SERVER ?>

DB_PORT : <?= DB_PORT ?>

DB_USERNAME : <?= DB_USERNAME ?>

DB_PASSWORD : <?= DB_PASSWORD ?>

DB_DATABASE : <?= DB_DATABASE ?>

DB_OPTS : <?= DB_OPTS ?>
</pre>

<h2>Environment Variables</h2>
<pre>
DATABASE_URL : <?= getenv('DATABASE_URL') ?>

GOOGLE_JS_API_KEY : <?= getenv('GOOGLE_JS_API_KEY') ?>

GOOGLE_GCM_API_KEY : <?= getenv('GOOGLE_GCM_API_KEY') ?>

GOOGLE_ANALYTICS_KEY : <?= getenv('GOOGLE_ANALYTICS_KEY') ?>

MAINGUN_API_DOMAIN : <?= getenv('MAINGUN_API_DOMAIN') ?>

MAILGUN_API_KEY : <?= getenv('MAILGUN_API_KEY') ?>

MAILGUN_FROM_EMAIL : <?= getenv('MAILGUN_FROM_EMAIL') ?>

PROXIMO_USER : <?= getenv('PROXIMO_USER') ?>

PROXIMO_PASS : <?= getenv('PROXIMO_PASS') ?>

PROXIMO_HOST : <?= getenv('PROXIMO_HOST') ?>

SITE_SHOW_ERRORS : <?= getenv('SITE_SHOW_ERRORS') ?>
</pre>

<h2>Proxy settings</h2>
<pre>
PROXY_SERVER_PORT : <?= getenv('PROXY_SERVER_PORT') ?>

PROXY_BASE_URL : <?= getenv('PROXY_BASE_URL') ?>

</pre>