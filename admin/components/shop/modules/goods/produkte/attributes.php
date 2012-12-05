<?php require ("../../../../../includes/general.inc.php"); 
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors',1); 
if (isset($_POST["save"])){
	$DB->query("DELETE FROM wf_group_attr WHERE group_id = ".$_GET["id"]." AND type = 1;");
	$query = "INSERT INTO `wf_group_attr` (`id`, `group_id`, `name`, `type`) VALUES ";
	$first = true;
	foreach($_POST["save_attr"] as $attr){
	if(!$first) $query .= ", ";
	$query .= "(NULL, '".$_GET["id"]."', '".$attr."', 1)";
	$first = false;
	}
	$query .= ";";
	$DB->query($query);
}
$attributes = $DB->select("SELECT * FROM wf_group_attr WHERE group_id = ".$_GET["id"]." AND type = 1 ORDER by id;");
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
				margin-left: 20px;
			}
		</style>
        <title>Wireframe Permissions</title>
            </head>

    <body>
		<div  style="overflow:auto; height:100%;"> 
		<h3>Attribute</h3>
		<p><b>Hier können sie mögliche Attribute des Produktes festlegen.</b></p>
		<form action="" method="post" name="group_attributes">
			<table style="width:100%">
<?php 
	foreach($attributes as $attr){
		echo '
		<tr id="row_'.$attr["id"].'">
			<td>
				<input style="height:24px;" type="text" name="save_attr['.$attr["id"].']" value="'.$attr["name"].'"/>
			</td><td>
                <button type="button" id="'.$attr["id"].'" class="del_button btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Löschen</span>
                </button>
			</td>
		</tr>';
	}
?>
	<tr id="bottom_line"><td>
				<button class="btn btn-success start" id="new_button">
                    <i class="icon-plus icon-white"></i>
                    <span>Neues Attribut</span>
                </button>
	</td><td>
		<input type="hidden" name="save" value="save"/>
                <button class="btn btn-success start">
                    <i class="icon-check icon-white"></i>
                    <span>Speichern</span>
                </button>
	</td></tr>
	</table>
		</form>
</div>
<script type="text/javascript">
	function init(){
		$(".del_button").click(function(event){	
  			event.preventDefault();
  			$('#row_' + $(this).attr('id')).remove();
		});
	}
	var new_id = 1;
	$("#new_button").click(function(event){	
  		event.preventDefault();
  		$("#bottom_line").before('<tr id="row_'+ new_id +'"><td><input style="height:24px;" type="text" name="save_attr[new_'+ new_id +']" value=""/></td><td><button type="button" id="new_'+ new_id +'" class="del_button btn btn-danger delete"><i class="icon-trash icon-white"></i><span>Löschen</span></button></td></tr>');
  		new_id = new_id + 1;
  		init();
	});
	init();
</script>
</body>
</html>