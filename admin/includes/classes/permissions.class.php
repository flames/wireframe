<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of permissions
 *
 * @author Abadon
 */
class permissions {
    private $user;
    private $permissions;
    private $bans;
    private $db;
    function __construct(){
        $this->db = $_SESSION["_registry"]["db"];
        if($_SESSION["_registry"]["user"]){
            $user = $_SESSION["_registry"]["user"];
            $this->user["name"]  = $user["name"];
            $this->user["group"]  = $user["group"];
        }
        else{
            $this->user["name"]  = "guest";
            $this->user["group"]  = "Gast";
        }
        if (!isset($user["permissions"])){
            $this->reloadPermissions();
        }
        else{
            $this->permissions = $user['permissions'];
            $this->bans = $user['bans'];
        }
    }
    public function reloadPermissions(){
        $this->bans = array();
        $this->permissions = array();
        $this->getPermissions($this->user["name"]);
        $_SESSION["_registry"]["user"]["permissions"] = $this->permissions;
        $_SESSION["_registry"]["user"]["bans"] = $this->bans;
    }  
    
    private function getUser_Permissions($entity){
        $type = $this->db->query_fetch_single("SELECT type FROM permissions_entity WHERE name = '$entity' LIMIT 1;");
        $permissions = $this->db->select_single("SELECT permission FROM permissions WHERE `name` LIKE '".$entity."' AND TYPE = ".$type." AND value= '';");
        $return_perms = array();
        $return_bans = array();
        foreach ($permissions as $permission){
            $permission = preg_replace("/\*/", ".*", $permission);
            $permission = preg_replace("/([^\.])([\.])([^\*])/", "$1\\.$3", $permission);
            if ($permission[0] == "-") array_push ($return_bans, substr($permission, 1));
            else array_push ($return_perms, $permission);
         }
        return array($return_perms,$return_bans);
    }   
    
    private function getParent_Permissions($entity){
        $type = $this->db->query_fetch_single("SELECT type FROM permissions_entity WHERE name = '$entity' LIMIT 1;");
        $permissions = $this->db->select_single("SELECT permission FROM permissions WHERE `name` LIKE '$entity' AND TYPE = $type AND value= '';");
        $parents = $this->db->select_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '$entity' AND `parent` != '' AND TYPE = $type;");  
        if (count($parents) > 0){
        $return_array = array(array(),array());
        foreach ($parents as $parent){
            $return_array_temp = $this->getClearPermissions($parent);
            $return_array[0] = array_merge($return_array[0], $return_array_temp[0]);
            $return_array[1] = array_merge($return_array[1], $return_array_temp[1]);
        }
        }
        return $return_array;
    }   
    
    private function getClearPermissions($entity){
        $return_array = array(array(),array());
        $type = $this->db->query_fetch_single("SELECT type FROM permissions_entity WHERE name = '$entity' LIMIT 1;");
        $permissions = $this->db->select_single("SELECT permission FROM permissions WHERE `name` LIKE '$entity' AND TYPE = $type AND value= '';");
        foreach ($permissions as $permission){
            $permission = preg_replace("/\*/", ".*", $permission);
            $permission = preg_replace("/([^\.])([\.])([^\*])/", "$1\\.$3", $permission);
            if ($permission[0] == "-") array_push ($return_array[1], substr($permission, 1));
            else array_push ($return_array[0], $permission);
         }
        $parents = $this->db->select_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '$entity' AND `parent` != ''  AND TYPE = $type;");  
        if (count($parents) > 0){
        foreach ($parents as $parent){
            $this->getClearPermissions($parent);
        }
        }
        return $return_array;
    }   
    
    private function getPermissions($entity){
        $type = $this->db->query_fetch_single("SELECT type FROM permissions_entity WHERE name = '$entity' LIMIT 1;");
        $permissions = $this->db->select_single("SELECT permission FROM permissions WHERE `name` LIKE '$entity' AND TYPE = $type AND value= '';");
        foreach ($permissions as $permission){
            $permission = preg_replace("/\*/", ".*", $permission);
            $permission = preg_replace("/([^\.])([\.])([^\*])/", "$1\\.$3", $permission);
            if ($permission[0] == "-") array_push ($this->bans, substr($permission, 1));
            else array_push ($this->permissions, $permission);
         }
        $parents = $this->db->select_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '$entity' AND TYPE = $type;");  
        foreach ($parents as $parent){
            $this->getPermissions($parent);
        }

    }   
    public function hasPermission($toCheck){
        if (is_array($this->bans)){
            foreach($this->bans as $ban){
                if (preg_match("/^$ban$/", $toCheck, $hit)) {
                    return FALSE;
                }
            }
        }
        if (is_array($this->permissions)){
            foreach($this->permissions as $permission){
                if (preg_match("/^$permission$/", $toCheck, $hit)) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    public function hasUserPermission($toCheck, $entity){
        $arrays = $this->getUser_Permissions($entity);
        if (is_array($arrays[1])){
            foreach($arrays[1] as $ban){
                if (preg_match("/^$ban$/", $toCheck, $hit)) {
                    return false;
                }
            }
        }
        if (is_array($arrays[0])){
            foreach($arrays[0] as $permission){
                if (preg_match("/^$permission$/", $toCheck, $hit)) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function hasParentPermission($toCheck, $entity){
        $arrays = $this->getParent_Permissions($entity);
        //print_r($arrays);
        if (is_array($arrays[1])){
            foreach($arrays[1] as $ban){
                if (preg_match("/^$ban$/", $toCheck, $hit)) {
                    return false;
                }
            }
        }
        if (is_array($arrays[0])){
            foreach($arrays[0] as $permission){
                if (preg_match("/^$permission$/", $toCheck, $hit)) {
                    return true;
                }
            }
        }
        return false;
    }
}

?>
