<?php
//$local_ip = gethostbyname("pro-tec35.ath.cx");

if(!substr_count($_SERVER['HTTP_REFERER'],$URL_ROOT) && !substr_count($_SERVER['HTTP_REFERER'],"http://www.bluechemgroup.com") && $_SERVER['HTTP_REFERER'] && ($local_ip != $_SERVER["REMOTE_ADDR"] OR $_GET["key"])){
	$kmc = new folderant_api();
	die;
}
elseif($local_ip == $_SERVER["REMOTE_ADDR"] && $_GET["action"]){
	$_POST = $_GET;
	$_POST["short_tags"] = json_decode($_POST["short_tags"]);
}

$kmc = new folderant();
	if($content[1] == "download"){
	$kmc->get_file($content[2]);
}
$SIDEBAR = '<fieldset>
	<legend>'.$LANG["folderant"]["information"].'</legend>
	<b>
	'.$LANG["folderant"]["folders"].': '.$kmc->count_folders().'<br/>
	'.$LANG["folderant"]["files"].':'.$kmc->count_files().'<br/><br/>
	<a class="btn btn-info" id="get_short_tags">'.$LANG["folderant"]["short_tags"].'</a><br/><br/>
	<a class="btn btn-info" id="get_product_codes">'.$LANG["folderant"]["product_codes"].'</a><br/>
	</b>
</fieldset>
<fieldset>
	<legend>'.$LANG["folderant"]["popular"].'</legend>';
		$popular = $kmc->get_popular();
		foreach($popular as $file){
			$SIDEBAR .='
                <a href="javascript:open_file_details(\'file_'.$file["id"].'\')"><img src=/components/folderant/helpers/folderant_preview.php?id='.$file["id"].' style="float:left; width:40px; margin-bottom:10px;"/><div style="width:255px; float:right; height:40px;">'.$file["name"].'</div></a><div style="clear:both"></div>
               
			';
		}
$SIDEBAR .='
</fieldset>
<fieldset>
	<legend>'.$LANG["folderant"]["new"].'</legend>';
		$latest = $kmc->get_latest();
		foreach($latest as $file){
			$SIDEBAR .='
                <a href="javascript:open_file_details(\'file_'.$file["id"].'\')"><img src=/components/folderant/helpers/folderant_preview.php?id='.$file["id"].' style="float:left; width:40px; margin-bottom:10px;"/><div style="width:255px; float:right; height:40px;">'.$file["name"].'</div></a><div style="clear:both"></div>
               
			';
		}
$SIDEBAR .='
</fieldset>
<fieldset>
	<legend>'.$LANG["folderant"]["logout"].'</legend>
	<form id="form-login" name="login" method="post" action="http://www.bluechemgroup.com/index.php" target="_parent" style="margin:0;">
		<input type="hidden" value="com_user" name="option">
		<input type="hidden" value="logout" name="task">
		<input type="hidden" value="L2RlL3Nwb25zb3JpbmcvbmV3cw==" name="return">
	</form>
	<a class="btn btn-info" href="javascript:$(\'#form-login\').submit()">'.$LANG["folderant"]["logout_btn"].'</a>
</fieldset>';
if($EMBED){
	echo "
	<div id=\"sidebar\">
$SIDEBAR;
	</div>";
}
?>

<form action="" method="post" id="kmc_search">
	<br/><br/>
	<div class="input-append">
		<input type="text" placeholder="<?php echo $LANG["folderant"]["search_placeholder"]; ?>" class="search-query span2" id="kmc" name="kmc" value="<?php echo $_POST["kmc_text"]; ?>" autocomplete="off"/>
		<button name="search" value="suchen" type="submit" class="btn btn-info" style="margin-left:-10px;">
                    <span><?php echo $LANG["folderant"]["search"]; ?></span>
    	</button>
	</div>
	<script>
		$("#kmc").autocomplete("<?php echo $URL_ROOT; ?>/components/folderant/helpers/folderant_suggest.php");
	</script>
	<div class="modal hide fade" id="doc_tags">
		<div class="modal-header">
			<a href="#" class="close" data-dismiss="modal">&times;</a>
			<h3><?php echo $LANG["folderant"]["search_doc_type_desc"]; ?></h3>
		</div>
		<div class="modal-body">
<?php
	foreach($kmc->get_short_tags() as $key => $value){
		echo '
		<input type="checkbox" name="short_tags[]" value="\''.$key.'\'"'; echo in_array("'".$key."'", $_POST["short_tags"]) ? ' checked="checked" ' : ''; echo'/>&nbsp'.$value.'<br/>';
	}
?>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-primary" data-dismiss="modal">OK</a>
		</div>
	</div>
	<div class="modal hide fade" id="short_tags">
		<div class="modal-header">
			<a href="#" class="close" data-dismiss="modal">&times;</a>
			<h3><?php echo $LANG["folderant"]["short_tags"]; ?></h3>
		</div>
		<div class="modal-body">
		<table style="width:100%;">
<?php
$count = 0;
	foreach($kmc->get_short_tags() as $key => $value){
		if($count == 0) echo '<tr>';
		$count ++;
		echo '<td><b>'.$key.': </b></td><td>'.$value.'</td>';
		if($count == 2){
			echo "</tr>";
			$count = 0;
		}
	}
	if ($count == 1) echo "</tr>";
?>
</table>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo $LANG["folderant"]["close"]; ?></a>
		</div>
	</div>
	<div class="modal hide fade" id="product_codes">
		<div class="modal-header">
			<a href="#" class="close" data-dismiss="modal">&times;</a>
			<h3><?php echo $LANG["folderant"]["product_codes"]; ?></h3>
		</div>
		<div class="modal-body">
		<table style="width:100%;">
<?php
$count = 0;
	foreach($kmc->get_product_codes() as $key => $value){
		if($count == 0) echo '<tr>';
		$count ++;
		echo '<td><b>'.$key.': </b></td><td>'.$value.'</td>';
		if($count == 2){
			echo "</tr>";
			$count = 0;
		}
	}
	if ($count == 1) echo "</tr>";
?>
</table>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn btn-primary" data-dismiss="modal"><?php echo $LANG["folderant"]["close"]; ?></a>
		</div>
	</div>
	<div class="btn-group">
		<a class="btn <?php if($_POST["fulltext"]) echo 'btn-success';?>" id="fulltext_toggle"><?php echo $LANG["folderant"]["search_content"]; ?></a>
		<a class="btn btn-warning cancel"  <?php if(!$_POST["use_short_tags"]) echo 'style="display:none;"';?> id="discard_filter"><?php echo $LANG["folderant"]["search_doc_types_discard"]; ?></a>
		<a class="btn <?php if($_POST["use_short_tags"]) echo 'btn-success';?>" id="tag_filter" ><?php echo $LANG["folderant"]["search_doc_type"]; ?></a>
	</div>
	<input type="hidden" name="use_short_tags" id="use_short_tags" value="<?php echo $_POST["use_short_tags"];?>"/>
	<input type="hidden" name="fulltext" id="fulltext" value="<?php echo $_POST["fulltext"];?>"/>
</form>
<?php
	$results = array();
	if(isset($_POST["kmc_text"]) && $_POST["kmc_text"]){
		if(!$_POST["use_short_tags"]) unset($_POST["short_tags"]);
		$results = $kmc->search($_POST["kmc_text"],$_POST["fulltext"],$_POST["short_tags"]);
		if ($results) echo '<h4><a href="'.$_SERVER['SCRIPT_NAME'].'/KMC/">'.$LANG["folderant"]["search_discard"].'</a></h4>';
		else echo '<h4>'.$LANG["folderant"]["search_no_results"].'</h4>';
	}
		echo '<div id="tree1" data-url="'.$URL_ROOT.'/components/folderant/helpers/folderant_explorer.php?search='.urlencode(json_encode($results)).'"><img src="'.$URL_ROOT.'img/ajax-loader.gif"/></div>
		';
		echo '	<div class="modal hide fade" id="file_details">
				</div>';
		echo "	<script type='text/javascript'>
					function toggle_node(node){
						if(node.children.length !== 0){
							if(!node.is_open){		
								$('#tree1').tree('openNode', node);
							}
							else{
								$('#tree1').tree('closeNode', node);
							}
						}
						else{
							open_file_details(node.id);
						}
					}

					function open_file_details(id){
						id = id.replace('file_',''); 
						$.post('".$URL_ROOT."/components/folderant/helpers/folderant_details.php', {id: id}, function(data) {
  							$('#file_details').html(data);	    				";
		if($EMBED) echo "
  							$('.modal').css('top',(mouse_y - 150) + 'px');";
  					echo "
							$('#file_details').modal('show');
						});
					}

					$('#tree1').tree(";
		if(count($results)) echo "{autoOpen: true}";
		echo ");
					$('#tree1').bind(
    					'tree.click',
    					function(event) {
        				var node = event.node;
        				toggle_node(node);
    					}
					);
					$('#tree1').bind(
    					'tree.init',
    					function() {
    				";
		if($EMBED) echo "
        					jQuery(sendContentHeight('http://www.bluechemgroup.com/de/kmc-login/kmc-neu'));";
		if ($_GET["folder"]){
			$folders = explode("/", $_GET["folder"]);
			$parent = 0;
			foreach($folders as $folder){
				$folder_2 = $DB->query_fetch_single("SELECT id FROM wf_folderant_folders WHERE name = '$folder' AND parent = $parent LIMIT 1;");
				if($folder_2){
				echo "
							node = $('#tree1').tree('getNodeById', ".$folder_2.");
							toggle_node(node);";
				}
				else{
							$file =	$DB->query_fetch_single("SELECT id FROM wf_folderant_files WHERE name = '$folder' AND parent = $parent LIMIT 1;");
				echo 		"
							open_file_details('file_".$file."');";
				}
				$parent = $folder_2;
			}
		}
        echo '
   						}
					);
jQuery(document).ready(function() {
	$("#fulltext_toggle").click(function(){
		if($("#fulltext").val() == 1){
			$("#fulltext").val("0");
			$("#fulltext_toggle").removeClass("btn-success");
		}
		else{
			$("#fulltext").val("1");
			$("#fulltext_toggle").addClass("btn-success");
		}
	})
	$("#tag_filter").click(function(){
		if(!$("#tag_filter").hasClass("btn-success")){
			$("#use_short_tags").val("1");
			$("#tag_filter").addClass("btn-success");
			$("#discard_filter").css("display","inline-block");
		}';
		if($EMBED) echo "
  		$('.modal').css('top','50px');";
  	echo '
		$("#doc_tags").modal("show");
	})
	$("#get_short_tags").click(function(){';
		if($EMBED) echo "
  		$('.modal').css('top','50px');";
  	echo '
		$("#short_tags").modal("show");
	})
	$("#get_product_codes").click(function(){';
		if($EMBED) echo "
  		$('.modal').css('top','50px');";
  	echo '
		$("#product_codes").modal("show");
	})
	$("#discard_filter").click(function(){
			$("#use_short_tags").val("0");
			$("#tag_filter").removeClass("btn-success");
			$("#discard_filter").css("display","none");
			$("input[@name=\'short_tags[]\']:checked").attr("checked", false); 
	})
});
				</script>';
?>
