<?php
class menus_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->table            = "wf_menus";
        $this->init();
    }
}
?>
