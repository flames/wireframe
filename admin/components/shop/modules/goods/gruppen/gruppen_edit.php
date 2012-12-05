<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gruppen_modul.php";
$modul = new gruppen_modul();

$fields = array(
					"Basisdaten" => array(
    						array("Input","Name","name"),
    						array("Select","Typ","type",array(1=>"physisch",2=>"digital")),
    						array("Number","spez. MwSt.","spec_tax")
    				),
					"Optionen" => array(
							array("Options","Optionen","wf_options")
					)
    );
echo $modul->showEntityStructured($_GET["edit"], $fields);
?>
</div>
