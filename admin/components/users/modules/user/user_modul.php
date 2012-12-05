<?php
include_once 'includes/classes/modul.class.php';
class user_modul extends modul{
  
    function __construct(){
        $this->init();
        $this->path             =  pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   =  parse_ini_file( $this->path."localisation/de.ini");
		$this->config			= parse_ini_file( $this->path."modul.ini");
		$this->permission		= $this->config["permission"];
        $this->table       = "permissions";
    }
        /**
    * Liest die Tabelle aus
    *
    * @param  array  $fields Feldarray
    * @param  array  $filters Filterarray
    * @param  string  $table
    * @return array  assoziatives array der Tabelle
    */
   public function getTable ($fields=FALSE,$filters=FALSE, $table = FALSE){
		$search = $_GET["search"];
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE type = 1";
		        if ($search){
                $query .= " AND `name` LIKE '%".$search."%'";
        }
        $users = $this->db->select($query,MYSQLI_ASSOC, FALSE, "name");
        $query = "SELECT *  FROM permissions WHERE permission != 'name' AND type = 1 AND (";
         if ($fields){
            $first = TRUE;
            foreach($fields as $field){
                if (!$first) $query .= " OR ";
                $query .= '(permission LIKE \''.$field[2].'\' AND value != "")';
                $first = FALSE;
            }
        }
        $query .= ")";
        if ($filters){
            $first = TRUE;
            foreach ($filters as $filter){
                $query .= " AND `".$filter["0"]."`".$filter["1"]."'".$filter["2"]."'";
                $first = FALSE;
            }
            if ($search){
                $query .= " AND ";
            }
            $query .= " )";
        }
        if ($search){
                $query .= " AND `name` LIKE '%".$search."%'";
        }
        $order = FALSE;
        $direction = FALSE;
        $NOORDER = TRUE;
        if (!$_REQUEST["order"]["field"] && !$NOORDER) $order = "order_id"; else $order = $_REQUEST["order"]["field"];
        if (!$_REQUEST["order"]["direction"] && !$NOORDER) $direction = "ASC"; else $direction = $_REQUEST["order"]["direction"];
        
        if ($order){
            $query .= ' ORDER BY '.$order;
        }
        if ($direction){
            $query .= ' '.$direction;
        }
        $query .= ';';
        $options = $this->db->select($query);
        foreach ($options as $value){
            $users[$value["name"]][$value["permission"]] = $value["value"];
        }
        
        $query = "SELECT * FROM permissions_inheritance WHERE type = 1 AND child LIKE '%".$search."%'";
		//echo $query;
        $groups = $this->db->select($query,MYSQLI_ASSOC, FALSE, "child");
        foreach ($groups as $key => $value){
            $users[$key]["group"] = $value["parent"];
        }
        return $users;
    }

    public function edit_buttons($id,$css_id){
                $html.='<div id="'.$css_id.'" class="btn-group" style="margin:0 0 10px 0;">';
        if ($this->permissions->hasPermission($this->permission.".edit")) $html.='
                <button class="btn btn-success start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'save_btn\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-check icon-white"></i>
                    <span>Speichern</span>
                </button>
                <button class="btn btn-primary start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'saveback\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-share icon-white"></i>
                    <span>Speichern und zurück</span>
                </button>
                <button class="btn btn-info start" onclick="open_iframe(\''.$this->base_url.'admin/components/users/permissions.php?id='.$id.'\',600,600);">
                    <i class="icon-asterisk icon-white"></i>
                    <span>Rechte verwalten</span>
                </button>';
        $html.='
                <button class="btn btn-warning cancel" onclick="window.location = \''.$_SESSION["_registry"]["variables"]["backlink"].'\'">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>abbrechen</span>
                </button>
                </div>
                ';
            return $html;
    }

     public function getEntity($id, $fields=FALSE, $table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
         $query = "SELECT *  FROM permissions WHERE name ='".$user["name"]."' AND type = 1 AND (";
         if ($fields){
            $first = TRUE;
            foreach($fields as $field){
                if (!$first) $query .= " OR ";
                $query .= '(permission LIKE \''.$field[2].'\' AND value != "")';
                $first = FALSE;
            }
        }
        $query .= ")";
        $options = $this->db->select($query);
        foreach ($options as $value){
            $user_data[$value["permission"]] = $value["value"];
        }
        $query = "SELECT parent FROM permissions_inheritance WHERE type = 1 AND child = '".$user["name"]."' LIMIT 1;";
        $user_data["name"] = $user["name"];   
        $user_data["group"] = $this->db->query_fetch($query);
                if ($user_data["group"]["parent"] != "") $user_data["group"] = $user_data["group"]["parent"];
        else $user_data["group"] = FALSE;

        return $user_data;
    }


     public function getEntityStructured($id, $fields=FALSE, $table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        //echo $query."<br>";
        $user = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
         $query = "SELECT *  FROM permissions WHERE name ='".$user["name"]."' AND type = 1 AND (";
         if ($fields){
            $first = TRUE;
            foreach($fields as $sub_fields){
                foreach ($sub_fields as $field){
                    if (!$first) $query .= " OR ";
                    $query .= '(permission LIKE \''.$field[2].'\' AND value != "")';
                    $first = FALSE;
                }
            }
        }
        $query .= ")";
        $options = $this->db->select($query);
        foreach ($options as $value){
            $user_data[$value["permission"]] = $value["value"];
        }
        $query = "SELECT parent FROM permissions_inheritance WHERE type = 1 AND child = '".$user["name"]."' LIMIT 1;";
        $user_data["name"] = $user["name"];   
        $user_data["group"] = $this->db->query_fetch($query);
                if ($user_data["group"]["parent"] != "") $user_data["group"] = $user_data["group"]["parent"];
        else $user_data["group"] = FALSE;

        return $user_data;
    }

    public function saveEntity($id, $fields=FALSE, $table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        foreach ($fields as $field){
            if ($field[2] != "name" && $field[2] != "group" && $field[2] != "password") {
                $row = $this->db->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = '".$field[2]."' AND type = '1' LIMIT 1;");
                if($this->db->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', '".$field[2]."', '', '".mysql_escape_string($_POST["save"][$field[2]])."');";
                else                        $query = "UPDATE permissions SET value = '".mysql_escape_string($_POST["save"][$field[2]])."' WHERE name = '".$user["name"]."' AND permission = '".$field[2]."' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }
            elseif ($field[2] == "group"){
                $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE child = '".$user["name"]."' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }            
            elseif ($field[2] == "name"){
                if ($_POST["save"][$field[2]] != $user["name"]){
                    $query = "UPDATE permissions_inheritance SET child = '".$_POST["save"][$field[2]]."' WHERE child = '".$user["name"]."';";
                    $this->db->query($query);
                    $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE parent = '".$user["name"]."';";
                    $this->db->query($query);
                    $query = "UPDATE permissions_entity SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$user["name"]."' LIMIT 1;";
                    $this->db->query($query);
                    $query = "UPDATE permissions SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$user["name"]."';";
                    $this->db->query($query);
                    $user["name"] = $_POST["save"][$field[2]];
                }
            }
            elseif ($field[2] == "password" && $_POST["save"][$field[2]] != ""){
                $auth             =  new auth();
                $password = $auth->crypt($_POST["save"][$field[2]]);
                $row = $this->db->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;");
                if($this->db->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', 'password', '', '".$password."');";
                else                        $query = "UPDATE permissions SET value = '".$password."' WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }
        }
    }
    public function saveEntityStructured($id, $fields=FALSE, $table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        foreach ($fields as $sub_fields){
            foreach ($sub_fields as $field){
            if ($field[2] != "name" && $field[2] != "group" && $field[2] != "password") {
                $row = $this->db->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = '".$field[2]."' AND type = '1' LIMIT 1;");
                if($this->db->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', '".$field[2]."', '', '".mysql_escape_string($_POST["save"][$field[2]])."');";
                else                        $query = "UPDATE permissions SET value = '".mysql_escape_string($_POST["save"][$field[2]])."' WHERE name = '".$user["name"]."' AND permission = '".$field[2]."' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }
            elseif ($field[2] == "group"){
                $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE child = '".$user["name"]."' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }            
            elseif ($field[2] == "name"){
                if ($_POST["save"][$field[2]] != $user["name"]){
                    $query = "UPDATE permissions_inheritance SET child = '".$_POST["save"][$field[2]]."' WHERE child = '".$user["name"]."';";
                    $this->db->query($query);
                    $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE parent = '".$user["name"]."';";
                    $this->db->query($query);
                    $query = "UPDATE permissions_entity SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$user["name"]."' LIMIT 1;";
                    $this->db->query($query);
                    $query = "UPDATE permissions SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$user["name"]."';";
                    $this->db->query($query);
                    $user["name"] = $_POST["save"][$field[2]];
                }
            }
            elseif ($field[2] == "password" && $_POST["save"][$field[2]] != ""){
                $auth             =  new auth();
                $password = $auth->crypt($_POST["save"][$field[2]]);
                $row = $this->db->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;");
                if($this->db->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$user["name"]."', '1', 'password', '', '".$password."');";
                else                        $query = "UPDATE permissions SET value = '".$password."' WHERE name = '".$user["name"]."' AND permission = 'password' AND type = '1' LIMIT 1;";
                $this->db->query($query);
            }
        }
        }
    }
    
    public function deleteEntity($id, $table = FALSE){
        $query = "SELECT name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch_single($query,MYSQLI_ASSOC, FALSE);
        if (!$table) $table = $this->table;
        $return = TRUE;
        $query ="DELETE FROM permissions
                WHERE name = '".$user."' AND type = 1;";
        if (!$this->db->query($query)) $return = FALSE;
        
        $query ="DELETE FROM permissions_entity
                WHERE name = '".$user."' AND type = 1 LIMIT 1;";
        if (!$this->db->query($query)) $return = FALSE;
        
        $query ="DELETE FROM permissions_inheritance
                WHERE child = '".$user."' AND type = 1;";
        if (!$this->db->query($query)) $return = FALSE;
    }
    
    public function statusEntity($id,$to,$table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        $this->db->query("SELECT id FROM permissions WHERE name = '".$user["name"]."' AND permission = 'status' LIMIT 1;");
        if($this->db->affected() < 1)   
                $query = "  INSERT INTO `permissions` 
                            (`id`, `name`, `type`, `permission`, `world`, `value`) 
                            VALUES 
                            (NULL, '".$user["name"]."', '1', 'status', '', '".$to."');";
        else    $query ="   Update permissions
                            SET value = ".$to."
                            WHERE permission = 'status' AND name = '".$user["name"]."';";
        return $this->db->query($query);
    }
    
    public function getEditField_UserGroup($id,$field,$entity){
        $query = "SELECT name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $user = $this->db->query_fetch_single($query,MYSQLI_ASSOC, FALSE);
        $query = "SELECT parent FROM permissions_inheritance WHERE type = 1 AND child = '".$user."' LIMIT 1;";
        $user_group = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        if ($user_group["parent"] != "") $user_group = $user_group["parent"];
        else $user_group = FALSE;
        
        $query = "SELECT * FROM permissions_entity WHERE type = 0";
        $groups = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = '<select name="save['.$field[2].']">
                    <option value="">None</option>';
        foreach ($groups as $group){
            $html .= '<option value="'.$group["name"].'" ';
                if ($user_group == $group["name"]) $html .= 'selected="selected"';
            $html .= '>'.$group["name"].'</option>';
        }
        $html .= '</select>';
        return $html;
    }
    
    public function getEditField_Password($id,$field,$entity){
        $html = '<input type="password" name="save['.$field[2].']" />';
        return $html;
    }
    
      public function newEntity($fields=FALSE, $table = FALSE){
        //$searches = $this->getSearch();
        $query = "INSERT INTO `permissions_inheritance` (`id`, `child`, `parent`, `type`) VALUES (NULL, 'newUser', '', '1');
                ";
        $this->db->query($query);
        $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, 'newUser', '1', 'status', '', '2');
                ";
        $this->db->query($query);
        $query = "INSERT INTO `permissions_entity` (`id`, `name`, `type`, `prefix`, `suffix`, `default`) VALUES (NULL, 'newUser', '1', '', '', '0');
                ";
        $this->db->query($query);
        return $this->db->lastindex();
    }

    public function getButton_Status3($row){
        if (!$this->permissions->hasPermission($this->permission.".edit")) return false;
        $button = '
                        <button id="status3_'.$row["id"].'" title="Status" class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">
                            <i class="icon-off icon-white"></i>
                        </button>
                        <ul id="status3_container_'.$row["id"].'" class="dropdown-menu status_dropdown">
                        <li><img title="gesperrt" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_rot.gif" title="inactive" onclick="ajax_action(\'change_entity_status\',\''.$this->table.'\',\''.$row["name"].'\',0)"/></li>
                        <li><img title="inaktiv" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_gruen.gif" title="active" onclick="ajax_action(\'change_entity_status\',\''.$this->table.'\',\''.$row["name"].'\',1)"/></li>                          
                        <li><img title="aktiv" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_gelb.gif" title="wait" onclick="ajax_action(\'change_entity_status\',\''.$this->table.'\',\''.$row["name"].'\',2)"/></li>
                        </ul>      
                        <script>
                            var position = $("#status3_'.$row["id"].'").position();
                            $("#status3_container_'.$row["id"].'").css("left",position.left + "px");
                        </script>

';
        return $button;
    }     
    public function getButton_Delete($row){ 
        if (!$this->permissions->hasPermission($this->permission.".del") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="Löschen" type="button" id="delete_button_'.$row["id"].'" class="del_button btn btn-danger" onclick="var r=confirm(\''.$this->lang["backend"]["delete_entry"].'\'); if (r==true) ajax_action(\'delete_entity\',\''.$this->table.'\',\''.$row["name"].'\');">
                        <i class="icon-trash icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    }    
}
?>
