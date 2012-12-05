<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/versandkosten_modul.php";
$modul = new versandkosten_modul();
$fields = array(
		array("TableSelect","Land","country","name_de","wf_countrys","id"),
        array("Input","bis KG","kg"),
        array("Input","Preis (in €)","price")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
