<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/bestellungen_modul.php";
$modul = new bestellungen_modul();

$fields = array(
					"Bestellung" => array(
    						array("Select","Status","status",array(1=>"eingegangen",2=>"bearbeitung",3=>"versendet")),
    						array("Bestellung","Bestellung")
    				),
					"Kundendaten" => array(
							array("Kundendaten","Kundendaten")
					)
    );
echo $modul->showEntityStructured($_GET["edit"], $fields);
?>
</div>
