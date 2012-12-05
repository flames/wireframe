<?php
error_reporting(E_ERROR | E_PARSE);
function __autoload($class)
{
    $classpath = pathinfo(__FILE__,PATHINFO_DIRNAME).'/classes/'.$class.'.class.php';
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

if($_GET["reset"]){
        $registry->reset();
}

if($_SESSION["_registry"]["section"] == "frontend"){
        $root = str_replace ('/admin/includes', '', pathinfo(__FILE__,PATHINFO_DIRNAME));
        unset ($_SESSION["_registry"]["lang"]);
        $registry->lang = parse_ini_file("$root/admin/localisation/de.ini", TRUE);
        unset ($_SESSION["_registry"]["section"]);
        $registry->section = "backend";
}



if($_POST["logout"]){
        unset($_SESSION["_registry"]["user"]);
}

if (empty($_SESSION["_registry"])){
        $root = str_replace ('/admin/includes', '', pathinfo(__FILE__,PATHINFO_DIRNAME));
    if ($handle = opendir($root.'/admin/config')) {
        while (false !== ($file = readdir($handle))) {
            if (preg_match("/.*\.ini/", $file, $hit)) {
                                $name = preg_replace('/\.ini/', '', $hit[0]);
                                $registry->$name  = parse_ini_file($root."/admin/config/".$file, TRUE);
            }
        }
        closedir($handle);
    }
        
        $registry->lang = parse_ini_file($root."/admin/localisation/de.ini", TRUE);
        $registry->root = $root;
        $registry->section = "backend";
}
unset ($_SESSION["_registry"]["db"]);
$registry->db = new db();
$registry->section = "admin";
unset ($_SESSION["_registry"]["time"]);
$registry->time = new time();
$REGISTRY = $_SESSION["_registry"];
$LANG = $REGISTRY["lang"];
$DEBUG = $REGISTRY["system_config"]["debug"];
$DIR_ROOT = $REGISTRY["root"];
$DB = $REGISTRY["db"];
$URL_ROOT = $REGISTRY["system_config"]["site"]["base_url"];
setlocale(LC_ALL, $LANG["language"]["locale"]);
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 1);   
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

if(isset($_SESSION["_registry"]["user"])){
    $permissions = new permissions();
}

if(is_file( pathinfo(__FILE__,PATHINFO_DIRNAME).'/custom_functions.inc.php')) include pathinfo(__FILE__,PATHINFO_DIRNAME).'/custom_functions.inc.php';
include pathinfo(__FILE__,PATHINFO_DIRNAME).'/functions.inc.php';
?>
