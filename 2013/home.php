<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

if (isset($_GET ["error"])) {
    if ($_GET ["error"] == 1) { ?>
        <p class="errorMsg">Je moet ingelogd zijn om deze pagina te bekijken!</p>
        <?php
    }
} ?>

<h1>Home</h1>
<p>Welkom op de Jotihunt-spelsite van de <a href="http://roothaangroep.nl/" target="_blank">Roothaangroep</a>. De Jotihunt is een door Scouting Gelderland georganiseerde vossenjacht die ieder jaar tijdens de JOTA/JOTI plaats vindt. Tijdens de 30 uur durende vossenjacht proberen maar liefst 70 groepen uit heel Gelderland 12 verschillende vossenteams te vinden. Hierbij worden zij geholpen door aanwijzingen die regelmatig op <a href="http://www.jotihunt.net" target="_blank">Jotihunt.net</a> verschijnen.</p>
<p>Deze site is bedoeld voor hunters van de Generaal Roothaangroep. Via deze site kunnen vossen-locaties worden opgezocht, het rijschema worden bekeken en dankzij de alarmpagina worden wij zo snel mogelijk gewaarschuwd wanneer een vossenteam actief/inactief wordt, een nieuw bericht op de site verschijnt en we een plaatsje stijgen/zakken in de scorelijst.</p>
<p>Ook dit jaar zijn wij op <strong><a href="http://www.facebook.com/RoothaanJotihunt">facebook.com/RoothaanJotihunt</a></strong> te volgen. Stay tuned voor foto's, filmpjes en andere nieuwtjes!</p>
<p>Het thema van de Jotihunt 2012 was <strong>Superhelden</strong>, in 2013 was het <strong>Alice in Wonderland</strong> en <strong>Happy Together</strong> was in 2014. In 2015 werden we <strong>EERSTE</strong> met het thema <strong>The Force Awakens</strong> en dit jaar is het thema <strong>Jotihunt goes Asian</strong>.</p>

<?php
if ($authMgr->isLoggedIn()) { ?>
    <h2>
        <a href="<?php echo BASE_URL;?>scores.php">Scorelijst</a>
    </h2>
    <br />
    
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
    <a href="http://www.jotihunt.net" target="_blank"><img src="<?=BASE_URL?>images/banner2016.jpg" /></a>
</div>
