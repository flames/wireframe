<?php
	$form_array = array(
			"main" => array(
					"E-Mail-Adresse" => array("","validate[required]", "name"),
					"Passwort" => array("","validate[required]", "password"),
					"Passwort wiederholen" => array("","validate[equals[password]]", "password2")
				),
			"adress" => array(
					"Anrede" => array("",FALSE, "anrede"),
					"Vorname" => array("","validate[required]", "vorname"),
					"Nachname" => array("","validate[required]", "nachname"),
					"Geburtsdatum" => array("","validate[required]", "gb_date"),
					"Firma" => array("",FALSE, "firma"),
					"Straße" => array("","validate[required]", "strasse"),
					"Hausnummer" => array("","validate[required]", "nr"),
					"PLZ" => array("","validate[required]", "plz"),
					"Ort" => array("","validate[required]", "ort"),
					"USt-ID (wenn vorhanden)" => array("",FALSE, "ust_id"),
					"zus. Info" => array("",FALSE, "info"),
					"Land" => array("","validate[required]", "land"),
					"Telefon" => array("",FALSE, "tel"),
					"Telefax" => array("",FALSE, "fax"),
					"Mobil" => array("",FALSE, "mobil"),
					"Telefon Privat" => array("",FALSE, "privat")
				)
		);
?>
	<div id="register">
		<form id="userdata">
			<table style="width:100%;">
				<tr>
					<td style="width:50%;">
						<?php echo gen_fields($form_array["main"]);?>
					</td>
				</tr>
				<tr>
					<td style="width:50%;vertical-align:top;">
					<h3>Persönliche Daten</h3>
						<?php echo gen_fields($form_array["adress"]);?>
					</td>
					<td style="width:50%;vertical-align:top;">
				</tr>
			</table>
		</form>
	</div>