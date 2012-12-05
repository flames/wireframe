<?php
include 'general.inc.php';
$user = user_details($_SESSION["_registry"]["user"]['name']);
//error_reporting(0);
switch ($_POST["action"]){
     /////////////////////
    ///User functions///
    ///////////////////
    case "login":
        if (($_POST["username"] == "" || $_POST["password"] == "password" ) && isset($_POST["login"])) {
            $error_msg = "Sie müssen beide Felder ausfüllen.";
            unset ($_SESSION["_registry"]["user"]);
        }
        $auth = new auth();
        $status = $auth->check_status($_POST["username"]);
        if ($status) $error_msg = $status;
        else{
            if($auth->check($_POST["username"],$_POST["password"])){
                    $_SESSION["_registry"]["user"] = array();
                    $_SESSION["_registry"]["user"]['name']  = $_POST["username"];
                    $_SESSION["_registry"]["user"]['pass']  = $_POST["password"];
                    $_SESSION["_registry"]["user"]['group'] = $DB->query_fetch_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '".$_POST["username"]."' AND TYPE = 1 LIMIT 1;");
                    $_SESSION["_registry"]["user"]['id']  = $DB->query_fetch_single("SELECT id FROM permissions_entity WHERE `name` = '".$_POST["username"]."' LIMIT 1;");
                }
                else {
                    $error_msg = "Der eingegebene Benutzername oder das Passwort sind nicht korrekt.";
                    unset ($_SESSION["_registry"]["user"]);
                }
        }
        if($error_msg) {sleep(1); echo $error_msg;}
        break;
        case 'logout':
            unset ($_SESSION["_registry"]["user"]);
            break;
        case 'save_userdata':
            $user_tmp = explode("&", $_POST["data"]);
            $user_data = array();
            foreach ($user_tmp as $field => $value){
                $pair = explode("=", $value);
                $user_data[$pair[0]] = urldecode($pair[1]);
            }
            if(!isset($user_data["use_del"])) $user_data["use_del"] = 0;
            $query .= "SELECT * FROM permissions_entity";
            $query .=" WHERE id = '".$user_data["id"]."';";
            $user = $DB->query_fetch($query,MYSQLI_ASSOC, FALSE);
        foreach ($user_data as $field => $value){
            if ($field != "name" && $field != "password" && $field != "password2") {
                $row = $DB->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = '".$field."' AND type = '1' LIMIT 1;");
                if($DB->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', '".$field."', '', '".mysql_escape_string($value)."');";
                else                        $query = "UPDATE permissions SET value = '".mysql_escape_string($value)."' WHERE name = '".$user["name"]."' AND permission = '".$field."' AND type = '1' LIMIT 1;";
                $DB->query($query);
            }
            elseif ($field == "name"){
                if ($value != $user["name"]){
                    $query = "UPDATE permissions_inheritance SET child = '".$value."' WHERE child = '".$user["name"]."';";
                    $DB->query($query);
                    $query = "UPDATE permissions_inheritance SET parent = '".$value."' WHERE parent = '".$user["name"]."';";
                    $DB->query($query);
                    $query = "UPDATE permissions_entity SET name = '".$value."' WHERE name = '".$user["name"]."' LIMIT 1;";
                    $DB->query($query);
                    $query = "UPDATE permissions SET name = '".$value."' WHERE name = '".$user["name"]."';";
                    $DB->query($query);
                    $user["name"] = $value;
                }
            }
            elseif ($field == "password" && $value){
                $auth             =  new auth();
                $password = $auth->crypt($value);
                $row = $DB->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;");
                if($DB->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', 'password', '', '".$password."');";
                else                        $query = "UPDATE permissions SET value = '".$password."' WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;";
                $DB->query($query);
            }
        }
        print("Ihre Daten wurden gespeichert.") ;
            break;
        case 'register_userdata':

            $user_tmp = explode("&", $_POST["data"]);
            $user_data = array();
            foreach ($user_tmp as $field => $value){
                $pair = explode("=", $value);
                $user_data[$pair[0]] = urldecode($pair[1]);
            }
            $query = "INSERT INTO `permissions_inheritance` (`id`, `child`, `parent`, `type`) VALUES (NULL, '".$user_data['name']."', 'Kunde', '1');
                ";
            $DB->query($query);
            $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user_data['name']."', '1', 'status', '', '1');
                ";
            $DB->query($query);
            $query = "INSERT INTO `permissions_entity` (`id`, `name`, `type`, `prefix`, `suffix`, `default`) VALUES (NULL, '".$user_data['name']."', '1', '', '', '0');
                ";
            $DB->query($query);
            $id = $DB->lastindex();
            $query = "SELECT * FROM permissions_entity";
            $query .=" WHERE id = '".$id."';";
            $user = $DB->query_fetch($query,MYSQLI_ASSOC, FALSE);
            unset($user_data['name']);
        foreach ($user_data as $field => $value){
            if ($field != "name" && $field != "password" && $field != "password2") {
                $row = $DB->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = '".$field."' AND type = '1' LIMIT 1;");
                if($DB->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', '".$field."', '', '".mysql_escape_string($value)."');";
                else                        $query = "UPDATE permissions SET value = '".mysql_escape_string($value)."' WHERE name = '".$user["name"]."' AND permission = '".$field."' AND type = '1' LIMIT 1;";
                $DB->query($query);
            }
            elseif ($field == "password" && $value){
                $auth             =  new auth();
                $password = $auth->crypt($value);
                $row = $DB->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;");
                if($DB->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', 'password', '', '".$password."');";
                else                        $query = "UPDATE permissions SET value = '".$password."' WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;";
                $DB->query($query);
            }
        }
        echo "Ihre Daten wurden gespeichert, sie könenn sich nun anmelden." ;
        break;
}
?>