<?php
require_once 'init.php';
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
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="<?= BASE_URL ?>js/jquery.ddslick.min.js"></script>

<div id="myDropdown"></div>

<script>
    var ddData = [
        {
        <?php
        echo implode('},{', $car_array);
        ?>
        }
    ];

    $('#myDropdown').ddslick({
        data:ddData,
        width:100,
        selectText: "Kies auto",
        imagePosition:"right",
        onSelected: function(selectedData){
            console.log(selectedData.selectedData.value);
            //callback function: do something with selectedData;
        }
    });
</script>