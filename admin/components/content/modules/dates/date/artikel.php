<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/artikel_modul.php";
$modul = new produkt_modul();
$fields = array(
        array("Status","Status","status"),
        array("Table","Kalendar","feed_id","name","wf_calendar","id"),    
        array("DateRange","Termin","from","to"),
        array("Input","Name","headline")
    );
$buttons = array("Edit","Copy","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
