<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/artikel_modul.php";
$modul = new produkt_modul();
$fields = array(
    array("Input","Name","name"),
    array("Input","Name EN","name_en"),
    array("Date","gesendet","date"),
    array("Html","Text DE","text_de",10240),
    array("Html","Text EN","text_en",10240),
    array("Html","Text FR","text_fr",10240),
    array("Html","Text IT","text_it",10240)
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>