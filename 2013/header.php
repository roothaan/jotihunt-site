<?php
$_headerOptions['title'] = 'Spelsite van de Roothaangroep, Doetinchem';
$_headerOptions['includeBody'] = true;
if (isset($headerOptions)) {
    $_headerOptions = array_merge($_headerOptions, $headerOptions);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Jotihunt <?php echo date('Y'); ?> &bull; <?php echo $_headerOptions['title']; ?></title>
<link rel="shortcut icon" href="<?php echo BASE_URL; ?>images/favicon.png" type="image/x-icon" />
<meta name="author" content="Ralf van den Boom, Sander Roebers, Jasper Roel" />
<meta name="description" lang="nl" content="De Roothaan Jotihunt Website" />
<meta name="keywords" content="Jotihunt, NUNC, Willie, Wortel, Stam, Roothaan, Roothaangroep, Doetinchem" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="content-language" content="nl" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link type="text/css" rel="stylesheet" href="<?=BASE_URL?>style/style.css?v=<?=filemtime(BASE_DIR."style/style.css")?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?=BASE_URL?>style/tables.css?v=<?=filemtime(BASE_DIR."style/tables.css")?>" media="screen" />
<link type="text/css" rel="stylesheet" href="<?=BASE_URL?>style/responsive.css?v=<?=filemtime(BASE_DIR."style/responsive.css")?>" media="screen" />
<link type="text/css" rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<link type="text/css" rel="stylesheet" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.css" />
<link type="text/css" rel="stylesheet" href="<?php echo BASE_URL; ?>js/timepicker/jquery.ui.timepicker.css" />
<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>js/jquery.jeditable.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>js/timepicker/jquery.ui.timepicker.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>jwplayer/jwplayer.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL; ?>js/jquery.datatables-editable.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo BASE_URL; ?>external/datetimepicker/jquery.datetimepicker.css" />
<script type="text/javascript" src="<?php echo BASE_URL; ?>external/datetimepicker/jquery.datetimepicker.js"></script>

<script type="text/javascript" src="<?php echo BASE_URL; ?>external/momentjs/moment.min.js"></script>

<?php require_once BASE_DIR . 'includes/analytics.include.php'; ?>
</head>
<?php if($_headerOptions['includeBody']) { ?>
<body class="withbg">
<?php } else { ?>
<body>
<?php } ?>
<?php if($_headerOptions['includeBody']) { ?>
    <div class="headerContainer">
        <div id="logo">
		    <?php
		    $aantalAfbeeldingen = 1;
            $r = rand(1, $aantalAfbeeldingen);
            switch ($r) {
                case 1 :
                    $title = 'asianfox';
                break;
            } ?>
		    <a href="<?=WEBSITE_URL?>" title="<?=$title?>" ><img src="<?=BASE_URL?>images/logos/jotihunt-<?=$title?>.png" title="<?=$title?>" alt="Jotihunt logo" /></a>
        </div>
    <!-- end #logo -->
    <div id="header">
        <div id="menu">
		    <?php 
		    function setPage($page) {
                $pageURL = $_SERVER ["SCRIPT_NAME"];
                $pageURLAr = explode("/", $pageURL);
                
                $curPage = $pageURLAr [count($pageURLAr) - 1];
                if ($page == $curPage) {
                    echo " class=\"current_page_item\"";
                }
            }
		    
		    if ($authMgr->isLoggedIn()) { ?>
    			<ul class="menu_container">
    			    <?php 
    			    if ($driver->isReady() && ($authMgr->isSuperAdmin() || $authMgr->getMyEventId() > 0)) { ?>
        				<li class="menu_item berichtenItem" <?php setPage("berichten.php"); ?>><a href="<?=WEBSITE_URL?>berichten" class="first" title="Berichten"></a></li>
                        <?php if (!$authMgr->isSuperAdmin()) { ?>
                            <li class="menu_item vossenItem" <?php setPage("vossen.php"); ?>><a href="<?=WEBSITE_URL?>vossen" title="Vossen"></a></li>
                            <li class="menu_item huntsItem" <?php setPage("edithunts.php"); ?>><a href="<?=WEBSITE_URL?>hunts" title="Hunts"></a></li>
                            <li class="menu_item huntersItem" <?php setPage("hunters.php"); ?>><a href="<?=WEBSITE_URL?>hunters" title="Hunters"></a></li>
                            <li class="menu_item opzienersItem" <?php setPage("opzieners.php"); ?>><a href="<?=WEBSITE_URL?>opzieners" title="Opzieners"></a></li>
                            <li class="menu_item telItem" <?php setPage("telnummers.php"); ?>><a href="<?=WEBSITE_URL?>telefoonnummers" title="Telefoonnummers"></a></li>
                            <li class="menu_item alarmItem" <?php setPage("alarmsite.php"); ?>><a href="<?=WEBSITE_URL?>alarm" title="Alarm pagina"></a></li>
                            <li class="menu_item fullscreenItem" <?php setPage("fullscreen.php"); ?>><a href="<?=WEBSITE_URL?>beamer" title="Beamer pagina"></a></li>
                            <li class="menu_item mapItem" <?php setPage("map.php"); ?>><a href="<?=WEBSITE_URL?>kaart" title="Fullscreen kaart"></a></li>
        				<?php 
                        }
    				} ?>
    				
    				<?php if(!$driver->isReady() || ($authMgr->isAdmin() && $authMgr->getMyEventId() > 0) || $authMgr->isSuperAdmin()) { ?>
    					<li class="menu_item adminItem" <?php setPage("admin.php"); ?>><a href="<?=WEBSITE_URL?>admin" title="Admin"></a></li>
    				<?php } ?>
    				<li class="menu_item logoutItem"><a href="<?=WEBSITE_URL?>logout" title="Uitloggen"></a></li>
    				<li class="menu_item showMenuItem"><a href="#" title="Menu tonen"></a></li>
    				<li class="clear"></li>
    			</ul>
    			<div id="userInfoBox">
    			<?php if (!$authMgr->isSuperAdmin()) { ?>
    			Event: <a href="/events" style="line-height:normal;color: #707f55;"><strong><?= $authMgr->getMyEventId() ?></strong></a><br />
    			Organisation: <strong><?= $authMgr->getMyOrganisationId() ?></strong><br /><?php } ?>
    			User: <?= $authMgr->getMe()->getDisplayName() ?>
    			<?php if ($authMgr->isSuperAdmin()) { echo ' (<strong>Super</strong>Admin)'; } 
    			  elseif ($authMgr->isAdmin()) { echo ' (Admin)'; } ?>
    			</div>
			<?php } else { ?>
			    <input type="button" value="Inloggen" id="inlogButton" />
    			<div id="logbox">
        			<?php
                    if (!$authMgr->isLoggedIn()) { ?>
                    	<form action="<?=WEBSITE_URL?>login" method="post">
                            <div>
                                <input type="text" name="username" size="15" /> <input type="password" name="password" size="15" /> <input type="submit" name="submit" value="Log in" />
                            </div>
                        </form>
                        <?php
                    } ?>
        		</div>
    		    <?php
			} ?>
		</div>
    </div>
        
    <script>
        $(".menu_item").hover(function() {
            $(".submenu_container").stop().slideUp(100);
            $(this).find(".submenu_container").stop().slideDown(100);
        }, function() {
            $(this).find(".submenu_container").stop().slideUp(100);
        });
        
        var menuExtended = false;
        var menuClicked = false;
        
        $(".showMenuItem").click(function(e) {
            e.preventDefault();
            menuClicked = !menuClicked;
            showHideMenu();
        });
        
        $(".headerContainer").hover(function(e) {
            e.preventDefault();
            showHideMenu();
        });
        
        $("#inlogButton").click(function(e) {
            e.preventDefault();
            $(this).hide();
            $("#logbox").show();
        });
        
        function showHideMenu() 
        {
            if(menuExtended && !menuClicked) {
                $("#menu .menu_container").removeClass("extendedMenu");
            } else {
                $("#menu .menu_container").addClass("extendedMenu");
            }
            
            menuExtended = !menuExtended;
        }
    </script>
</div>
<!-- end #header -->
<!-- end #header-wrapper -->

<?php } // end 'includeBody' ?>