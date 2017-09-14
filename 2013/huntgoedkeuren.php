<?php
if(!defined('opoiLoaded')) die('Incorrect or unknown use of application');
if (isset($_GET ['id'])) {
    $hunt = $driver->getHunt($_GET ['id']);
    
    if($_GET ['goedkeuren'] == 2) {
        $hunt->setGoedgekeurd(2);
    } elseif($_GET ['goedkeuren'] == 1) {
        $hunt->setGoedgekeurd(1);
    } else {
        $hunt->setGoedgekeurd(0);
    }
    
    $driver->updateHunt($hunt);
}

header('Location: ' . WEBSITE_URL . 'hunts');
die();