<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/group_modul.php";
$modul = new group_modul();
$fields = array(
    array("Input","Name","name"),
    array("Parent","Parent","parent")
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
