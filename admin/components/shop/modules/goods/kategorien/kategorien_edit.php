<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/kategorien_modul.php";
$modul = new kategorien_modul();

$fields = array(
	array("TableSelect","Parent","parent","name","wf_categorys","id"),
    array("Input","Name","name")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
