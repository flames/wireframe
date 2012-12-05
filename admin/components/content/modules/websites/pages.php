<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/pages_modul.php";
$modul = new sites_modul();
$fields = array(
    array("Input","Seite","name")
    );
$buttons = array("Edit","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
