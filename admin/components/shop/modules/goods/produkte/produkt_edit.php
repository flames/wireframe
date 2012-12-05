<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/produkt_modul.php";
$modul = new produkt_modul();
$fields = array(
    "Basisdaten" => array(
    array("Input","Artikelnummer","art_num"),
    array("Input","Name","name"),
    array("TableSelect","Gruppe","group","name","wf_groups","id"),
    array("BoolSelectRelation","Kategorien","wf_prod_cat","cat_id","prod_id","name","wf_categorys"),
    array("TableSelect","Lieferant","supplier","name","wf_shop_suppliers","id"),
    array("Number","Preis","price"),
    array("Number","spez. MwSt.","spec_tax"),
    array("Input","Schlagwörter","keywords"),
    array("HtmlMin","Kurztext","short_desc",1024),
    array("Html","Beschreibung","desc",4096)
    ),
    "Attribute und Optionen" => array(
	array("Attributes","Attribute","wf_prod_attr","attr_id","prod_id","name","wf_group_attr"),
    array("Options","Optionen","wf_options"),
    array("Uploads","Bilder","prod_pics",array('accept_file_types' => '/.+(png|gif|jpg|jpeg)/i'))
    )
    );
echo $modul->showEntityStructured($_GET["edit"], $fields);
?>
</div>