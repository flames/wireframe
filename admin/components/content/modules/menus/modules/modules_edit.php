<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/modules_modul.php";
$modul = new modules_modul();

$fields = array(
    	array("Input","Name","name"),
    	array("Select_Component","Komponente","component"),
    	array("Select_Section","Bereich","section"),
    	array("Input","Position","position"),
    	array("Option","Parameter","options")
    );
$langs = array("de","en","fr","it");
$lang_fields = array(0,4);
echo $modul->showEntityMultilang($_GET["edit"], $fields,$langs,$lang_fields);
?>
</div>
