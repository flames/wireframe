<?php
include_once 'includes/classes/modul.class.php';
class sites_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_websites";
        $this->init();
    }
}
?>
