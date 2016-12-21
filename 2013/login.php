<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

if ($authMgr->isLoggedIn()) {
    header("Location: ".WEBSITE_URL);
    die();
}
$errorMsg = $authMgr->loginViaPost();
?>

<div class="errorMsg"><?=$errorMsg?></div>
<h2>Deze pagina vereist dat je bent ingelogd.</h2>
<p>Log in met behulp van het formulier in de rechter bovenhoek.</p>
Weet je het wachtwoord niet? Neem even contact op met je Jotihunt site/app beheerder.</p>