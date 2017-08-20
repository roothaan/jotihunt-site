<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

// If it's not ready at all, we can safely ignore protocol
if ($driver->isReady()) {
    $authMgr->requireAdmin();
}
?>

<h1>Admin</h1>
<?php
    if (!defined('DB_DEFINED')) {?>
    <p class="errorMsg">De database is (nog?) niet geïnitialiseerd. Zorg er voor dat de <code>DATABASE_URL</code> variable in je PHP omgeving bestaat. Er hoeft nog niets in de database te staan (deze wordt later gevuld).</p>
    <br />De DATABASE_URL variable moet voldoen aan het PostgreSQL formaat:<br />
    <pre><code>postgres://username:password@hostname:port/databasename</code></pre>
    <?php } else {
?>
<ul>
    <li><a href="<?=WEBSITE_URL?>admin-hunter-map">Hunter map</a></li>
    <li><a href="<?=WEBSITE_URL?>admin-users">Users</a></li>
    <li><a href="<?=WEBSITE_URL?>admin-createusers">Auto create users</a></li>
    <li><a href="<?=WEBSITE_URL?>admin-gcm">Android GCM IDs</a></li>
    <li><a href="<?=WEBSITE_URL?>admin-poi">Pois</a></li>
</ul>
    <?php if (!$driver->isReady() || $authMgr->isSuperAdmin()) { ?>
    <h2>Super Admin</h2>
    <ul>
        <li><a href="<?=WEBSITE_URL?>suadmin-config">Config</a></li>
        <li><a href="<?=WEBSITE_URL?>suadmin-kml-import">KML Import tool</a></li>
        <li><a href="<?=WEBSITE_URL?>suadmin-events">Events</a> (<?= $driver->getAllEventsCount() ?>)</li>
        <li><a href="<?=WEBSITE_URL?>suadmin-organisations">Organisations</a> (<?= $driver->getAllOrganisationsCount() ?>)</li>
        <li><a href="<?=WEBSITE_URL?>suadmin-deelgebieden">Deelgebieden</a> (<?= $driver->getAllDeelgebiedenCount() ?>)</li>
        <li><a href="<?=WEBSITE_URL?>suadmin-speelhelften">Speelhelften</a> (<?= $driver->getAllSpeelhelftenCount() ?>)</li>
        <li><a href="<?=WEBSITE_URL?>suadmin-counterhunt">Counterhunt rondes</a> (<?= $driver->getAllCounterhuntrondjesCount() ?>)</li>
        <li><a href="<?=WEBSITE_URL?>suadmin-vossen">Vossen</a>  (<?= $driver->getAllTeamsCount() ?>)</li>
        
        <li><a href="<?=WEBSITE_URL?>suadmin-showdatabase">Show database</a></li>
        <li><a href="<?=WEBSITE_URL?>suadmin-phpinfo">phpinfo();</a></li>
    </ul>
    <div><h2>Danger zone</h2>
    <strong>Reset database</strong><br />
    Hiermee wordt ALLE data gewist, de tabellen opnieuw aangemaakt en het admin wachtwoord gereset.<br />
    Deze actie kan NIET ongedaan gemaakt worden.<br />
    
    <form action="<?=WEBSITE_URL?>admin" method="POST">
        Kies een nieuw admin wachtwoord:
        <input type="text" name="admin_pw" />
        <input type="hidden" name="action" value="resetDb" />
        <input type="submit" value="Reset" />
    </form>
    </div>
    <?php } ?>
<?php } ?>
<?php
if (!$driver->isReady() || $authMgr->isSuperAdmin()) {
    if (isset($_POST ['action'])) {
        if ('resetDb' === $_POST ['action']) {
            $newAdminPw = $_POST['admin_pw'];
            if (strlen($newAdminPw) > 0) {
                $dbDriver = Datastore::getDatabaseDriver();
                $success = $dbDriver->resetDb($newAdminPw);
                if ($success) {
                    echo '<h3>Database gereset!</h3>';
                    echo 'new pw: <code>'.$newAdminPw.'</code><br />';
                    echo 'Ververs de pagina om opnieuw in te loggen met dit wachtwoord.';
                } else {
                    echo '<h3>Kon de database niet resetten';
                }
            } else {
                echo '<h3>Wachtwoord vergeten</h3>';
            }
        }
    }
}