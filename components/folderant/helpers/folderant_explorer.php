<?php

    require("../../../includes/general.inc.php");
    $kmc = new folderant();
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    $search = json_decode(urldecode($_GET["search"]),true);
    if(!count($search)) $search = FALSE;
    echo json_encode($kmc->get_tree(0,$search));
?>