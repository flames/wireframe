<?php require ("../../includes/general.inc.php"); 
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors',1); 
$perm_class = new permissions();
$entity = $DB->query_fetch_single("SELECT name FROM permissions_entity WHERE `id` = ".$_GET["id"].";");
$type = $DB->query_fetch_single("SELECT type FROM permissions_entity WHERE name = '$entity' LIMIT 1;");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    	<link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap.min.css">
		<link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css">
		<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
		<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
		<script type="text/javascript" src="http://code.jquery.com/jquery-1.6.1.min.js"></script>
		<script type="text/javascript" src="<?php echo $URL_ROOT; ?>admin/js/jquery-ui.min.js"></script>
		<script type="text/javascript"> var URL_ROOT = '<?php echo $URL_ROOT; ?>'</script>
		<style>
			legend{
				margin-bottom:8px;
				font-size:16px;
			}
			fieldset{
				border-color: -moz-use-text-color -moz-use-text-color #EEEEEE;
				border-style: none none solid;
				border-width: 0 0 1px;
			}
			fieldset div fieldset{
				margin-left: 0;
			}
			ul{
				list-style:none;
				margin-left:23px;
			}
		</style>
        <title>Wireframe Permissions</title>
            </head>

    <body>
		<div  style="overflow:auto; height:100%;"> 
		<h3>Berechtigungen</h3>
		<p><b>Bitte setzen sie die gewünschten Berechtigungen und bestätigen sie diese</b></p>
<?php 
function generate_view($permissions,$names){
	global $perm_class, $entity, $LANG;
	echo "
		<ul>";
	foreach ($permissions as $name => $component){
			echo '
			<li id="'.$name.'"><fieldset id="'.$name.'">
			<legend><a id="perm_vis_'.$name.'" href="javascript:switch_perm_vis(\''.$name.'\')" style="font-size:14px; color:red; text-decoration:none;">+</a>&nbsp;&nbsp;&nbsp;
						<input class="main_perm_box" type="checkbox" name="perm['.$component["main"].']" value="1"'; if ($perm_class->hasUserPermission($component["main"].'.*', $entity)) echo ' checked="checked"'; if ($perm_class->hasParentPermission($permission["main"].'.*', $entity)) echo ' checked="checked" disabled="disabled"'; echo' />&nbsp;&nbsp;'.$names[$name][$name].'</legend>
						<div id="perm_content_'.$name.'" class="perm_content" style="display:none;">
						<ul>
			';

						foreach ($component as $permission => $permission_entity){
							if(!is_array($permission_entity) && $permission != "main"){
							echo "
							<li>";
								echo '<input class="perm_box" type="checkbox" name="perm['.$permission_entity.']" value="1" '; 
								if ($perm_class->hasUserPermission($permission_entity, $entity)) echo ' checked="checked"'; 
								if ($perm_class->hasParentPermission($permission_entity, $entity)) echo ' checked="checked" disabled="disabled"'; 
								echo' />&nbsp;&nbsp;'.$LANG["backend"][$permission].'&nbsp;&nbsp;&nbsp;&nbsp;
								';
							echo "
							</li>"		;
							}
							else if($permission != "main"){
								generate_view(array($permission=>$permission_entity),$names[$name]["subs"]);
							}	
						}

			echo '		
						</ul>
						</div>
				</fieldset>
			</li>
			';
	}
	echo '
		</ul>';
}
function generate_set($permissions,$_permissions, &$set_permissions){
	$setting = FALSE;
	foreach ($permissions as $name => $permission){
			if($_permissions[$permission["main"]]){$set_permissions[] = $permission["main"]."*"; $setting = TRUE;}
			else{
				foreach ($permission as $name_2 => $permission_entity){
						if(is_array($permission_entity)) {
							$temp_set = generate_set(array($name_2=>$permission_entity),$_permissions,$set_permissions);
							if($temp_set){
								if (!in_array($permission["main"],$set_permissions)) $set_permissions[] = $permission["main"];
								$setting = TRUE;
							}
						}
						else if(isset($_permissions[$permission_entity])) {
							if (!in_array($permission["main"],$set_permissions)) $set_permissions[] = $permission["main"];
							$set_permissions[] = $permission_entity;
							$setting = TRUE;
						}
				}
			}
	}
	return $setting;
}
$permissions = array();
$permissions['system']['main'] = 'system';
$permissions['system']['backend'] = 'system.backend';
$permissions['system']['sysmail'] = 'system.sysmail';
$names['system']['system'] = 'System';
$active_components = $DB->select_pair("components","name","active","order",FALSE, "active = 1" );
foreach($active_components as $component => $active){
	$permissions[$component] = parse_ini_file($DIR_ROOT."/admin/components/$component/permissions.ini",TRUE);
	$component_ini = parse_ini_file($DIR_ROOT."/admin/components/$component/component.ini",TRUE);
	$names[$component][$component] = $component_ini["name_de"];
	if ($handle = opendir($DIR_ROOT."/admin/components/$component/modules")) {
    	while (false !== ($file = readdir($handle))) {
			if (eregi("^\.{1,2}$",$file)) continue;
			$permissions[$component][$file] = parse_ini_file($DIR_ROOT."/admin/components/$component/modules/".$file."/permissions.ini",TRUE);
			$modul_ini = parse_ini_file($DIR_ROOT."/admin/components/$component/modules/".$file."/modul.ini",TRUE);
			$names[$component]['subs'][$file][$file]= $modul_ini["display_name_de"];
			$submodules = $modul_ini["subs"];
			if (isset($submodules)){
				foreach ($submodules as $submodule){
					$submodule_ini = parse_ini_file($DIR_ROOT."/admin/components/$component/modules/".$file."/".$submodule."/modul.ini",TRUE);
					$names[$component]['subs'][$file]['subs'][$submodule][$submodule] = $submodule_ini["display_name_de"];
				}
			}
    	}
    	closedir($handle);
	}
}
if(isset($_POST["set_perm"])){
	$_permissions = $_POST["perm"];
	$set_permissions = array();
	generate_set($permissions,$_permissions, $set_permissions);	
	$DB->query("DELETE FROM `permissions` WHERE `name` = '$entity' AND value = '' AND type=$type;");
	if(isset($_POST["perm"])){
		$query = "INSERT INTO `permissions` (`id` ,`name` ,`type` ,`permission` ,`world` ,`value`)VALUES ";
		$first = true;
		foreach ($set_permissions as $set_permission){
			if (!$perm_class->hasParentPermission($set_permission, $entity)){
				if (!$first) $query .=',';
				$query .= "(NULL , '$entity', '$type', '$set_permission', '', '')";
				$first = false;
			}
		}
		$query .= ";";
		$DB->query($query);
	}
	//echo '<script type="text/javascript">parent.Shadowbox.close(); </script>';
}
//print_r($user_permissions);
//print_r ($set_permissions);
//print_r($names);
echo '
<form action="" method="post" name="perm_form" id="perm_form">';
?>
<?php
generate_view($permissions,$names);
echo '
				<input type="hidden" name="set_perm" value="setzen"/>
                <button class="btn btn-success start" onclick="$(\'#perm_form\').submit();" style="margin-top:10px;">
                    <i class="icon-check icon-white"></i>
                    <span>Speichern</span>
                </button>
</form>';
?>
<script type="text/javascript">
	$('.main_perm_box').click(function(){
		var main_field = $(this).parent().parent().parent().parent();
		var parent_field = $(this).parent().parent();
		$('#'+parent_field.attr("id") + ' .main_perm_box,#'+parent_field.attr("id") + ' .perm_box').prop("checked",$(this).prop("checked"));
		if ($(this).prop("checked") == false){
			if(main_field) $("#"+main_field.attr("id")+">legend>input").prop("checked",false);
			if(parent_field) $("#"+parent_field.attr("id")+">legend>input").prop("checked",false);
		}		
		else{
			var main_set = true;
			$('#'+main_field.attr("id") + ' .main_perm_box').each(function(i){
				if($(this).prop("checked") == false && i > 0) main_set = false;
			});
			$("#"+main_field.attr("id")+">legend>input").prop("checked",main_set);
		}
	})
	
	$('.perm_box , .main_perm_box').click(function(){
		var parent_field = $(this).parent().parent().parent().parent();
		if(parent_field.attr("id") == undefined) var parent_field = $(this).parent().parent().parent().parent().parent().parent().parent();
		var main_field = parent_field.parent().parent().parent().parent().parent().parent();
		var comp_field = main_field.parent().parent().parent().parent().parent().parent();
		if ($(this).prop("checked") == false){
			if(parent_field) $("#"+parent_field.attr("id")+">legend>input").prop("checked",false);
			if(main_field) $("#"+main_field.attr("id")+">legend>input").prop("checked",false);
			if(comp_field) $("#"+main_field.attr("id")+">legend>input").prop("checked",false);
		}
		else{
			var parent_set = true;
			$('#'+parent_field.attr("id") + ' .perm_box').each(function(i){
				if($(this).prop("checked") == false) parent_set = false;
			});
			$("#"+parent_field.attr("id")+">legend>input").prop("checked",parent_set);
			var main_set = true;
			$('#'+main_field.attr("id") + ' .main_perm_box').each(function(i){
				if($(this).prop("checked") == false && i > 0) main_set = false;
			});
			$("#"+main_field.attr("id")+">legend>input").prop("checked",main_set);			
			var comp_set = true;
			$('#'+main_field.attr("id") + ' .main_perm_box').each(function(i){
				if($(this).prop("checked") == false && i > 0) comp_set = false;
			});
			$("#"+comp_field.attr("id")+">legend>input").prop("checked",comp_set);

		}
	})
	
	function switch_perm_vis(name){
		var box = $('#perm_content_'+ name);
		var switcher = $('#perm_vis_'+ name);
		if (box.css("display") == "none"){
			box.show();
			switcher.html("-");
		}
		else{
			box.hide();
			switcher.html("+");
		}
	}
</script>
</div>
</body>
</html>