<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_connector
 *
 * @author Abadon
 */
class db{
    private $db;
    private $config;
    function __construct($type="config"){
        if ($type == "config") $db_config = $_SESSION["_registry"]["db_config"];
        else if ($type == "backup") $db_config = $_SESSION["_registry"]["db_config"]["backup"];
        if (!isset($db_config["port"])) $db_config["port"] = 3306;
        $this->config = $db_config;
        @$this->db = new mysqli($db_config["host"],  $db_config["user"], $db_config["pass"], $db_config["database"], $db_config["port"]);
        if ($this->db->connect_errno){
            $this->send_DB_Error($db->connect_error,"Fehler in der Datenbank-Verbindung");
        }
        $this->query("SET NAMES utf8");
        $this->query("SET CHARACTER SET utf8");
		$this->query('SET character_set_results=utf8');
    }
    
    function __destruct() {
       $this->db->close();
   }

    function query($query,$result = TRUE){
        $this->db->ping();
        $results = $this->db->query(str_replace('#__', $this->config["prefix"]."_", $query)) or $this->send_DB_Error($this->db->error,$query);
        return $result;  
    }

    function lastindex(){	
        return $this->db->insert_id;
    }

    function lastindex_query($query){	
        $this->query($query);
        return $this->lastindex();
    }

    function affected(){	
        return $this->db->affected_rows;
    }
    
    function affected_query($query){	
        $this->query($query);
        return $this->affected();
    }
    
    function is_field($field, $table){
        $fields = $this->select("SHOW COLUMNS FROM $table;");
        foreach ($fields as $field_name){
            if ($field_name["Field"] == $field)  return TRUE;
        }
        return FALSE;
    }
    
    function get_max($field, $table){	
        return $this->query_fetch_single("SELECT MAX(`".$field."`) FROM ".$table.";");
    }
    
    function get_min($field, $table){	
        return $this->query_fetch_single("SELECT MIN(`".$field."`) FROM ".$table.";");
    }

    function free_result($res){    
        return $res->mysqli_free_result;
    }
    
    function fetch($res,$type = MYSQLI_ASSOC){
        if($res){return $res->fetch_array($type);}
    }
    
    function fetch_single($res,$type = MYSQLI_ASSOC){
        $res = $this->fetch($res,$type);
        if ($res) return reset($res);
        else return false;
    }

    function query_fetch($query){	
        return($this->fetch($this->db->query($query)));
    }
    function query_fetch_single($query){	
        return($this->fetch_single($this->db->query($query)));
    }

    function query_lastindex($query){	
        return($this->lastindex($this->db->query($query)));
    }

    function send_DB_Error($EMSG,$query){	
	$EBODY='<html><head><title>ERROR</title></head><body><!-- ERRORMESSAGE --><br><!-- CODEWRITER --><br><!-- CODEWRITEREMAIL --></body></html>';
	$EBODY=str_replace("<!-- ERRORMESSAGE -->",$query."-".$EMSG,$EBODY);
	$EBODY=str_replace("<!-- CODEWRITER -->",$CODEWRITER,$EBODY);
	$EBODY=str_replace("<!-- CODEWRITEREMAIL -->",$CODEWRITEREMAIL,$EBODY);
	print ($EBODY);
	exit;
    }
    
    function select ($query,$type = MYSQLI_ASSOC, $flag=FALSE, $key = FALSE){
		if(empty($query)) { return false; }
                $results = $this->db->query($query) or $this->send_DB_Error($this->db->error,$query);
        
                if($flag)
                print_r($results);
                if( (!$results) or (empty($results)) ) {
			return false;
		}
		$count = 0;
		$data = array();
		while ( $row = $this->fetch($results,$type))
		{
			if (!$key) $data[$count] = $row;
                        else $data[$row[$key]] = $row;
			$count++;
		}
		$this->free_result($results);
		return $data;
    }
    
    function select_single ($query,$type = MYSQLI_ASSOC, $flag=FALSE){
		if(empty($query)) { return false; }
                $results = $this->db->query($query) or $this->send_DB_Error($this->db->error,$query);
        
                if($flag)
                print_r($results);
                if( (!$results) or (empty($results)) ) {
			return false;
		}
		$count = 0;
		$data = array();
		while ( $row = $this->fetch($results,$type))
		{
			$data[$count] = reset($row);
			$count++;
		}
		$this->free_result($results);
		return $data;
    }
    
    function select_pair ($table,$key,$value,$order=FALSE,$flag=FALSE, $where=FALSE ){
                if($order) {
                    $orderstring = "ORDER BY `$order` ASC";
                    $order =  ", `$order`";
                }
                else $order = "";
                if($where) $wherestring = "WHERE $where";
                $results = $this->db->query("SELECT `$key`, `$value`$order FROM `$table` $wherestring $orderstring ;") or $this->send_DB_Error($this->db->error,$query);
        
                if($flag)
                print_r($results);
                if( (!$results) or (empty($results)) ) {
			return false;
		}
		$data = array();
		while ( $row = $this->fetch($results,MYSQLI_ASSOC))
		{
			$data[$row[$key]] = $row[$value];
		}
		$this->free_result($results);
		return $data;
    }
    
    function dump($to_dump = FALSE){
        require_once pathinfo(__FILE__,PATHINFO_DIRNAME)."/MySQLDump.class.php";
        $dumper = new MysqlDumper($this->config["host"],$this->config["user"],$this->config["pass"],$this->config["database"]);
        $dumper->setDroptables(true);
        return  $dumper->createDump($to_dump);
    }
}
?>