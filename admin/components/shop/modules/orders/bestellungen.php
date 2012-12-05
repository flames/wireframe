<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/bestellungen_modul.php";
$modul = new bestellungen_modul();
$fields = array(
        array("Input","Bestellnummer","id"),
        array("DateTime","Datum","date"),
        array("Table","Kunde","user","name","permissions_entity","id"),
        array("Input","Status","status")
    );
$buttons = array("Edit","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
