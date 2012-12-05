<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/pages_modul.php";
$modul = new sites_modul();
$fields = array(
	"Basisdaten" => array(
    		array("Input","Name","name"),
    		array("Input","URL","url"),
    		array("Upload","Logo","logo"),
    		array("BoolSelect","Sprachen","langs","name","wf_langs")
    	),
    "Verlinkungen" => array(
    		array("BoolSelectRelation","Verlinkungen","wf_website_relation","main_site","linked_site","name","wf_websites")
    	),
    "Social-Media" => array(
    		array("Input","Facebook","facebook"),
    		array("Input","Twitter","twitter")

    	),
    "Content" => array(

    	)
    );
echo $modul->showEntityStructured($_GET["edit"], $fields);
?>
