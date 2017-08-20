<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');
JotihuntUtils::requireLogin();

$user = $authMgr->getMe();
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
parse_str($url, $output);
?>

<h1>Gegevens aanpassen</h1>
<p>Hallo <strong><?= $user->getDisplayName() ?></strong>!</p>

<?php if (isset($output['success'])) { ?>
<p style="color: green"><strong>Je gegevens zijn successvol aangepast.</strong></p>
<?php } ?>
<h2>Gegevens aanpassen</h2>
<form method="POST" action="<?= BASE_URL ?>ajax/me.ajax.php">
    <table>
        <tr>
            <td>UserID</td><td><?= $user->getId() ?></td>
        </tr>
        <tr>
            <td>Gebruikersnaam</td><td><?= $user->getUsername() ?></td>
        </tr>
        <tr>
            <td>Volledige naam</td>
            <td><input type="text" name="displayname" value="<?= $user->getDisplayName() ?>"/>
            <?php if (isset($output['displayname'])) { ?>
            <span style="color:red"><?= $output['displayname'] ?></span>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <td>(Nieuw) wachtwoord</td>
            <td><input type="text" name="new_password" value=""/>
            <?php if (isset($output['new_password'])) {
                foreach ($output['new_password'] as $error) { ?>
                    <div style="color:red"><?= $error ?></div>
                <?php } ?>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <td></td><td><input type="submit" value="Aanpassen"/></td>
        </tr>
    </table>
</form>