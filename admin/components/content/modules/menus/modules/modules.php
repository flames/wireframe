<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/modules_modul.php";
$modul = new modules_modul();
$fields = array(
        array("Status","Status","status"),
        array("Input","Name","name"),
        array("Input","Komponente","component"),
        array("Input","Bereich","section"),
        array("Input","Position","position")
    );
$buttons = array("Edit","Order","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
