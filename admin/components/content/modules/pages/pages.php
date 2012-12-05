<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/pages_modul.php";
$modul = new sites_modul();
$fields = array(
    array("Status","Status","status"),
    array("Input","Titel","titel")
    );
$buttons = array("Edit","Copy","Order","Status","Delete");
echo $modul->listTree($fields,FALSE,$buttons)
?>
