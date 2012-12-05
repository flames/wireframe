<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gruppen_modul.php";
$modul = new gruppen_modul();

$fields = array(
    	array("Input","Name","name")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
