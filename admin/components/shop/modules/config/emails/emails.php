<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/emails_modul.php";
$modul = new emails_modul();
$fields = array(
        array("Input","Name","name")
    );
$buttons = array("Edit");
echo $modul->listTable($fields,FALSE,$buttons)
?>