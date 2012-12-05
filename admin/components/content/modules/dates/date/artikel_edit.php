<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/artikel_modul.php";
$modul = new produkt_modul();
$fields = array(
    array("Input","Überschrift","headline"),
    array("TableSelect","Kalendar","feed_id","name","wf_calendar","id"),
    array("DateRange","Termin","from","to"),
    array("Html","Vorschau","short_text",1024),
    array("Html","Text","text",10240)
    );
$langs = array("de","en","fr","it");
$lang_fields = array(0,3,4);
echo $modul->showEntityMultilang($_GET["edit"], $fields,$langs,$lang_fields);
?>
</div>