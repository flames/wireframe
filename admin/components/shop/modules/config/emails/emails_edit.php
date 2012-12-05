<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/emails_modul.php";
$modul = new emails_modul();
$fields = array(
        array("Input","Betreff","betreff"),
        array("HTMLMin","Nachricht","text",2048)
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>
