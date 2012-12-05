<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gruppen_modul.php";
$modul = new gruppen_modul();
$fields = array(
        array("Status","Status","status"),
        array("Input","Name","name")
    );
$buttons = array("Edit","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
