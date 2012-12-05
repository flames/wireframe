<?php
include_once 'includes/classes/modul.class.php';
class group_modul extends modul{

    function __construct(){
        $this->init();
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->base_url         = $_SESSION["_registry"]["system_config"]["site"]["base_url"];
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
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE type = 0;";
        $groups = $this->db->select($query,MYSQLI_ASSOC, FALSE, "name");
        $query = "SELECT *  FROM permissions WHERE type = 0 AND (";
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
        if ($searches){
                $first = TRUE;
                foreach ($searches as $search){
                if (!$first) $query .= " AND ";
                $query .= "`".$search["name"]."`".$search["operand"]."'".$search["value"];
                $first = FALSE;
                }
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
            $groups[$value["name"]][$value["permission"]] = $value["value"];
        }
        
        $query = "SELECT * FROM permissions_inheritance WHERE type = 0";
        $groups_parent = $this->db->select($query,MYSQLI_ASSOC, FALSE, "child");
        foreach ($groups_parent as $key => $value){
            $groups[$key]["parent"] = $value["parent"];
        }
        return $groups;
    }

    public function edit_buttons($id){
                $html.='<div class="btn-group" style="margin:0 0 10px 0;">';
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
        $group = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
         $query = "SELECT *  FROM permissions WHERE name ='".$group["name"]."' AND type = 0 AND (";
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
            $group_data[$value["permission"]] = $value["value"];
        }
        $query = "SELECT parent FROM permissions_inheritance WHERE type = 1 AND child = '".$group["name"]."' LIMIT 1;";
        $group_data["name"] = $group["name"];   
        $group_data["parent"] = $this->db->query_fetch($query);
             if ($group_data["group"]["parent"] != "") $group_data["group"] = $group_data["group"]["parent"];
        else $group_data["group"] = FALSE;

        return $group_data;
    }
    public function saveEntity($id, $fields=FALSE, $table = FALSE){
        $query = "SELECT id, name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $group = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        foreach ($fields as $field){
            if ($field[2] != "name" && $field[2] != "parent") {
                $this->db->query("SELECT id FROM permissions WHERE name = '".$group["name"]."' AND permission = '".$field[2]."' AND type = '0' LIMIT 1;");
                if($this->db->affected() < 1)  $query = "INSERT INTO `permissions` (`id`, `name`, `type`, `permission`, `world`, `value`) VALUES (NULL, '".$group["name"]."', '1', '".$field[2]."', '', '".mysql_escape_string($_POST["save"][$field[2]])."');";
                else                        $query = "UPDATE permissions SET value = '".mysql_escape_string($_POST["save"][$field[2]])."' WHERE name = '".$group["name"]."' AND permission = '".$field[2]."' AND type = '0' LIMIT 1;";
                $this->db->query($query);
            }
            elseif ($field[2] == "parent"){
                $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE child = '".$group["name"]."' AND type = '0' LIMIT 1;";
                $this->db->query($query);
            }
            elseif ($field[2] == "name"){
                $query = "UPDATE permissions_inheritance SET child = '".$_POST["save"][$field[2]]."' WHERE child = '".$group["name"]."';";
                $this->db->query($query);
                $query = "UPDATE permissions_inheritance SET parent = '".$_POST["save"][$field[2]]."' WHERE parent = '".$group["name"]."';";
                $this->db->query($query);
                $query = "UPDATE permissions_entity SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$group["name"]."' LIMIT 1;";
                $this->db->query($query);
                $query = "UPDATE permissions SET name = '".$_POST["save"][$field[2]]."' WHERE name = '".$group["name"]."';";
                $this->db->query($query);
                $group["name"] = $_POST["save"][$field[2]];
            }
        }
    }
    
    public function getEditField_Parent($id,$field,$entity){
        $query = "SELECT name ";
        $query .= " FROM permissions_entity";
        $query .=" WHERE id = '".$id."';";
        $group_name = $this->db->query_fetch_single($query,MYSQLI_ASSOC, FALSE);
        $query = "SELECT parent FROM permissions_inheritance WHERE type = 0 AND child = '".$group_name."' LIMIT 1;";
        $parent = $this->db->query_fetch($query,MYSQLI_ASSOC, FALSE);
        if ($parent["parent"] != "") $parent = $parent["parent"];
        else $parent = FALSE;
        
        $query = "SELECT * FROM permissions_entity WHERE type = 0";
        $groups = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = '<select name="save['.$field[2].']">
                    <option value="">None</option>';
        foreach ($groups as $group){
            $html .= '<option value="'.$group["name"].'" ';
                if ($parent == $group["name"]) $html .= 'selected="selected"';
            $html .= '>'.$group["name"].'</option>';
        }
        $html .= '</select>';
        return $html;
    }
    
      public function newEntity($fields=FALSE, $table = FALSE){
        $query = "INSERT INTO `permissions_inheritance` (`id`, `child`, `parent`, `type`) VALUES (NULL, 'newGroup', '', '0');
                ";
        $this->db->query($query);
        $query = "INSERT INTO `permissions_entity` (`id`, `name`, `type`, `prefix`, `suffix`, `default`) VALUES (NULL, 'newGroup', '0', '', '', '0');
                ";
        $this->db->query($query);
        return $this->db->lastindex();
    }
    public function getButton_Delete($row){ 
        if (!$this->permissions->hasPermission($this->permission.".del") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="Löschen" type="button" id="delete_button_'.$row["id"].'" class="del_button btn btn-danger" onclick="var r=confirm(\''.$this->lang["backend"]["delete_entry"].'\');if (r==true) ajax_action(\'delete_entity\',\''.$this->table.'\',\''.$row["name"].'\');">
                        <i class="icon-trash icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    }   

    public function getButton_Status($row){
        if (!$this->permissions->hasPermission($this->permission.".edit") && $_SESSION["_registry"]["section"] != "frontend") return false;
        if (!$row["status"]) $status = 1;
        else $status = 0;
        $button = '
                    <button title="Status" type="button" class="btn btn-info" onclick="ajax_action(\'change_group_status\',\''.$this->table.'\',\''.$row["name"].'\','.$status.')">
                        <i class="icon-off icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    }    
}
?>
