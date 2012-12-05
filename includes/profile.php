<?php
	$user = user_details($_SESSION["_registry"]["user"]['name']);
	$form_array = array(
			"main" => array(
					"E-Mail-Adresse" => array($user['name'],"validate[required]", "name"),
					"Passwort" => array($user['nodes']['password'],FALSE, "password"),
					"Passwort wiederholen" => array("","validate[equals[password]]", "password2")
				),
			"adress" => array(
					"Anrede" => array($user['nodes']['anrede'],FALSE, "anrede"),
					"Vorname" => array($user['nodes']['vorname'],"validate[required]", "vorname"),
					"Nachname" => array($user['nodes']['nachname'],"validate[required]", "nachname"),
					"Geburtsdatum" => array($user['nodes']['gb_date'],"validate[required]", "gb_date"),
					"Firma" => array($user['nodes']['firma'],FALSE, "firma"),
					"Straße" => array($user['nodes']['strasse'],"validate[required]", "strasse"),
					"Hausnummer" => array($user['nodes']['nr'],"validate[required]", "nr"),
					"PLZ" => array($user['nodes']['plz'],"validate[required]", "plz"),
					"Ort" => array($user['nodes']['ort'],"validate[required]", "ort"),
					"USt-ID (wenn vorhanden)" => array($user['nodes']['ust_id'],FALSE, "ust_id"),
					"zus. Info" => array($user['nodes']['info'],FALSE, "info"),
					"Land" => array($user['nodes']['land'],"validate[required]", "land"),
					"Telefon" => array($user['nodes']['tel'],FALSE, "tel"),
					"Telefax" => array($user['nodes']['fax'],FALSE, "fax"),
					"Mobil" => array($user['nodes']['mobil'],FALSE, "mobil"),
					"Telefon Privat" => array($user['nodes']['privat'],FALSE, "privat")
				),
			"del_adress" => array(
					"Anrede" => array($user['nodes']['del_anrede'],FALSE, "del_anrede"),
					"Vorname" => array($user['nodes']['del_vorname'],"validate[checked_required]", "del_vorname"),
					"Nachname" => array($user['nodes']['del_nachname'],"validate[checked_required]", "del_nachname"),
					"Firma" => array($user['nodes']['del_firma'],FALSE, "del_firma"),
					"Straße" => array($user['nodes']['del_strasse'],"validate[checked_required]", "del_strasse"),
					"Hausnummer" => array($user['nodes']['del_nr'],"validate[checked_required]", "del_nr"),
					"PLZ" => array($user['nodes']['del_plz'],"validate[checked_required]", "del_plz"),
					"Ort" => array($user['nodes']['del_ort'],"validate[checked_required]", "del_ort"),
					"zus. Info" => array($user['nodes']['del_info'],FALSE, "del_info"),
					"Land" => array($user['nodes']['del_land'],"validate[checked_required]", "del_land"),
					"Telefon" => array($user['nodes']['del_tel'],FALSE, "del_tel"),
					"Telefax" => array($user['nodes']['del_fax'],FALSE, "del_fax"),
					"Mobil" => array($user['nodes']['del_mobil'],FALSE, "del_mobil")
				)
		);


?>
				<div class="tab-content">
					<div class="tab-pane active" id="profile_tab_start">
						<h3>Wilkommen <?php echo $_SESSION["_registry"]["user"]['name']; ?></h3>
						<p>
							Sie haben sich erfolgreich eingelogged.<br/>
							Hier finden sie Informationen über ihre Bestellungen und können ihre Benutzerdaten bearbeiten.
						</p>
					</div>
					<div class="tab-pane" style="display:none;" id="profile_tab_data">
						<p>
							<form id="profile_userdata">
								<input type="hidden" name="id" value="<?php echo $user["id"];?>" />
								<table style="width:100%;">
									<tr>
										<td style="width:50%;">
											<?php echo gen_fields($form_array["main"]);?>
										</td>
									</tr>
									<tr>
										<td style="width:50%;">
											<h3>Rechnungsadresse</h3>
											<?php echo gen_fields($form_array["adress"]);?>
										</td>
										<td style="width:50%; vertical-align:top;">
											<h3>Lieferanschrift (falls abweichend)</h3>
											<input type="checkbox" value="1" name="use_del" id="use_del" <?php if($user['nodes']['use_del']) echo 'checked="checked"'; ?>/>Lieferanschrift verwenden
											<?php echo gen_fields($form_array["del_adress"]);?>
										</td>
									</tr>
								</table>
							</form>
						</p>
					</div>
					<div class="tab-pane" style="display:none;" id="profile_tab_orders">
					</div>
				</div>
			<script>
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(".tab-pane").fadeOut("slow");
                $($(this).attr('href')).fadeIn("slow");
            })
            </script>