<?php
require($DIR_ROOT."../../../includes/general.inc.php");
$kmc = new folderant();
echo $kmc->get_details($_REQUEST["id"]);
?>