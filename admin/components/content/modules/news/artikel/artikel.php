<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/artikel_modul.php";
$modul = new produkt_modul();
$fields = array(
        array("Status","Status","status"),
        array("Table","Gruppe","cat_id","name","wf_news_cat","id"),
        array("Table","Newsfeed","feed_id","name","wf_newsfeeds","id"),
        array("Input","Name","headline")
    );
$buttons = array("Edit","Copy","Status","Delete");
echo $modul->listTable($fields,FALSE,$buttons)
?>
