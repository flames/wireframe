<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/suppliers_modul.php";
$modul = new suppliers_modul();
$fields = array(
        array("Input","Name","name"),
        array("Input","Email","email"),
        array("TableSelect","Land","country","name_de","wf_countrys","id"),
        array("Input","Adresse","adress"),
        array("Input","PLZ","zip"),
        array("Input","Stadt","city"),
        array("Html","Notizen","notices",1024)
    );
echo $modul->showEntity($_GET["edit"], $fields);
?>
</div>