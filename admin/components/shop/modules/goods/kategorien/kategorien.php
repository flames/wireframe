<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/kategorien_modul.php";
$modul = new kategorien_modul();
$fields = array(
        array("Status","Status","status"),
		array("Table","Parent","parent","name","wf_categorys","id"),
        array("Input","Name","name")
    );
$buttons = array("Edit","Order","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
