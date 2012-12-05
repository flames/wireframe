<?php 
$EMBED = TRUE;
ob_start();
require("includes/general.inc.php");
if(isset($_GET["logout"])){
        unset($_SESSION["_registry"]["user"]);
        header('Location: '."http://".$_SERVER['PHP_SELF'].$_SERVER['REQUEST_URI']);
}
$css_files = array  (
                        "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css",
                        $URL_ROOT."js/validator/css/validationEngine.jquery.css",
                        "http://twitter.github.com/bootstrap/assets/css/bootstrap.css",
                        "http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css",
                        $URL_ROOT."/css/bootstrap-image-gallery.min.css",
                        $URL_ROOT."js/treeview/jqtree.css",
                        $URL_ROOT."css/embed.css",
                    );
?>
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?php
$js_files = array   (
                        "//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js",
                        $URL_ROOT."js/jquery.autocomplete.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-dropdown.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-button.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-typeahead.js",
                        $URL_ROOT."js/treeview/tree.jquery.js",
                        $URL_ROOT."js/jquery.ba-postmessage.js",
                        $URL_ROOT."js/main.js"
                    );
$content = preg_split("/\//", str_replace(array($URL_ROOT.'embed.php/',"//"),array("","%2F"),"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]));
foreach ($content as $id => $con){
        if (!$con)unset($content[$id]);
        else $content[$id] = urldecode($con);
}
if (!isset($content[0])){$content[0] = "Presse";}
$page = $DB->query_fetch("SELECT * FROM wf_sites where titel = '".$content[0]."' LIMIT 1;");

$page_title = $page["seo_title"];
$pagesc = $page["seosc"];
$page_keys = $page["seo_keywords"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
        <head>
                <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
                <meta name="description" content="<?php echo $pagesc; ?>" />
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
<div id="iframe_wrapper">
<?php
if($page["type"] == 2) include("includes/".$page["file"]);
else echo $page["text"];
?>
</div>
<script type="text/javascript">
var sendContentHeight = function(parent_url) {
    function setHeight() {
        var height = jQuery('body').outerHeight(true);
        if (height < 1100) height = 1100;
        jQuery.postMessage({
            if_height : height
        }, parent_url, parent);
    };
    setHeight();
};
var mouse_x;
var mouse_y;
jQuery(document).ready(function() {
        jQuery(sendContentHeight('http://www.bluechemgroup.com/de/kmc-login/kmc-neu'));
        jQuery(sendContentHeight('http://bluechemgroup.com/de/kmc-login/kmc-neu'));
        $(document).mousemove(function(e){
                mouse_x = e.pageX;
                mouse_y = e.pageY;
        }); 
        setInterval("jQuery(sendContentHeight('http://www.bluechemgroup.com/de/kmc-login/kmc-neu'))",200);
        setInterval("jQuery(sendContentHeight('http://bluechemgroup.com/de/kmc-login/kmc-neu'))",200);
});
</script>
</body>
</html>
<?php
ob_end_flush();
?>