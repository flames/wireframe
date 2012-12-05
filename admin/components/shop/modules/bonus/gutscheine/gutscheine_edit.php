<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/gutscheine_modul.php";
$modul = new gutscheine_modul();
$fields = array(
		array("Input","Name","name"),
		array("Code","Gutescheincode","code"),
        array("Bool","einmalig","once"),
        array("DateRange","Gültig","from","till"),
        array("Input","Preisnachlass","ammount"),
        array("Info","Benutzt","used"),
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
