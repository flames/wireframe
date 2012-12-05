<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/user_modul.php";
$modul = new user_modul();
$fields = array(
	"Stammdaten" => array(
    array("Input","Name","name"),
    array("UserGroup","Bentuzergruppe","group"),
    array("Password","Passwort","password"),
    array("Input","Anrede", "anrede"),
	array("Input","Vorname", "vorname"),
	array("Input","Nachname", "nachname"),
	array("Input","Geburtsdatum", "gb_date"),
	array("Input","USt-ID (wenn vorhanden)", "ust_id"),
	array("Input","Telefon", "tel"),
	array("Input","Telefax", "fax"),
	array("Input","Mobil", "mobil"),
	array("Input","Telefon Privat", "privat")
    ),
    "Rechnungsadresse" => array(
	array("Input","Anrede", "anrede"),
	array("Input","Vorname", "vorname"),
	array("Input","Nachname", "nachname"),
	array("Input","Firma", "firma"),
	array("Input","Straße", "straße"),
	array("Input","Hausnummer", "nr"),
	array("Input","PLZ", "plz"),
	array("Input","Ort", "ort"),
	array("Input","zus. Info", "info"),
	array("Input","Land", "land")
	),
	"Versandadresse" => array(
	array("Input","Zustell. Anrede", "del_anrede"),
	array("Input","Zustell. Vorname", "del_vorname"),
	array("Input","Zustell. Nachname", "del_nachname"),
	array("Input","Zustell. Firma", "del_firma"),
	array("Input","Zustell. Straße", "del_straße"),
	array("Input","Zustell. Hausnummer", "del_nr"),
	array("Input","Zustell. PLZ", "del_plz"),
	array("Input","Zustell. Ort", "del_ort"),
	array("Input","Zustell. zus. Info", "del_info"),
	array("Input","Zustell. Land", "del_land"),
	array("Input","Zustell. Telefon", "del_tel"),
	array("Input","Zustell. Telefax", "del_fax"),
	array("Input","Zustell. Mobil", "del_mobil")
	)
    );
echo $modul->showEntityStructured($_GET["edit"], $fields);
?>
