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
    <li><a href="<?=BASE_URL?>hunter_map.php">Hunter map</a></li>
    <li><a href="<?=BASE_URL?>admin/users.php">Users</a></li>
    <li><a href="<?=BASE_URL?>admin/createusers.php">Auto create users</a></li>
    <li><a href="<?=WEBSITE_URL?>admin-gcm">Android GCM IDs</a></li>
    <li><a href="<?=BASE_URL?>admin/poi.php">Pois</a></li>
</ul>
    <?php if (!$driver->isReady() || $authMgr->isSuperAdmin()) { ?>
    <h2>Super Admin</h2>
    <ul>
        <li><a href="<?=BASE_URL?>admin/config.php">Config</a></li>
        <li><a href="<?=BASE_URL?>admin/kml-import.php">KML Import tool</a></li>
        <li><a href="<?=BASE_URL?>admin/events.php">Events</a> (<?= $driver->getAllEventsCount() ?>)</li>
        <li><a href="<?=BASE_URL?>admin/organisations.php">Organisations</a> (<?= $driver->getAllOrganisationsCount() ?>)</li>
        <li><a href="<?=BASE_URL?>admin/deelgebieden.php">Deelgebieden</a> (<?= $driver->getAllDeelgebiedenCount() ?>)</li>
        <li><a href="<?=BASE_URL?>admin/speelhelften.php">Speelhelften</a> (<?= $driver->getAllSpeelhelftenCount() ?>)</li>
        <li><a href="<?=BASE_URL?>admin/counterhunt.php">Counterhunt rondes</a> (<?= $driver->getAllCounterhuntrondjesCount() ?>)</li>
        <li><a href="<?=BASE_URL?>admin/vossen.php">Vossen</a>  (<?= $driver->getAllTeamsCount() ?>)</li>
        
        <li><a href="<?=BASE_URL?>admin/showdatabase.php">Show database</a></li>
        <li><a href="<?=BASE_URL?>admin/phpinfo.php">phpinfo();</a></li>
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