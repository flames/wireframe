<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/config_modul.php";
$modul = new config_modul();
$fields = array(
        array("Input","Name","option"),
        array("Input","Wert","value")
    );
$buttons = array("Edit");
echo $modul->listTable($fields,FALSE,$buttons)
?>
