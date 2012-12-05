<?php
class tools_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->kmc 				= new folderant();
        $this->init();
    }

    public function update_index(){
    	$this->kmc->update_db();
    }

    public function index_folders(){
    	$this->kmc->index_folders();
    }

    public function make_backup(){
    	$this->kmc->make_backup();
    }

    public function get_backup(){
    	$this->kmc->get_backup();
    }



}
?>
