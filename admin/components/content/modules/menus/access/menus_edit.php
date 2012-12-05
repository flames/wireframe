<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/menus_modul.php";
$modul = new menus_modul();

$fields = array(
    	array("Input","Name","name")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
