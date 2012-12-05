<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gruppen_modul.php";
$modul = new gruppen_modul();
$fields = array(
        array("Input","Name","name")
    );
$buttons = array("Edit","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
