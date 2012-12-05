<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/produkt_modul.php";
$modul = new produkt_modul();
$fields = array(
        array("Status","Status","status"),
        array("Table","Gruppe","group","name","wf_groups","id"),
        array("Input","Artikelnummer","art_num"),
        array("Input","Name","name")
    );
$buttons = array("Edit","Copy","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
