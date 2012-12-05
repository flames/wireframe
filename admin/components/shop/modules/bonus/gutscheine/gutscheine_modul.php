<?php
include_once 'includes/classes/modul.class.php';
class gutscheine_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_bonus";
        $this->init();
    }
        public function getEditField_code($id,$field,$entity){
        $html = '<input type="text" name="save['.$field[2].']" value="'.$entity[$field[2]].'" style="width:60%;" id="code_input"/> <input type="submit" id="gen_code" value="generieren" class="btn btn-action" style="margin-bottom:9px;"/>
        <script type="text/javascript">
        $("#gen_code").click(function(event){
        	event.preventDefault();
    		$.post(URL_ROOT + "admin/includes/ajax.php", { action: \'gen_code\'},function(data) {
        		$("#code_input").val(data);
     		});
        });
        </script>';
        return $html;
    }   
}
?>
