<?php
if(!defined("opoiLoaded")) die("Scouting Putten, het lukt jullie niet om ons te hacken!");

JotihuntUtils::requireLogin();

$car_array = array ();
if ($handle = opendir('images/cars')) {
    while ( false !== ($entry = readdir($handle)) ) {
        if ($entry != "." && $entry != "..") {
            $car_array [] = 'value: "' . BASE_URL . 'images/cars/' . $entry . '",
                    selected: false,
                    description: "",
                    imageSrc: "' . BASE_URL . 'images/cars/' . $entry . '"';
        }
    }
    closedir($handle);
}
?>

<script src="<?php echo BASE_URL; ?>js/jquery.ddslick.min.js"></script>
<script type="text/javascript">
    function showRij(){
    	$('#rijtoggle').fadeOut(200, function() {
    		$('#rijform').fadeIn(200);
    	});
    }
    function hideRij(){
    	$('#rijform').fadeOut(200, function() {
    		$('#rijtoggle').fadeIn(200);
    	});
    }
    
    $(document).ready(function() {
        $(".datepicker").datepicker();
        $('.timepicker').timepicker();
    
        $('#rijders').dataTable( {
    		"bPaginate": false,
    		"bLengthChange": false,
    		"bFilter": false,
    		"bSort": false,
    		"bInfo": false,
    		"bAutoWidth": false,
    		"aoColumns": [
                {sName: "status" },
                {sName: "deelgebied" },
                {sName: "naam" },
                {sName: "bijrijders" },
                {sName: "tel" },
                {sName: "van" },
                {sName: "tot" },
                {sName: "auto" }
          ]
    	})
    	<?php if ($authMgr->isAdmin()) { ?>
    	.makeEditable({
    	    sUpdateURL: "<?=BASE_URL?>ajax/hunters.ajax.php",
    	    sDeleteURL: "<?=BASE_URL?>ajax/hunters.ajax.php"
    	})
    	<?php } ?>
    	;
    	
    	$('#carsselect').ddslick({
            data:ddData,
            width:100,
            selectText: "Kies auto",
            imagePosition:"right",
            onSelected: function(selectedData){
                //console.log(selectedData.selectedData.value);
                //callback function: do something with selectedData;
            }
        });
        
        $('#carsselect input.dd-selected-value').attr("name","auto");
    });

    var ddData = [
        {
        <?php
        echo implode('},{', $car_array);
        ?>
        }
    ];

    
</script>

<h1>Rijders</h1>

<?php if ($authMgr->isAdmin()) { ?>
<button id="btnDeleteRow">Verwijder rijder</button>
<?php } ?>

<form action="<?=BASE_URL . 'ajax/hunters.ajax.php'?>" method="POST">
    <table id="rijders">
        <thead>
            <tr>
                <th style="width: 20px;"><b><span title="Status">S</span></b></th>
                <th style="width: 70px;"><b>Deelgebied</b></th>
                <th style="width: 100px;"><b>Chaffeur</b></th>
                <th style="width: 200px;"><b>Bijrijders</b></th>
                <th style="width: 120px;"><b>Tel. nummer</b></th>
                <th style="width: 120px;"><b>Van</b></th>
                <th style="width: 120px;"><b>Tot</b></th>
                <th style="width: 120px;"><b>Auto</b></th>
            </tr>
        </thead>
        <?php
        if ($authMgr->isAdmin()) {
            // http://gta.wikia.com/Vehicles_in_GTA_1
            ?>
            <tfoot>
            <tr>
                <td colspan="8"><hr style="float: left; width: 785px; height: 0px; border: 0px; border-top: 1px solid #512e6b; display: block;" /> <br /></td>
            </tr>
            <tr id="rijtoggle" style="height: 30px;">
                <td colspan="8" style="text-align: left;"><small><a href="javascript:void(0);" onclick="showRij();" style="text-decoration: none;">Voeg rijder toe</a></small></td>
            </tr>
            <tr id="rijform" style="height: 30px; display: none;">
                <td>&nbsp;</td>
                <td><select name="deelgebied">
                    <?php 
                    $allDeelgebieden = $driver->getAllDeelgebieden();
            		foreach($allDeelgebieden as $deelgebied) { ?>
                        <option value="<?= $deelgebied->getId() ?>"><?= $deelgebied->getName() ?></option>
                        <?php
            		} ?>
                </select></td>
                <td><select name="userId">
                <?php
                    $users = $driver->getAllUsers();
                    foreach ( $users as $user ) {
                        echo '<option value="' . $user->getId() . '">' . $user->getDisplayName() . '</option>';
                    }
                ?>
                </select></td>
                <td><input type="text" name="bijrijders" value="Bijrijders" style="width: 90px;" /></td>
                <td><em>Wordt automatisch ingevuld</em></td>
                <td><input type="text" name="startdatum" value="<?php echo date('d-m-Y');?>" style="width: 70px;" class="datepicker" /><input type="text" name="starttijd" value="<?php echo date('H:i');?>" class="timepicker" style="width: 70px;" /></td>
                <td><input type="text" name="einddatum" value="<?php echo date('d-m-Y');?>" style="width: 70px;" class="datepicker" /><input type="text" name="eindtijd" value="<?php echo date('H:i');?>" class="timepicker" style="width: 70px;" /></td>
                <td><div id="carsselect"></div></td>
                <td><input type="submit" name="submitrij" value="Voeg toe" class="button" /> <small><a href="javascript:void(0);" onclick="hideRij();" style="text-decoration: none;">Annuleer</a></small></td>
            </tr>
        </tfoot>
        <?php } ?>
        <tbody>
        <?php
        $ridercollection = $driver->getAllRiders();
        $lastriderlocationcollection = $driver->getLastRiderLocations();
        foreach ( $ridercollection as $rider ) {
            ?>
	        <tr id="<?php echo $rider->getId(); ?>">
	            <td>
	                <?php
	                if(isset($lastriderlocationcollection[$rider->getId()])) {
	                    $lastlocationtime = $lastriderlocationcollection[$rider->getId()]->getTime();
    	                if($lastlocationtime > date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." - 15 minutes"))) { ?>
    	                    <img src="<?php echo BASE_URL."images/actief.png";?>" title="Actief" />
    	                    <?php
    	                } else if($lastlocationtime > date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." - 30 minutes"))) { ?>
    	                    <img src="<?php echo BASE_URL."images/inactief.png";?>" title="Inactief" />
    	                    <?php
    	                } else { ?>
    	                    <img src="<?php echo BASE_URL."images/offline.png";?>" title="Offline" />
    	                    <?php
    	                }
	                } else { ?>
	                    <img src="<?php echo BASE_URL."images/nooit.png";?>" title="Nooit online geweest" />
	                    <?php
	                }
	                ?>
	            </td>
                <td><?php echo $rider->getDeelgebied();?></td>
                <td class="read_only"><?php echo $rider->getUser()->getDisplayName();?></td>
                <td><?php echo $rider->getBijrijder();?></td>
        
                <td><?= JotihuntUtils::getPhoneNumbersForUserId($rider->getUser()->getId()) ?></td>
                <td><?php echo date('d-m-Y H:i:s',$rider->getVan());?></td>
                <td><?php echo date('d-m-Y H:i:s',$rider->getTot());?></td>
                <td><img src="<?php echo BASE_URL."images/cars/".$rider->getAuto();?>" title="auto" alt="<?php echo $rider->getAuto();?>" /></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</form>