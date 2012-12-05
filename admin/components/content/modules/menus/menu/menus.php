<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/menus_modul.php";
$modul = new menus_modul();
$fields = array(
        array("Status","Status","status"),
        array("Input","Menü","name"),
        array("Input","Position","position")
    );
$buttons = array("Edit","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
