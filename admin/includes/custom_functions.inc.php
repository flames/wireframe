<?php
	function mccolor($string){
		return preg_replace_callback('/(&[0-9a-f])([^&]+|$)/', 'mccolor_callback', $string);
	}

	function mccolor_callback($m) {
   	 $trans = array(
        	"&0"=>'#000000;',
        	"&1"=>'#0000BF;',
        	"&2"=>'#00BF00;',
        	"&3"=>'#00BFBF;',
        	"&4"=>'#BF0000;',
        	"&5"=>'#BF00BF;',
        	"&6"=>'#BFBF00;',
        	"&7"=>'#BFBFBF;',
        	"&8"=>'#404040;',
        	"&9"=>'#4040FF;',
        	"&a"=>'#40FF40;',
        	"&b"=>'#40FFFF;',
        	"&c"=>'#FF4040;',
        	"&d"=>'#FF40FF;',
        	"&e"=>'#3F3F10;',
        	"&f"=>'#FFFFFF;',
    	);
    	return '<span style="color:'.$trans[$m[1]].'">'.$m[2].'</span>';
	}

	$users = array();
	$goups = array();
	function user_details($username){
        global $DB;
            $user = $DB->query_fetch("SELECT * FROM permissions_entity WHERE name LIKE '$username' LIMIT 1");
            $user['nodes']= $DB->select_pair ('permissions','permission','value',FALSE,FALSE, "name = '$username' AND value != '' AND permission != 'password'" );
            $user['inheritance'] = $DB->query_fetch("SELECT * FROM permissions_inheritance WHERE child = '$username' LIMIT 1");
            $user['group'] = $DB->query_fetch("SELECT * FROM permissions_entity WHERE name = '".$user["inheritance"]["parent"]."' LIMIT 1");
            $user['group']['nodes'] = $DB->select_pair ('permissions','permission','value',FALSE,FALSE, "name = '".$user['group']['name']."' AND value != ''" );
            return $user;
	}
?>