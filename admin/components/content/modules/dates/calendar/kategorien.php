<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/kategorien_modul.php";
$modul = new kategorien_modul();
$fields = array(
        array("Status","Status","status"),
        array("Input","Name","name")
    );
$buttons = array("Edit","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
