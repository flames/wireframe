<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/kategorien_modul.php";
$modul = new kategorien_modul();

$fields = array(
    array("Input","Name","name"),
    array("Html","Beschreibung","desc", 2048)
    );

$langs = array("de","en","fr","it");
$lang_fields = array(0,1);
echo $modul->showEntityMultilang($_GET["edit"], $fields,$langs,$lang_fields);
?>
</div>
