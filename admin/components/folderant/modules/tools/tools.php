<?php
include pathinfo(__FILE__,PATHINFO_DIRNAME)."/tools_modul.php";
$modul = new tools_modul();
$files = array();
if($_POST["folderant_action"]){
	echo "<pre>";
		print_r($modul->$_POST["folderant_action"]());
	echo "</pre>";
}
echo '
	<form action="" method="post" id="folderant_tools">
		<input type="hidden" id="folderant_action" name="folderant_action" value="" />
	</form>
	<a class="btn" style="width:200px;" onclick="$(\'#folderant_action\').val(\'update_index\');$(\'#folderant_tools\').submit();">Index aktualisieren</a><br/><br/>
	<a class="btn" style="width:200px;" onclick="$(\'#folderant_action\').val(\'index_folders\');$(\'#folderant_tools\').submit();">Index erneuern</a><br/><br/>
	<a class="btn" style="width:200px;" onclick="$(\'#folderant_action\').val(\'make_backup\');$(\'#folderant_tools\').submit();">Backup</a><br/><br/>
	<a class="btn" style="width:200px;" onclick="$(\'#folderant_action\').val(\'get_backup\');$(\'#folderant_tools\').submit();">Backup einspielen</a>
'; 
?>