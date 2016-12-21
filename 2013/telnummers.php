<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();

if (isset($_POST ['submit'])) {
    $userId = $_POST ['userId'];
    $tel = $_POST ['tel'];
    $driver->addPhonenumber($userId, $tel);
}

$result = $driver->getAllPhonenumbers(); ?>

<h1>Telefoonnummers</h1>
<hr id="hrheader">
<table style="border: 0px">

<?php
foreach ( $result as $phonenumber ) { ?>
    <tr>
        <td style="width: 120px"><?= $driver->getUserById($phonenumber ['user_id'])->getDisplayName() ?></td>
        <td style="width: 120px"><a href="tel:<?php echo $phonenumber ['phonenumber']; ?>"><?php echo $phonenumber ['phonenumber']; ?></a></td>
        <?php 
        if ($authMgr->isAdmin()) { ?>
            <td><a href="deletetelefoonnummer/?id=<?php echo $phonenumber ['id']; ?>" onclick="return confirm('Weet je zeker dat je dit telefoonnummer wilt verwijderen?');">[x]</a></td>
            <?php 
        } ?>
    </tr>
    <?php 
} ?>

</table>
<hr style="float: left; width: 268px; height: 0px; border: 0px; border-top: 1px solid #512e6b; display: block;" />
<br />
<form method="post">
    <table>
        <tr>
            <td>Naam</td>
                    <td><select name="userId">
                <?php
                    $users = $driver->getAllUsers();
                    foreach ( $users as $user ) {
                        $selected = $authMgr->getMe()->getId() === $user->getId() ? ' selected="selected"' : '';
                        echo '<option value="' . $user->getId() . '"'.$selected.'>' . $user->getDisplayName() . '</option>';
                    }
                ?></td>
        </tr>
        <tr>
            <td>Telefoonnummer</td>
            <td><input type="text" name="tel" /></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submit" value="Voeg toe" id="button" /></td>
        </tr>
    </table>
</form>