<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta name="description" content="<?php echo $page_desc; ?>" />
		<meta name="keywords" content="<?php echo $page_keys; ?>" />
		<title><?php echo $page_title; ?></title>
        <?php foreach ($css_files as $css){?>
        <link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo $css; ?>" />  
        <?php } ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $URL_ROOT; ?>css/print.css" media="print" />
		<link rel="icon" href="<?php echo $URL_ROOT; ?>images/favicon.ico" type="image/x-icon" />
        <script type="text/javascript">var URL_ROOT = '<?php echo $URL_ROOT; ?>'; </script>
        <?php foreach ($js_files as $js){?>
        <script type="text/javascript" src="<?php echo $js; ?>"></script> 
        <?php } ?>
        <!-- PNG FIX for IE6 -->
  		<!-- http://24ways.org/2007/supersleight-transparent-png-in-ie6 -->
		<!--[if lte IE 6]>
			<script type="text/javascript" src="js/pngfix/supersleight-min.js"></script>
		<![endif]-->
	</head>
<body>
    <div id="main_wrapper">

    <div id="header">
        <div id="logo">
                <a href="<?php echo $URL_ROOT;?>"><img src="<?php echo $URL_ROOT;?>img/logo.png" alt="Bluechemgroup" /></a>    
       </div>
	   <div id="navbar-example" class="navbar navbar-static">
		  <div class="navbar-inner">
			<div class="container">
                {{header_top}}
			</div>
		  </div>
	    </div>
    </div>
    <div id="panel_holder">
    <div id="panel">
        {{featured}}
    </div>
        	  <div class="panel-control close"><span>Close</span></div>
</div>
    <script type="text/javascript">
    	$('.panel-control').click(function() {
    		if($(".panel-control").hasClass("close")){
    			$(".panel-control > span").html("open");
    			$('#panel').slideUp();
    			$(".panel-control").removeClass("close").addClass("open");
    		}
    		else if($(".panel-control").hasClass("open")){
    			$(".panel-control > span").html("close");
    			$('#panel').slideDown();
    			$(".panel-control").removeClass("open").addClass("close");
    		}
		});
	</script>

    <div id="all">
<?php
	if(!isset($main_section)) $main_section = $DB->query_fetch("SELECT * FROM wf_sites WHERE titel='".$content[0]."' LIMIT 1;");
	$parent = $main_section["id"];
	foreach ($content as $content_part) {
		$path .= str_replace("%2F","//",urlencode($content_part)).'/'; 
		if($content_part == $main_section["titel"]) break;
	}
	if($main_section["navbar"]) {
		$site_menu = generate_tree("wf_sites", "titel", "site_menu", $main_section["titel"].'/', false, $parent);
		echo'
				<div id="left">
					<div id="sub_menu">'.$site_menu.'</div>
				</div>';
		 $center_css = 'center';
	}
	else $center_css = 'center_big';
?>
	<div id="<?php echo $center_css; ?>">