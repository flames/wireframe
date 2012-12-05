<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/pages_modul.php";
$modul = new sites_modul();
$fields = array(
    array("Input","Titel","titel"),
    array("Alias","Alias","alias"),
	array("TableSelect","Menu","menu","name","wf_menus","id"),
	array("TableSelect","Parent","parent","titel","wf_sites","id"),
    array("Select_Component","Komponente","component"),
    array("Select_Modul","Modul","modul"),
    array("Select_View","View","view"),
    array("Option","Parameter","text"),
	array("Input","SEO Titel","seo_title"),
	array("Input","SEO Keywords","seo_keywords"),
	array("Input","SEO Description","seo_desc")
);
$langs = array("de","en","fr","it");
$lang_fields = array(0,1,7,8,9,10);
echo $modul->showEntityMultilang($_GET["edit"], $fields,$langs,$lang_fields);
?>
