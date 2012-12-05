<?php
class sites_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_sites";
        $this->init();
        $this->views = $this->get_installed_views();
    }

    public function getEditField_Alias($id,$field,$entity){
        $html = '<input type="text" name="save['.$field[2].']" value=\''.htmlspecialchars ($entity[$field[2]],ENT_QUOTES,"UTF-8").'\' />';
        return $html;
    }

    public function getEditField_Select_Component($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">
        <option value="">Bitte wählen</option>';
            foreach ($this->views as $key => $select){
            $html .= '<option value="'.$key.'"';
            if ($entity[$field[2]] == $key) $html .=' selected="selected"';
            $html .='>'.$select["name_de"].'</option>';
            }
        $html .= '</select>';  
        return $html;
    }

    public function getEditField_Select_Modul($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'"><option value="">zuerst Modul wählen</option></select>
        <script type="text/javascript">
            var modules = {';
            foreach ($this->views as $key => $select){   
                $html .= '\''.$key.'\' : \'<option value="">Bitte wählen</option>';
                foreach($select["modules"] as $vk => $view){
                    $html .= '<option value="'.$vk.'"';
                    if ($entity[$field[2]] == $vk) $html .=' selected="selected"';
                    $html .='>'.$view["name_de"].'</option>';
                }
                $html .= '\',';
            }
        $html .= '};
        if($("#component option:selected").val()) ($("#modul").html(modules[$("#component option:selected").val()]));
        $("#component").change(function(){
            ($("#modul").html(modules[$("#component option:selected").val()]));
        })
        </script>';
        return $html;
    }

    public function getEditField_Select_View($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'"><option value="">zuerst Modul wählen</option></select>
        <script type="text/javascript">
        	var views = {';
        	foreach ($this->views as $key => $modul){	
                foreach($modul["modules"] as $key => $select){
        		$html .= '\''.$key.'\' : \'<option value="">Bitte wählen</option>';
        		foreach($select["views"] as $vk => $view){
        			$html .= '<option value="'.$vk.'"';
            		if ($entity[$field[2]] == $vk) $html .=' selected="selected"';
            		$html .='>'.$view.'</option>';
        		}
        		$html .= '\',';
            }
        	}
        $html .= '};
        if($("#modul option:selected").val()) ($("#view").html(views[$("#modul option:selected").val()]));
        $("#modul").change(function(){
        	($("#view").html(views[$("#modul option:selected").val()]));
        })
        </script>';
        return $html;
    }

    public function getEditField_Option($id,$field,$entity){
        $html = '
        <div id="option_'.$field[2].'"></div>
        <script type="text/javascript">
            var options_'.$field[2].' = {';
            foreach ($this->views as $module){ 
                foreach ($module["modules"] as $key => $select){ 
                        foreach($select["fields"] as $vfk => $view_field){
                            $field_2 = explode(",",str_replace('"', '', $view_field["field"]));
                            if(count($field_2) > 1){
                                if ($field_2[0] != "Input") {
                                    $call = "getEditField"."_".$field_2[0];
                                    $field_2[2] = $field[2];
                                    $fieldData = ereg_replace("\n", " ", $this->$call($id,$field_2,$entity));
                                }
                                else $fieldData = '<input type="text" name="save['.$field[2].']" value="'.htmlspecialchars($entity[$field[2]],ENT_QUOTES,"UTF-8").'" />';
                            }               
                            else{
                                $fieldData = "Keine Parameter";
                            }
                            $html .= '\''.$vfk.'\' : \''.stripslashes(preg_replace("/\r|\n/s", "", str_replace("script", "scr' + 'ipt", $fieldData))).'\',';
                    }
                }
            }
        $html .= '};
        if($("#view option:selected").val()) ($("#option_'.$field[2].'").html(options_'.$field[2].'[$("#view option:selected").val()]));
        $("#view").change(function(){
            ($("#option_'.$field[2].'").html(options_'.$field[2].'[$("#view option:selected").val()]));
        })
        </script>
        ';
        return $html;
    }

    private function get_installed_views(){
        $active_components = $this->db->select_pair("components","name","active","order",FALSE, "active = 1" );
        foreach($active_components as $file => $active){
                if (is_dir($this->base_root."/admin/components/".$file) && isset($active_components[$file])) {
                        $component = parse_ini_file($this->base_root."/admin/components/".$file."/component.ini",TRUE);
                        $components[$file] = $component;
                        $components[$file]["url"] = $thise->base_url."admin/".$file."/";
                        $components[$file]["path"] = "components/".$file."/";
                        if ($handle = opendir($this->base_root."/admin/components/$file/modules/")){
                            while (false !== ($module = readdir($handle))) {
                                if (preg_match("^\.{1,2}^",$module)) continue;
                                if (is_dir($this->base_root."/admin/components/$file/modules/".$module)) {
                                    $modul = parse_ini_file($this->base_root."/admin/components/$file/modules/".$module."/modul.ini",TRUE);
                                    if($modul["views"]){
                                        $components[$file]["modules"][$modul["name"]] = array("name_de" => $modul["display_name_de"], "views" => $modul["views"]);
                                        $components[$file]["modules"][$modul["name"]]["fields"] = parse_ini_file($this->base_root."/admin/components/$file/modules/".$module."/view_fields.ini",TRUE);
                                    }

                                }
                            }
                        }
                        if(!count($components[$file]["modules"])) unset($components[$file]);
                }
        }
    return $components;
	}
}
?>
