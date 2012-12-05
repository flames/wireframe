<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/kategorien_modul.php";
$modul = new kategorien_modul();

$fields = array(
	array("TableSelect","Gruppe","cat_id","name","wf_news_cat","id"),
    array("Input","Name","name"),
    array("Html","Text","text",2048),
    );
$langs = array("de","en","fr","it");
$lang_fields = array(1,2);
echo $modul->showEntityMultilang($_GET["edit"], $fields,$langs,$lang_fields);
?>
</div>
