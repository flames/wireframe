<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/user_modul.php";
$modul = new user_modul();
$fields = array(
    array("Status3","Status","status"),
    array("Input","Name","name"),
    array("Input","Bentuzergruppe","group")
    );
$buttons = array("Edit","Status3","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
