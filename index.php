<?php 
ob_start();
require_once("includes/general.inc.php");
if(isset($_GET["logout"])){
        unset($_SESSION["_registry"]["user"]);
        header('Location: '."http://".$_SERVER['PHP_SELF'].$_SERVER['REQUEST_URI']);
}
$css_files = array  (
                        "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css",
                        $URL_ROOT."js/validator/css/validationEngine.jquery.css",
                        "http://twitter.github.com/bootstrap/assets/css/bootstrap.css",
                        "http://twitter.github.com/bootstrap/assets/css/bootstrap-responsive.css",
                        "http://twitter.github.com/bootstrap/assets/css/docs.css",
                        "http://twitter.github.com/bootstrap/assets/js/google-code-prettify/prettify.css",
                        "http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css",
                        $URL_ROOT."/css/bootstrap-image-gallery.min.css",
                        $URL_ROOT."js/treeview/jqtree.css",
                        $URL_ROOT."css/style.css",
                    );
?>
<!--[if lt IE 7]><link rel="stylesheet" href="http://blueimp.github.com/cdn/css/bootstrap-ie6.min.css"><![endif]-->
<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<?php
$js_files = array   (
                        "//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js",
                        $URL_ROOT."js/jquery.autocomplete.js",
                        $URL_ROOT."js/validator/js/languages/jquery.validationEngine-de.js",
                        $URL_ROOT."js/validator/js/jquery.validationEngine.js",
                        "http://twitter.github.com/bootstrap/assets/js/google-code-prettify/prettify.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-transition.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-alert.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-dropdown.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-scrollspy.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-tab.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-popover.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-button.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-collapse.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-carousel.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-typeahead.js",
                        "http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js",
                        $URL_ROOT."js/bootstrap-image-gallery.min.js",
                        $URL_ROOT."js/treeview/tree.jquery.js",
                        $URL_ROOT."js/main.js",
                        $URL_ROOT."js/pdf_combined.js"
                    );
$content = preg_split("/\//", str_replace(array($URL_ROOT,"//"),array("","%2F"),"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]));
foreach ($content as $id => $con){
        if (!$con)unset($content[$id]);
        else $content[$id] = urldecode($con);
}
if (!isset($content[0])){$content[0] = "Blarg";$content[1] = "home";}
$page = $DB->query_fetch("SELECT * FROM wf_sites where alias = '".$content[0]."' AND view != 'ext_link' AND view != 'int_link' AND view != '' LIMIT 1;");
if(isset($content[1]) && $DB->affected_query("SELECT * FROM wf_sites where parent = '".$page["id"]."' LIMIT 1;")) {
        $parent["id"] = $page["id"];
        $parent["alias"] = $page["alias"];
        $page = $DB->query_fetch("SELECT * FROM wf_sites where alias = '".$content[1]."' AND view != 'ext_link' AND view != 'int_link' AND view != '' LIMIT 1;");
}
if(isset($content[2]) && $DB->affected_query("SELECT * FROM wf_sites where parent = '".$page["id"]."' LIMIT 1;")) {
        $parent["id"] = $page["id"];
        $parent["alias"] = $page["alias"];
        $page = $DB->query_fetch("SELECT * FROM wf_sites where alias = '".$content[2]."' AND view != 'ext_link' AND view != 'int_link' AND view != '' LIMIT 1;");
}

$page_title = $page["seo_title"];
$page_desc = $page["seo_desc"];
$page_keys = $page["seo_keywords"];
require_once 'header.php';
require("components/".$page["component"]."/modules/".$page["modul"]."/".$page["view"].".php");
?>
                </div>
                <div id="right">
                {{sidebar}}        
                </div>
                <div style="clear:both;"></div>
        </div>
</div>
<div id="foot">
    <div class="footer">
        {{footer}}   
        <a style="float:right; margin-right:20px;" onclick="$('html, body').animate({scrollTop:0}, 'slow');">Seitenanfang</a>
        <div align="center" id="member" style="clear:both;">
</div>
    </div>
</div>
</body>
</html>
<?php
function set_position($position, &$contents){
    global $DB;
    $sections = $DB->select("SELECT * FROM wf_sections WHERE position = '$position' AND status = 1 ORDER by `order`;");
    foreach($sections as $section){
        require("components/".$section["component"]."/sections/".$section["section"].".php");
    }
    $contents = str_replace('{{'.$position.'}}', $content, $contents);
}
$contents = ob_get_contents();
$positions = array();
preg_match_all('{{(.*)}}', $contents, $positions);
foreach ($positions[0] as $position){
    $clear_position = str_replace(array('{','}'), '', $position);
    if($clear_position != '"data_track_addressbar":true'){
        set_position($clear_position, $contents);
    }
}
ob_clean();
echo $contents;
ob_end_flush();
?>