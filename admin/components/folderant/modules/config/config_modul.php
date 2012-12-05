<?php
class config_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_folderant_config";
        $this->init();
    }
    public function list_buttons(){
        $html = '
                    <p class="headline">'.$this->lang["backend"]["module"]["available_entitys"].'</p>
                                            <div class="btn-group">';
                                $html .= '
                <button type="submit" class="btn btn-primary start" onClick="location.reload();">
                    <i class="icon-refresh icon-white"></i>
                    <span>Aktualisieren</span>
                </button></div><br/><br/>';
            return $html;
    }
}
?>
