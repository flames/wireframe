<?php 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of folderant
 *
 * @author Abadon
 */

class folderant_api extends folderant{

    function __construct(){
        ob_clean();
        $this->db       =  $_SESSION["_registry"]["db"];
        $this->lang     =  $_SESSION["_registry"]["lang"];
        $this->config 	=  $this->db->select_pair("wf_folderant_config","key","value");
        $this->short_tags   =  $this->db->select_pair("wf_folderant_shorttags","short_tag","type_de");
        $this->process_query();
    }

	public function get_file($id){
		ob_clean();
		$file = $this->db->query_fetch_single("SELECT fullpath FROM wf_folderant_files WHERE id = $id LIMIT 1;");
    	$sendmime = $this->getType($file);
    	header ("HTTP/1.1 200 OK");
    	header("Content-type: $sendmime");
    	header("Content-Length: ".filesize($dlpath.$file));
    	header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
    	readfile($file); 
		ob_end_flush();
		die;
	}

   private function process_query(){
      $this->check_api_token();
      switch($_GET["action"]){
         case "search":
            echo json_encode($this->search($_GET["kmc_text"], $_GET["fulltext"], json_decode(urldecode($_GET["short_tags"]), true)));
            break;
         case "get_file":
            $this->get_file($_GET["id"]);
            break;
      }
   }

   private function check_api_token(){
      $api_token = $_GET["key"];
      $cleaned_url = preg_replace(array("/http:\/\//","/\/.*/"), "", $_SERVER['HTTP_REFERER']);
      $ip = gethostbyname($cleaned_url);
      $host = $this->get_host($ip);
      $api_user = $this->db->query_fetch("SELECT * FROM `wf_api_keys` WHERE url = '$cleaned_url' AND type = 'folderant' LIMIT 1;");
      if ($host == $api_user["hostname"] && $api_token == sha1($api_user["key"].$api_user["secret"])) return true;
      else echo "invalid call";
      die;
   }

   private function get_host($ip){
        $ptr= implode(".",array_reverse(explode(".",$ip))).".in-addr.arpa";
        $host = dns_get_record($ptr,DNS_PTR);
        if ($host == null) return $ip;
        else return $host[0]['target'];
      }
}
?>