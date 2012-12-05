<?php
error_reporting(0);
function __autoload($class)
{
    $classpath = pathinfo(__FILE__,PATHINFO_DIRNAME).'/../admin/includes/classes/'.$class.'.class.php';
    if ($_SESSION["_registry"]["system_config"]["debug"]["debug_mode"] && $_SESSION["_registry"]["system_config"]["debug"]["classloads"])
    echo '<p class="debug_msg" style="color:orange;"><b>Try to load class: '.$classpath.'</b></p>';
    if (file_exists($classpath)){
        include_once($classpath);
        if ($_SESSION["_registry"]["system_config"]["debug"]["debug_mode"] && $_SESSION["_registry"]["system_config"]["debug"]["classloads"])
        echo '<p class="debug_msg" style="color:green;"><b>'.$class.' loaded</b></p>';
    }
    else{
        echo '<p class="debug_msg" style="color:red;"><b>CAN NOT LOAD CLASS '.$classpath.'. FILE NOT FOUND </b></p>';
    }
}

$registry = registry::getInstance();
$root = str_replace ('/includes', '', pathinfo(__FILE__,PATHINFO_DIRNAME));
unset ($_SESSION["_registry"]["root"]);


if(isset($_GET["reset"]) && $_GET["reset"]){
        $registry->reset();
}

if(isset($_SESSION["_registry"]["section"]) && $_SESSION["_registry"]["section"] == "backend"){
     if (isset($language)) {
        $registry->lang = parse_ini_file($root."/localisation/$language.ini", TRUE);
        $registry->section = "frontend";
     }
}

if(isset($_GET["logout"]) && $_POST["logout"]){
        unset($_SESSION["_registry"]["user"]);
}

if (empty($_SESSION["_registry"])){
        //$root = str_replace ('/includes', '', pathinfo(__FILE__,PATHINFO_DIRNAME));
    $root = preg_replace('#[/\\\]includes#', '',pathinfo(__FILE__,PATHINFO_DIRNAME));
    if ($handle = opendir($root.'/admin/config')) {
        while (false !== ($file = readdir($handle))) {
            
            if (preg_match("/.*\.ini/", $file, $hit)) {
                                $name = preg_replace('/\.ini/', '', $hit[0]);
                                $registry->$name  = parse_ini_file($root."/admin/config/".$file, TRUE);
            }
        }
        closedir($handle);
    } 
        $registry->lang = parse_ini_file($root."/localisation/de.ini", TRUE);
        $registry->root = $root;
        $registry->section = "frontend";
}
$registry->root = $root;
if($_GET["language"]){
    unset ($_SESSION["_registry"]["lang"]);
    $_SESSION["_registry"]["lang"] = parse_ini_file($root."/localisation/".$_GET["language"].".ini", TRUE);
}
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 1); 
unset ($_SESSION["_registry"]["db"]);
$registry->db = new db();
unset ($_SESSION["_registry"]["time"]);
$registry->time = new time();
$PERMISSIONS = new permissions();
$REGISTRY = $_SESSION["_registry"];
$LANG = $REGISTRY["lang"];
$DEBUG = $REGISTRY["system_config"]["debug"];
$DIR_ROOT = $root;
$DB = $REGISTRY["db"];
$URL_ROOT = $REGISTRY["system_config"]["site"]["base_url"];
$IMAGE = new image();
$TIME = new time();
if(!$REGISTRY["user"]["name"]) $LOGGED = FALSE;
else $LOGGED = TRUE;
if ($debug["debug_mode"] && $debug["configloads"]){
    echo "<br>";
    foreach($REGISTRY as $config => $configFlags){
        if (preg_match("/.*_config/", $config, $hit)) {
            echo '<p class="debug_msg" style="color:green;"><b>'.$hit[0].' loaded</b></p>';
        }
    }
}
if ($debug["debug_mode"] && $debug["showlang"]){
            echo '<p class="debug_msg" style="color:green;"><b>loaded language: '.$lang["language"]["name"].' ('.$lang["language"]["shortname"].')</b></p>';
}

if(is_file( pathinfo(__FILE__,PATHINFO_DIRNAME).'/custom_functions.inc.php')) include pathinfo(__FILE__,PATHINFO_DIRNAME).'/custom_functions.inc.php';
include pathinfo(__FILE__,PATHINFO_DIRNAME).'/../admin/includes/functions.php';
if(!substr_count("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],$URL_ROOT)) header("location: $URL_ROOT");
?>