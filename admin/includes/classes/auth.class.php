<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author Abadon
 */
class auth {
    private $lang;
    public $name;
    public $pass;
    public $group;
    private $db;
    function __construct(){
        $this->db = $_SESSION["_registry"]["db"];
        $this->lang =  $_SESSION["_registry"]["lang"]["backend"];
    }
    
    /**
    * Login-methode, bei Erfolg Weiterleitung auf die Hauptseite ansonsten Login-Form
    *
    * @param  string  $user Username
    * @param  string  $pass Password
    */
    public function login($user = FALSE, $pass = FALSE){
        $status = $this->check_status($user);
        if ($status) {
                sleep (1);
		$this->print_login_form($status);  
        }
        
	else if ($this->check($user, $pass))
	{
                $group = $this->db->query_fetch_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '$user' AND TYPE = 1 LIMIT 1;");
                $pass2 = $pass2["value"];
		        $_SESSION["_registry"]["user"] = array();
                $_SESSION["_registry"]["user"]['name'] = $user;
                $_SESSION["_registry"]["user"]['pass'] = $pass;
                $_SESSION["_registry"]["user"]['group'] = $group;
                $permissions = new permissions();
                if(!$permissions->hasPermission("backend")){
				echo "test";
                    unset($_SESSION["_registry"]["user"]);
                    sleep (1);
                    $this->print_login_form($status);  
                }
                else
                    echo '<script type="text/javascript">window.location="'.$_SESSION["_registry"]["system_config"]["site"]["base_url"].'admin/"; </script>';
	}
	else
	{
                sleep (1);
		$this->print_login_form($this->lang["badpassword"]);
	}
        
    }

    public function login_return($user = FALSE, $pass = FALSE){
    if ($this->check($user, $pass))
    {
                $group = $this->db->query_fetch_single("SELECT parent FROM permissions_inheritance WHERE `child` LIKE '$user' AND TYPE = 1 LIMIT 1;");
                $_SESSION["_registry"]["user"] = array();
                $_SESSION["_registry"]["user"]['name'] = $user;
                $_SESSION["_registry"]["user"]['pass'] = $pass;
                $_SESSION["_registry"]["user"]['group'] = $group;
                $permissions = new permissions();
                return TRUE;
    }
    else
    {
                sleep (1);
                return FALSE;
    }
        
    }

    /**
    * Check_Status-methode, überprüft den Status des übergebenen Benutzers
    *
    * @param  string  $user Username
    * @return string  $status bei erfolg FALSE, ansonsten Fehlermeldung
    */    
    public function check_status($user){
        $status = $this->db->query_fetch_single("SELECT value FROM permissions WHERE permission = 'status' AND name = '".$user."';");
        switch ($status){
            case "0": $status = $this->lang["account_deactivated"]; break;
            case "2": $status = $this->lang["account_not_activated"]; break;
            default : $status = FALSE; break;
        }
        return $status;
    }
    
    /**
    * Crypt-Methode, Passwwort verschlüsseln
    *
    * @param  string  $pass Password
    * @return string  $pass crypted password
    */
    public function crypt($pass = FALSE){
        if(!$pass){
            return FALSE;
        }
        else{
            $pass = Bcrypt::hash($pass, 12);
            $pass = $pass["hash"];
            return $pass;
        }
    }

    /**
    * Überprüfung auf korrekten Login
    *
    * @param  string  $user Username
    * @param  string  $pass Password
    * @return bool    TRUE / FALSE
    */
    public function check($user = FALSE, $pass = FALSE){
        if(!$user || !$pass){
            return FALSE;
        }
        else{
            $pass2 = $this->db->query_fetch_single("SELECT value FROM permissions WHERE `name` LIKE '$user' AND `permission` LIKE 'password' LIMIT 1;");
	if (Bcrypt::check_hash($pass, $pass2))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
        }
    }
    
    /**
    * Gibt dias Loginformular aus
    *
    * @param  string  $message Nachricht die über der Form erscheint
    */
    public function print_login_form($message= ""){
        //echo $this->crypt("ub140299");
	    echo '
	        <div id="abstand_login"></div>
            <div id="login_box" class="well">
               <h1>wireframe </h1>
                <p id="login_message">'.$message.'</p>
                <form action="" id="login" method="post">
                <span class="labelinside"> <label for="user">'.$this->lang["username"].':</label> <input class="form_submit_next text_input" id="user" name="user"/> </span>
                <br />
                <span class="labelinside"> <label for="pass">'.$this->lang["password"].':</label> <input type="password" class="text_input" id="pass" name="pass"/> </span>
                <br />
                <input type="hidden" name="login" value="login">
                <button class="btn btn-success fileinput-button" type="submit" value="Login" name="login">
                    <i class="icon-check icon-white"></i>
                    <span>Login</span>
                </button>
                </form>
                <script type="text/javascript">form_submit("login")</script>
                <br />
                <div id="zeit"></div><br />
                <div id="support">Support: <a href="mailto:wireframe@knew-it.de">wireframe@knew-it.de</a></div>
            </div>';
    }    
}

?>
