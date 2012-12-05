<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/specials_modul.php";
$modul = new specials_modul();
$fields = array(
        array("DateRange","Zeitraum","start","end"),
        array("Input","Name","name"),
        array("Input","Nachlass Preis (in %)","preis"),
        array("Input","Nachlass Versand (in %)","versand")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>