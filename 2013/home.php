<?php
if(!defined("opoiLoaded")) die('Incorrect or unknown use of application');

if (isset($_GET ["error"])) {
    if ($_GET ["error"] == 1) { ?>
        <p class="errorMsg">Je moet ingelogd zijn om deze pagina te bekijken!</p>
        <?php
    }
}

if (defined('WELCOME_MESSAGE')) {
    echo WELCOME_MESSAGE;
}

if ($authMgr->isLoggedIn()) { ?>
    <h2>Hunter Highscores</h2>
    <div>
        <table>
            <?php
            $highscores = $driver->getHunterHighscore();
            foreach ( $highscores as $highscore ) { ?>
                <tr>
                    <td style="width: 200px;font-weight: bold;"><?=$highscore->user->getDisplayName()?></td><td><?=$highscore->score?></td>
                </tr>
                <?php
            } ?>
        </table>
    </div>
    <a href="<?=WEBSITE_URL?>hunts">Bekijk overzicht</a><br />
    <?php 
    require_once BASE_DIR . 'blocks/scorePerDeelgebied.block.php';
}
?>
<div class="jotihuntBanner">
    <a href="https://www.jotihunters.nl" target="_blank"><img src="<?=BASE_URL?>images/themes/<?= THEME_NAME ?>/banner.jpg" /></a>
</div>
