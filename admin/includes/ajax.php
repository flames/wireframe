<?php
include 'general.inc.php';
$base_root = $_SESSION["_registry"]["root"]."/";
error_reporting(0);


function delete($table,$id){
    global $DB;
    $DB->query("DELETE FROM $table WHERE id=$id LIMIT 1;");
}

function get_tree($table, $table_field, $master = 0,$search = FALSE,$parent_table = FALSE, $parent_field){
        global $DB;
        if($search) {$search_string = " AND $tablefield LIKE '%".$search["name"]."%' ";}
        if($parent_table){
            $sub_folders = $DB->select("SELECT id, $parent_field as label FROM $parent_table WHERE parent = $master ORDER BY $parent_field;");
        }
        else{
            $sub_folders = $DB->select("SELECT id, $table_field as label FROM $table WHERE parent = $master $search_string ORDER BY `order`;");  
        }
      if($sub_folders){
         foreach ($sub_folders as $key => $folder){
            if(isset($folder["label"])) $sub_folders[$key]["label"] = $folder["label"];
            $children = get_tree($table, $table_field,$folder["id"],$search);
            if($children) $sub_folders[$key]["children"] = $children;
         }
      }
      if(count($sub_files) || count($sub_folders)) return $sub_folders;
   }

if($_POST["action"] == "gen_pass"){
     function CreatePassword($length = 7, $capitals = true, $specialSigns = true)
  {
    $array = array();


    if($length < 8)
      $length = mt_rand(8,20);
    for($i=48;$i<58;$i++)
      $array[] = chr($i);
    for($i=97;$i<122;$i++)
      $array[] = chr($i);
    if($capitals )
      for($i=65;$i<90;$i++)
        $array[] = chr($i);
    if($specialSigns)
    {
      for($i=33;$i<47;$i++)
        $array[] = chr($i);
      for($i=59;$i<64;$i++)
        $array[] = chr($i);
      for($i=91;$i<96;$i++)
        $array[] = chr($i);
      for($i=123;$i<126;$i++)
        $array[] = chr($i);
    }
    mt_srand((double)microtime()*1000000);
    $passwort = '';
    for ($i=1; $i<=$length; $i++)
    {
      $rnd = mt_rand( 0, count($array)-1 );
      $passwort .= $array[$rnd];
    }
    echo $passwort;
    exit;
  }
}
else if($_POST["action"] == "change_order"){
    $old_position = $DB->query_fetch_single("SELECT `order` FROM ".$_POST["table"]." WHERE id=".$_POST["id"]." LIMIT 1");
    if($_POST["value"] < $old_position){
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = `order`+1 WHERE `order` BETWEEN ".$_POST["value"]." AND $old_position - 1;");
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = ".$_POST["value"]." WHERE `ID` = ".$_POST["id"]." LIMIT 1;");
    }
    else if ($_POST["value"] > $old_position){
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = `order`-1 WHERE `order` BETWEEN ".$_POST["value"] ." AND $old_position + 1;");
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = ".$_POST["value"]." WHERE `ID` = ".$_POST["id"]." LIMIT 1;");
    }
}
else if($_POST["action"] == "change_order_parent"){
    $values = split(',',$_POST["value"]);
    $old_position = $DB->query_fetch_single("SELECT `order` FROM ".$_POST["table"]." WHERE id=".$_POST["id"]." LIMIT 1");
    if($_POST["value"] < $old_position){
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = `order`+1 WHERE (`order` BETWEEN ".$values[0]." AND $old_position - 1) AND parent = ".$values[1].";");
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = ".$values[0]." WHERE `ID` = ".$_POST["id"]." LIMIT 1;");
    }
    else if ($_POST["value"] > $old_position){
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = `order`-1 WHERE (`order` BETWEEN ".$values[0] ." AND $old_position + 1) AND parent = ".$values[1].";");
        $DB->query("UPDATE `".$_POST["table"]."` SET `order` = ".$values[0]." WHERE `ID` = ".$_POST["id"]." LIMIT 1;");
    }
}
else if($_POST["action"] == "delete"){
    delete($_POST["table"],$_POST["id"]);
}
else if($_POST["action"] == "delete_entity"){
        $entity = $_POST["id"];
        $query ="DELETE FROM permissions WHERE name = '".$entity."'";
        $DB->query($query);
        $result .= $query.'
        ';
        $query ="DELETE FROM permissions_entity WHERE name = '".$entity."';";
        $DB->query($query);
        $result .= $query.'
        ';
        $query ="DELETE FROM permissions_inheritance WHERE child = '".$entity."' OR parent = '".$entity."';";
        $DB->query($query);
        $result .= $query.'
        ';
        echo $result;
}
else if($_POST["action"] == "change_status"){
    $DB->query("UPDATE ".$_POST["table"]." set `status` = ".$_POST["value"] ." WHERE id=".$_POST["id"]." LIMIT 1;");
}
else if($_POST["action"] == "change_entity_status"){
    if($DB->affected_query("SELECT id FROM permissions WHERE permission = 'status' AND name = '".$_POST["id"]."' LIMIT 1;"))
    $DB->query("UPDATE permissions set `value` = ".$_POST["value"] ." WHERE permission = 'status' AND name = '".$_POST["id"]."' LIMIT 1;");
    else
    $DB->query("INSERT INTO permissions (
                `name` ,
                `type` ,
                `permission` ,
                `value`
             )
            VALUES (
                '".$_POST["id"]."', '1', 'status', '".$_POST["value"] ."'
            );");
}
else if($_POST["action"] == "change_group_status"){

    if($DB->affected_query("SELECT id FROM permissions WHERE permission = 'status' AND name = '".$_POST["id"]."' LIMIT 1;"))
    $DB->query("UPDATE permissions set `value` = ".$_POST["value"] ." WHERE permission = 'status' AND name = '".$_POST["id"]."' LIMIT 1;");

    else
    $DB->query("INSERT INTO permissions (
                `name` ,
                `type` ,
                `permission` ,
                `value`
             )
            VALUES (
                '".$_POST["id"]."', '0', 'status', '".$_POST["value"] ."'
            );");


}
else if($_POST["action"] == "delete_mass"){
    foreach($_POST["to_del"] as $del){
        delete($_POST["table"],$del);
    }
}
else if($_POST["action"] == "gen_code"){
    echo token(20);
}
else if($_POST["action"] == "get_tree"){
    header("HTTP/1.0 200 OK");
    header('Content-type: application/json; charset=utf-8');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Pragma: no-cache");
    $modul = new modul();
    $tree = $modul->getTree(unserialize($_POST["fields"]),$_POST["field"], $_POST["id"],$_POST["table"]);
    echo $modul->json_nodes(unserialize($_POST["fields"]),$tree);
    die();
}

else if($_GET["action"] == "gen_tree"){
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
    print_r(json_encode(get_tree($_GET["table"], $_GET["table_field"], 0,$_GET["search"],$_GET["parent_table"], $_GET["parent_field"])));
}
else if($_POST["action"] == "move_tree"){
    $target = $DB->query_fetch("SELECT id, `order`, parent FROM ".$_POST["table"]." WHERE id=".$_POST["target"]." LIMIT 1");
    switch($_POST["position"]){
        case "inside":
            $new_position = 1;
            $parent = $target["id"];
            break;
        case "after":
            $parent = $target["parent"];
            break;
        case "before":
            $parent = $target["parent"];
            break;
    }
    $childs = $DB->select("SELECT id FROM ".$_POST["table"]." WHERE parent = $parent AND id != ".$_POST["moved"]." ORDER BY `order`;");
    $i = 0;
    foreach ($childs as $child){
        $i++;
        $DB->query("UPDATE ".$_POST["table"]." SET `order` = $i WHERE id = ".$child["id"]." LIMIT 1;");
        if($_POST["target"] == $child["id"]){
            switch($_POST["position"]){
                case "after":
                    $DB->query("UPDATE ".$_POST["table"]." SET `order` = ".($i + 1).", parent = $parent WHERE id = ".$_POST["moved"]." LIMIT 1;");
                    $i++;
                    break;
                case "before":
                    $DB->query("UPDATE ".$_POST["table"]." SET `order` = ".($i).", parent = $parent WHERE id = ".$_POST["moved"]." LIMIT 1;");
                    $DB->query("UPDATE ".$_POST["table"]." SET `order` = ".($i + 1)." WHERE id = ".$child["id"]." LIMIT 1;");
                    $i++;
                    break;
            }
        }
    }
    echo $new_position;
}
?>