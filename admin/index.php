<?php
require_once "includes/general.inc.php";
$css_files = array  (
                        "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/themes/base/jquery-ui.css",
                        "http://blueimp.github.com/cdn/css/bootstrap.min.css",
                        "http://blueimp.github.com/cdn/css/bootstrap-responsive.min.css",
                        "http://blueimp.github.com/Bootstrap-Image-Gallery/css/bootstrap-image-gallery.min.css",
                        $URL_ROOT."admin/css/jquery.fileupload-ui.css",
                        $URL_ROOT."admin/js/shadowbox/shadowbox.css",
                        $URL_ROOT."admin/css/DT_bootstrap.css",
                        "http://twitter.github.com/bootstrap/assets/css/bootstrap.css",
                        $URL_ROOT."admin/js/jwysiwyg/jquery.wysiwyg.css",
                        $URL_ROOT."admin/js/jwysiwyg/plugins/fileManager/wysiwyg.fileManager.css",
                        $URL_ROOT."admin/css/main.css",
                    );
$js_files = array   (
                        "//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js",
                        $URL_ROOT."admin/js/jquery-ui.min.js",
                        $URL_ROOT."admin/js/jquery.ui.datepicker-de.js",
                        $URL_ROOT."admin/js/vendor/jquery.ui.widget.js",
                        "http://blueimp.github.com/JavaScript-Templates/tmpl.min.js",
                        "http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js",
                        "http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js",
                        "http://blueimp.github.com/cdn/js/bootstrap.min.js",
                        "http://blueimp.github.com/Bootstrap-Image-Gallery/js/bootstrap-image-gallery.min.js",
                        "http://twitter.github.com/bootstrap/assets/js/bootstrap-tab.js",
                        $URL_ROOT."admin/js/upload/jquery.iframe-transport.js",
                        $URL_ROOT."admin/js/upload/jquery.fileupload.js",
                        $URL_ROOT."admin/js/upload/jquery.fileupload-fp.js",
                        $URL_ROOT."admin/js/upload/jquery.fileupload-ui.js",
                        $URL_ROOT."admin/js/upload/locale_de.js",
                        $URL_ROOT."admin/js/shadowbox/shadowbox.js",
                        $URL_ROOT."admin/js/jquery.form.js",
                        $URL_ROOT."admin/js/datatables/js/jquery.dataTables.js",
                        $URL_ROOT."admin/js/DT_bootstrap.js",
                        $URL_ROOT."admin/js/jwysiwyg/jquery.wysiwyg.js",
                        $URL_ROOT."admin/js/jwysiwyg/plugins/wysiwyg.fullscreen.js",
                        $URL_ROOT."admin/js/jwysiwyg/plugins/wysiwyg.i18n.js",
                        $URL_ROOT."admin/js/jwysiwyg/plugins/wysiwyg.rmFormat.js",
                        $URL_ROOT."admin/js/jwysiwyg/plugins/wysiwyg.fileManager.js",
                        $URL_ROOT."admin/js/jwysiwyg/controls/wysiwyg.colorpicker.js",
                        $URL_ROOT."admin/js/jwysiwyg/controls/wysiwyg.image.js",
                        $URL_ROOT."admin/js/jwysiwyg/controls/wysiwyg.link.js",
                        $URL_ROOT."admin/js/jwysiwyg/controls/wysiwyg.table.js",
                        $URL_ROOT."admin/js/jwysiwyg/i18n/lang.de.js",
                        $URL_ROOT."admin/js/jstree/_lib/jquery.cookie.js",
                        $URL_ROOT."admin/js/jstree/_lib/jquery.hotkeys.js",
                        $URL_ROOT."admin/js/jstree/jquery.jstree.js",
                        $URL_ROOT."admin/js/main.js"
                    );

$page_title = "Wireframe Admin";
$content = preg_split("/\//", str_replace(array($URL_ROOT,"//"),array("","%2F"),"http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]));
foreach ($content as $id => $con){
        if (!$con || $con[0] == "?")unset($content[$id]);
        else $content[$id] = urldecode($con);
}
require_once 'header.php';
$auth = new auth();
if ($_SESSION["_registry"]["user"]['name'] && $_SESSION["_registry"]["user"]['pass']){
    if ($auth->check($_SESSION["_registry"]["user"]['name'],$_SESSION["_registry"]["user"]['pass'])){
        if(!isset($content[1])) echo '<script type="text/javascript">location=\'content/\'</script>';
        require "includes/backend.php";     
    }
    else{
        $auth->print_login_form();
        }
}
elseif($_POST["login"]){
    if ($_POST["user"] && $_POST["pass"]){
        $auth->login($_POST["user"],$_POST["pass"]); 
    }
    else{$auth->print_login_form($lang["backend"]["fillboth"]);}
}
else{
    if("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] != $_SESSION["_registry"]["system_config"]["site"]["base_url"]."admin/"){
        echo '<script type="text/javascript">window.location="'.$_SESSION["_registry"]["system_config"]["site"]["base_url"].'admin/"; </script>';
    }
    echo $auth->print_login_form();}
require_once 'footer.php';
?>