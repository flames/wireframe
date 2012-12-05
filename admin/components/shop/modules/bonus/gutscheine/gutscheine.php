<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gutscheine_modul.php";
$modul = new gutscheine_modul();
$fields = array(
		array("Input","Name","name"),
		array("Input","Gutescheincode","code"),
        array("Input","Benutzt","used")
    );
$buttons = array("Edit","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
