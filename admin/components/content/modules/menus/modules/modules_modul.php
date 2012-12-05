<?php
class modules_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_sections";
        $this->init();
        $this->sections = $this->get_sections();
    }

    public function getEditField_Select_Component($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">
        <option value="">Bitte wählen</option>';
            foreach ($this->sections as $key => $select){
            $html .= '<option value="'.$key.'"';
            if ($entity[$field[2]] == $key) $html .=' selected="selected"';
            $html .='>'.$select["name_de"].'</option>';
            }
        $html .= '</select>';  
        return $html;
    }

    public function getEditField_Select_Section($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'"><option value="">zuerst Modul wählen</option></select>
        <script type="text/javascript">
            var modules = {';
            foreach ($this->sections as $key => $select){   
                $html .= '\''.$key.'\' : \'<option value="">Bitte wählen</option>';
                foreach($select["sections"] as $vk => $view){
                    $html .= '<option value="'.$vk.'"';
                    if ($entity[$field[2]] == $vk) $html .=' selected="selected"';
                    $html .='>'.$view["name_de"].'</option>';
                }
                $html .= '\',';
            }
        $html .= '};
        if($("#component option:selected").val()) ($("#section").html(modules[$("#component option:selected").val()]));
        $("#component").change(function(){
            ($("#section").html(modules[$("#component option:selected").val()]));
        })
        </script>';
        return $html;
    }

    public function getEditField_Option($id,$field,$entity){
        $html = '
        <div id="option_'.$field[2].'"></div>
        <script type="text/javascript">
            var options_'.$field[2].' = {';
            foreach ($this->sections as $module){ 
                foreach ($module["sections"] as $key => $select){ 
                $field_2 = explode(",",$select["field"]);
                if(count($field_2) > 1){
                    if ($field[0] != "Input") {
                        $call = "getEditField"."_".$field_2[0];
                        $field_2[2] = $field[2];
                        $fieldData = ereg_replace("\n", " ", $this->$call($id,$field_2,$entity));
                    }
                    else $fieldData = '<input type="text" name="save['.$field_2[2].']" value="'.htmlspecialchars($entity[$field_2[2]],ENT_QUOTES,"UTF-8").'" />';
                }               
                else{
                        $fieldData = "Keine Parameter";
                }
                $html .= '\''.$key.'\' : \''.stripslashes(preg_replace("/\r|\n/s", "", str_replace("script", "scr' + 'ipt", $fieldData))).'\',';
            }
        }
        $html .= '};
        if($("#section option:selected").val()) ($("#option_'.$field[2].'").html(options_'.$field[2].'[$("#section option:selected").val()]));
        $("#section").change(function(){
            ($("#option_'.$field[2].'").html(options_'.$field[2].'[$("#section option:selected").val()]));
        })
        </script>
        ';
        return $html;
    }

    private function get_sections(){
        $active_components = $this->db->select_pair("components","name","active","order",FALSE, "active = 1" );
        foreach($active_components as $file => $active){
                if (is_dir($this->base_root."/admin/components/".$file) && isset($active_components[$file]) && is_file($this->base_root."/admin/components/".$file."/sections.ini")) {
                        $component = parse_ini_file($this->base_root."/admin/components/".$file."/component.ini",TRUE);
                        $components[$file] = $component;
                        $components[$file]["sections"] = parse_ini_file($this->base_root."/admin/components/".$file."/sections.ini",TRUE);
                }
        }
    return $components;
	}
}
?>