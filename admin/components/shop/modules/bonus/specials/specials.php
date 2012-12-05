<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/specials_modul.php";
$modul = new specials_modul();
$fields = array(
        array("Status","Status","status"),
        array("DateRange","Zeitraum","start","end"),
        array("Input","Name","name")
    );
$buttons = array("Edit","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
