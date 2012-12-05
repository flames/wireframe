<?php
/**
* MySQLdumper is a small PHP class that lets you generate a dump of a MySQL database 
* with just 2 lines of code. The dump can be used as a database backup. The dump
* is a valid MySQL-query in plain text. It doesn't depend on the 'mysqldump' command
* line utility, so you won't encounter a problem if this isn't available on the server.
*
* Example 1: Create a database drump.
* <code>
* $dumper = new MysqlDumper("localhost", "user", "password", "databasename");
* $dumpstring = $dumper->createDump();
* </code>
*
* Example 2: Create a database drump with a 'DROP TABLE IF EXISTS'-statement for each table.
* <code>
* $dumper = new MysqlDumper("localhost", "user", "password", "databasename");
* $dumper->setDroptables(true);
* $dumpstring = $dumper->createDump();
* </code>
* 
* Example 3: Create dumps of different databases.
* <code>
* $dumper = new MysqlDumper("localhost", "user", "password", "database1");
* $dumpstring1 = $dumper->createDump();
* $dumper->setDBname"("database2");
* $dumpstring2 = $dumper->createDump();
* </code>
*
* @package  MySQLdumper
* @version  1.0
* @author   Dennis Mozes <opensource@mosix.nl>
* @url		http://www.mosix.nl/mysqldumper
* @since    PHP 4.0
* @copyright Dennis Mozes
* @license GNU/LGPL License: http://www.gnu.org/copyleft/lgpl.html
**/
class Mysqldumper {
	var $_host;
	var $_dbuser;
	var $_dbpassword;
	var $_dbname;
	var $_isDroptables;
	
	function Mysqldumper($host = "localhost", $dbuser = "", $dbpassword = "", $dbname = "") {
		$this->setHost($host);
		$this->setDBuser($dbuser);
		$this->setDBpassword($dbpassword);
		$this->setDBname($dbname);
		// Don't drop tables by default.
		$this->setDroptables(false);
	}
	
	function setHost($host) {
		$this->_host = $host;
	}
	
	function getHost() {
		return $this->_host;
	}
	
	function setDBname($dbname) {
		$this->_dbname = $dbname;
	}
	
	function getDBname() {
		return $this->_dbname;
	}
	
	function getDBuser() {
		return $this->_dbuser;
	}
	
	function setDBpassword($dbpassword) {
		$this->_dbpassword = $dbpassword;
	}
	
	function getDBpassword() {
		return $this->_dbpassword;
	}
	
	function setDBuser($dbuser) {
		$this->_dbuser = $dbuser;
	}
	
	// If set to true, it will generate 'DROP TABLE IF EXISTS'-statements for each table.
	function setDroptables($state) {
		$this->_isDroptables = $state;
	}
	
	function isDroptables() {
		return $this->_isDroptables;
	}
	
	function createDump($to_dump = FALSE) {
		// Set line feed
		$lf = "\r\n";
		
		$resource = mysql_connect($this->getHost(), $this->getDBuser(), $this->getDBpassword());
		mysql_select_db($this->getDbname(), $resource);
		mysql_query("SET NAMES utf8");
        mysql_query("SET CHARACTER SET utf8");
		mysql_query('SET character_set_results=utf8');
		$result = mysql_query("SHOW TABLES");
		$tables = $this->result2Array(0, $result);
		foreach ($tables as $key => $tblval) {
			if(!$to_dump || in_array($tblval, $to_dump)){
				$result = mysql_query("SHOW CREATE TABLE `$tblval`");
				$createtable[$tblval] = $this->result2Array(1, $result);
			}
			else unset($tables[$key]);
		}

		// Generate dumptext for the tables.
		foreach ($tables as $tblval) {
			// Generate DROP TABLE statement when client wants it to.
			if($this->isDroptables()) {
				$output .= $lf."DROP TABLE IF EXISTS `$tblval`;" . $lf;
			}
			$output .= $createtable[$tblval][0].";" . $lf;
			$output .= $lf;
			$result = mysql_query("SELECT * FROM `$tblval`");
			$rows = $this->loadObjectList("", $result);
			foreach($rows as $row) {
				$insertdump = $lf;
				$insertdump .= "INSERT INTO `$tblval` VALUES (";
				$arr = $this->object2Array($row);
				foreach($arr as $key => $value) {
					$value = addslashes($value);
					$value = str_replace("\n", '\r\n', $value);
					$value = str_replace("\r", '', $value);
					$insertdump .= "'$value',";
				}
				$output .= rtrim($insertdump,',') . ");";
			}
		}
		mysql_close($resource);
		return $output;
	}
	
	// Private function object2Array.
	function object2Array($obj) {
		$array = null;
		if(is_object($obj)) {
			$array = array();
			foreach (get_object_vars($obj) as $key => $value) {
				if(is_object($value))
					$array[$key] = $this->object2Array($value);
				else
					$array[$key] = $value;
			}
		}
		return $array;
	}
	
	// Private function loadObjectList.
	function loadObjectList($key='', $resource) {
		$array = array();
		while ($row = mysql_fetch_object($resource)) {
			if ($key)
				$array[$row->$key] = $row;
			else
				$array[] = $row;
		}
		mysql_free_result($resource);
		return $array;
	}
	
	// Private function result2Array.
	function result2Array($numinarray = 0, $resource) {
		$array = array();
		while ($row = mysql_fetch_row($resource)) {
			$array[] = $row[$numinarray];
		}
		mysql_free_result($resource);
		return $array;
	}
}
?>