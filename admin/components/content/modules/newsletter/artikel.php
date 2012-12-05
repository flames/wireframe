<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/artikel_modul.php";
$modul = new produkt_modul();
$fields = array(
        array("Status","Status","status"),
        array("Input","Name","Name"),
        array("Date","gesendet","Date")
    );
$buttons = array("Edit","Copy","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
