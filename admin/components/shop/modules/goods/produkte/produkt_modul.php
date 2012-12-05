<?php
include_once 'includes/classes/modul.class.php';
class produkt_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_prods";
        $this->init();
    }
    public function edit_buttons($id,$css_id){
                $html.='<div id="'.$css_id.'" class="btn-group" style="margin:0 0 10px 0;">';
        if ($this->permissions->hasPermission($this->permission.".edit")) $html.='
                <button class="btn btn-success start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'save_btn\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-check icon-white"></i>
                    <span>Speichern</span>
                </button>
                <button class="btn btn-primary start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'saveback\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-share icon-white"></i>
                    <span>Speichern und zurück</span>
                </button>
                <button class="btn btn-info start" onclick="open_iframe(\''.$this->base_url.'admin/components/shop/modules/goods/produkte/attributes.php?id='.$id.'\',600,600);">
                    <i class="icon-asterisk icon-white"></i>
                    <span>Attribute</span>
                </button>';
        $html.='
                <button class="btn btn-warning cancel" onclick="window.location = \''.$_SESSION["_registry"]["variables"]["backlink"].'\'">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>abbrechen</span>
                </button>
                </div>
                ';

            return $html;
    }

    public function getEditField_Attributes($id,$field,$entity){
        $items = $this->db->select("SELECT ".$field[3].", ".$field[4]." FROM ".$field[2]." WHERE ".$field[4]." = $id;",MYSQLI_ASSOC,FALSE,$field[3]);
        $query = "SELECT id, ".$field[5]." FROM ".$field[6]." WHERE (group_id = ".$entity["group"]." AND type = 0) OR (group_id = ".$id." AND type = 1);";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        foreach ($selects as $select){
            $html .= '<input type="checkbox" class="small_input" name="save['.$field[1].']['.$select["id"].']" value="'.$select["id"].'" ';
            if (isset($items[$select["id"]][$field[4]]))  $html .= 'checked="checked"'; 
            $html .= '> '.$select[$field[5]].'<br/>';
        }
        $html .= '
        <script type="text/javascript">
            function sb_close(){
                location.reload();
            }
        </script>';
       return $html;
    }

    public function getEditField_Options($id,$field,$entity){
        $types = array("Checkbox","Checkboxgruppe","Radiogruppe","Selectgruppe");
        $group_options = $this->db->select("SELECT * FROM `".$field[2]."` WHERE `group` IS NULL AND `entity_type` =0 AND `entity_id` =".$entity["group"]." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
        $item_options = $this->db->select("SELECT * FROM `".$field[2]."` WHERE `group` IS NULL AND `entity_type` =1 AND `entity_id` =".$entity["id"]." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
        if($group_options){
        $html .= '
            <fieldset style="width:825px" id="option_holder">
                <legend>Gruppenoptionen</legend>    ';
        foreach($group_options as $option_id => $item_option){
            $ref = $this->db->query_fetch("SELECT `operation`,`value` FROM ".$field[2]." WHERE `type` = 4 AND `group` =".$option_id." AND `entity_id` =".$entity["id"]." LIMIT 1;");
            if($ref) {foreach($ref as $key => $value) if($value) {$item_option[$key] = $value;}}
            $html .='
            <fieldset id="optionfield_'.$option_id.'">
                <legend class="btn';
            if($ref) $html .=' btn-success';
            else $html .=' btn-warning';
            $html .='" style="width:825px; margin-bottom:10px; cursor:default; height: 28px;">
                    <div style="float:left; width:auto;"><h3>'.$item_option["name"].'</h3></div>
                    <button type="button" option="'.$option_id.'" id="unuse_option_'.$option_id.'" class="btn btn-warning unuse_option" style="float:right;';
            if(!$ref) $html .=' display:none;';
            $html .='">
                        <i class="icon-ban-circle icon-white"></i>
                        <span>nicht verwenden</span>
                    </button>
                    <button type="button" option="'.$option_id.'" id="use_option_'.$option_id.'" class="btn btn-success use_option" style="float:right;';
            if($ref) $html .=' display:none;';
            $html .='">
                        <i class="icon-ok icon-white"></i>
                        <span>verwenden</span>
                    </button>
                </legend>
                <div class="inner_field" ';
            if(!$ref) $html .=' style = "display:none;';
            $html .='>';
            if($item_option["type"] == 0){
                        $html .= '
                            Name: <input readonly="readonly" type="text" value="'.$item_option["name"].'" />&nbsp;&nbsp;&nbsp;
                            Operation: 
                            <select name="save['.$field[1].'][groups]['.$option_id.'][operation]">
                                <option value="0"';
                                if ($item_option["operation"] == 0) $html .=' selected="selected"';
                        $html .='>normal</option>
                                <option value="1"';
                                if ($item_option["operation"] == 1) $html .=' selected="selected"';
                        $html .='>prozent</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            Wert: <input type="text" name="save['.$field[1].'][groups]['.$option_id.'][value]" value="'.$item_option["value"].'" />
                            <input type="hidden" name="save['.$field[1].'][groups]['.$option_id.'][type]" value="'.$item_option["type"].'" />
                            <input id="save_'.$option_id.'" type="hidden" name="save['.$field[1].'][groups]['.$option_id.'][save]" value="';
                            if($ref) $html .='1';
                            else $html .='0';
                            $html .='" />

                        ';
            }
            else{
                        $childs = $this->db->select("SELECT * FROM ".$field[2]." WHERE `type` IS NULL AND `group` =".$option_id." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
                        $html .= '
                            Name: <input readonly="readonly" type="text" value="'.$item_option["name"].'" />&nbsp;&nbsp;&nbsp;
                            Type: <input readonly="readonly" type="text" value="'.$types[$item_option["type"]].'" />
                            <input id="save_'.$option_id.'" type="hidden" name="save['.$field[1].'][groups]['.$option_id.'][save]" value="';
                            if($ref) $html .='1';
                            else $html .='0';
                            $html .='" />
                        ';
                        foreach($childs as $child_id => $child){
                            $sub_ref = $this->db->query_fetch("SELECT `operation`,`value` FROM ".$field[2]." WHERE `type` = 4 AND `group` =".$child_id." AND `entity_id` =".$entity["id"]." LIMIT 1;");
                            if($sub_ref) {foreach($sub_ref as $key => $value) if($value) {$child[$key] = $value;}}
                        $html .= '
                            <div style="width:825px; padding-left:25px;" id="sub_optionfield_'.$child_id.'">
                            <br/>
                            <div style="width:800px; margin-bottom:10px; cursor:default;" class="btn btn-small sub_header';
                            if($sub_ref || !$ref) $html .=' btn-success';
                            else $html .=' btn-warning';
                            $html .='">
                                <div style="float:left; width:auto;"><h4>'.$child["name"].'</h4></div>
                                <button type="button" option="'.$child_id.'" id="unuse_sub_option_'.$child_id.'" class="btn btn-warning btn-small unuse_sub_option" style="float:right; padding:1px 9px;';
                            if(!$sub_ref && $ref) $html .=' display:none;';
                            $html .='">
                                    <i class="icon-ban-circle icon-white"></i>
                                    <span>nicht verwenden</span>
                                </button>
                                <button type="button" option="'.$child_id.'" id="use_sub_option_'.$child_id.'" class="btn btn-success btn-small use_sub_option" style="float:right; padding:1px 9px;';
                            if($sub_ref || !$ref) $html .=' display:none;';
                            $html .='">
                                    <i class="icon-ok icon-white"></i>
                                    <span>verwenden</span>
                                </button>
                            </div>
                            <div class="inner_field"';
                            if(!$sub_ref && $ref) $html .=' style="display:none;"';
                            $html .='>
                            Name: <input readonly="readonly" type="text" value="'.$child["name"].'"/>&nbsp;&nbsp;&nbsp;
                            Operation: 
                            <select name="save['.$field[1].'][groups]['.$option_id.'][childs]['.$child_id.'][operation]">
                                <option value="0"';
                                if ($child["operation"] == 0) $html .=' selected="selected"';
                        $html .='>normal</option>
                                <option value="1"';
                                if ($child["operation"] == 1) $html .=' selected="selected"';
                        $html .='>prozent</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            Wert: <input type="text" name="save['.$field[1].'][groups]['.$option_id.'][childs]['.$child_id.'][value]" value="'.$child["value"].'" />
                            <input id="sub_save_'.$child_id.'" type="hidden" name="save['.$field[1].'][groups]['.$option_id.'][childs]['.$child_id.'][save]" value="';
                            if($sub_ref || !$ref) $html .='1';
                            else $html .='0';
                            $html .='" />
                            </div>
                            </div>
                        ';
                        }
            }
            $html .='
            </div>
            </fieldset><br/><br/>';
        }
    }
        $html .= '
            <fieldset style="width:825px" id="option_holder">
                <legend>Produktoptionen<a style="float:right;" class="btn btn-success" data-toggle="modal" href="#new_option" ><i class="icon-plus icon-white"></i>Neue Option</a></legend>    ';
        foreach($item_options as $option_id => $item_option){
            $html .='
            <fieldset id="optionfield_'.$option_id.'">
                <legend class="btn" style="width:825px; margin-bottom:10px; cursor:default; height: 28px;">
                    <div style="float:left; width:auto;"><h3>'.$item_option["name"].'</h3></div>
                    <button type="button" option="'.$option_id.'" id="delete_option_'.$option_id.'" class="btn btn-danger delete_option" style="float:right;">
                        <i class="icon-trash icon-white"></i>
                        <span>Entfernen</span>
                    </button>';
            if($item_option["type"] != 0)
            $html .='
                     <a style="float:right;margin-right:5px;" class="btn btn-success new_option" id="new_option_'.$option_id.'"  data-toggle="modal" href="#new_sub_option_'.$option_id.'" ><i class="icon-plus icon-white"></i>Neue Option</a>';
            $html .='
                    <button type="button" option="'.$option_id.'" id="restore_option_'.$option_id.'" class="btn btn-success restore_option" style="float:right; display:none;">
                        <i class="icon-refresh icon-white"></i>
                        <span>Wiederherstellen</span>
                    </button>
                </legend>
                <div class="inner_field">';
            if($item_option["type"] == 0){
                        $html .= '
                            Name: <input type="text" name="save['.$field[1].']['.$option_id.'][name]" value="'.$item_option["name"].'" />&nbsp;&nbsp;&nbsp;
                            Operation: 
                            <select name="save['.$field[1].']['.$option_id.'][operation]">
                                <option value="0"';
                                if ($item_option["operation"] == 0) $html .=' selected="selected"';
                        $html .='>normal</option>
                                <option value="1"';
                                if ($item_option["operation"] == 1) $html .=' selected="selected"';
                        $html .='>prozent</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            Wert: <input type="text" name="save['.$field[1].']['.$option_id.'][value]" value="'.$item_option["value"].'" />
                            <input type="hidden" name="save['.$field[1].']['.$option_id.'][type]" value="'.$item_option["type"].'" />
                            <input id="save_'.$option_id.'" type="hidden" name="save['.$field[1].']['.$option_id.'][save]" value="1" />

                        ';
            }
            else{
                        $childs = $this->db->select("SELECT * FROM ".$field[2]." WHERE `type` IS NULL AND `group` =".$option_id." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
                        $html .= '
                            Name: <input type="text" name="save['.$field[1].']['.$option_id.'][name]" value="'.$item_option["name"].'" />&nbsp;&nbsp;&nbsp;
                            Type: 
                            <select name="save['.$field[1].']['.$option_id.'][type]">
                                <option value="1"';
                                if ($item_option["type"] == 1) $html .=' selected="selected"';
                        $html .='>'.$types[1].'</option>
                                <option value="2"';
                                if ($item_option["type"] == 2) $html .=' selected="selected"';
                        $html .='>'.$types[2].'</option>
                                <option value="3"';
                                if ($item_option["type"] == 3) $html .=' selected="selected"';
                        $html .='>'.$types[3].'</option>
                            </select>
                            <input id="save_'.$option_id.'" type="hidden" name="save['.$field[1].']['.$option_id.'][save]" value="1" />
                        ';
                        foreach($childs as $child_id => $child){
                        $html .= '
                            <div style="width:825px; padding-left:25px;" id="sub_optionfield_'.$child_id.'">
                            <br/>
                            <div style="width:800px; margin-bottom:10px; cursor:default;" class="btn btn-small sub_header">
                                <div style="float:left; width:auto;"><h4>'.$child["name"].'</h4></div>
                                <button type="button" option="'.$child_id.'" id="delete_sub_option_'.$child_id.'" class="btn btn-danger btn-small delete_sub_option" style="float:right; padding:1px 9px;">
                                    <i class="icon-trash icon-white"></i>
                                    <span>Entfernen</span>
                                </button>
                                <button type="button" option="'.$child_id.'" id="restore_sub_option_'.$child_id.'" class="btn btn-success btn-small restore_sub_option" style="float:right; display:none; padding:1px 9px;">
                                    <i class="icon-refresh icon-white"></i>
                                    <span>Wiederherstellen</span>
                                </button>
                            </div>
                            <div class="inner_field">
                            Name: <input type="text" name="save['.$field[1].']['.$option_id.'][childs]['.$child_id.'][name]" value="'.$child["name"].'" />&nbsp;&nbsp;&nbsp;
                            Operation: 
                            <select name="save['.$field[1].']['.$option_id.'][childs]['.$child_id.'][operation]">
                                <option value="0"';
                                if ($child["operation"] == 0) $html .=' selected="selected"';
                        $html .='>normal</option>
                                <option value="1"';
                                if ($child["operation"] == 1) $html .=' selected="selected"';
                        $html .='>prozent</option>
                            </select>&nbsp;&nbsp;&nbsp;
                            Wert: <input type="text" name="save['.$field[1].']['.$option_id.'][childs]['.$child_id.'][value]" value="'.$child["value"].'" />
                            <input id="sub_save_'.$child_id.'" type="hidden" name="save['.$field[1].']['.$option_id.'][childs]['.$child_id.'][save]" value="1" />
                            </div>
                            </div>
                        ';
                        }
            $html .='
                    <div class="modal hide" id="new_sub_option_'.$option_id.'">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h3>Neue Gruppen Option</h3>
                        </div>
                        <div class="modal-body">
                            Name: <input type="text" id="sub_new_option_name_'.$option_id.'" /><br/>
                            Operation: 
                            <select  id="sub_new_option_operation_'.$option_id.'">
                                <option value="0">normal</option>
                                <option value="1">prozent</option>
                            </select><br/>
                            Wert: <input type="text"   id="sub_new_option_value_'.$option_id.'"/>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn" data-dismiss="modal">Schließen</a>
                            <a onclick="$(\'#optionfield_'.$option_id.'\').children(\'.inner_field\').append(get_sub_option('.$option_id.',$(\'#sub_new_option_name_'.$option_id.'\').val(), $(\'#sub_new_option_operation_'.$option_id.'\').val(), $(\'#sub_new_option_value_'.$option_id.'\').val()));$(\'#sub_new_option_name_'.$option_id.'\').val(\'\');$(\'#sub_new_option_operation_'.$option_id.'\').val(0);$(\'#sub_new_option_value_'.$option_id.'\').val(\'\');init_buttons();" class="btn btn-primary">Hinzufügen</a>
                        </div>
                    </div>';
            }
            $html .='
            </div>
            </fieldset>';
        }
        $html .= '
        </fieldset>
        <div class="modal hide" id="new_option">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Neue Option</h3>
            </div>
            <div class="modal-body">
                            Name: <input type="text" id="new_option_name"/>&nbsp;&nbsp;&nbsp;
                            Type: 
                            <select  id="new_option_type">
                                <option value="0">'.$types[0].'</option>
                                <option value="1">'.$types[1].'</option>
                                <option value="2">'.$types[2].'</option>
                                <option value="3">'.$types[3].'</option>
                            </select>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Schließen</a>
                <a onclick="$(\'#option_holder\').append(get_option($(\'#new_option_name\').val(), $(\'#new_option_type\').val(), \'\', \'\'));$(\'#new_option_name\').val(\'\');$(\'#new_option_type\').val(\'0\');init_buttons();" class="btn btn-primary">Hinzufügen</a>
            </div>
        </div>
        <script>
            var last_option_index = 0;
            function get_option(name, type, operation, value){
                last_option_index = last_option_index + 1;
                var html = 
\'\\n<fieldset id="optionfield_new_\' + last_option_index + \'">\'+
\'\\n  <legend class="btn" style="width:825px; margin-bottom:10px; cursor:default; height: 28px;">\'+
\'\\n      <div style="float:left; width:auto;"><h3>\' + name + \'</h3></div>\'+
\'\\n      <button type="button" option="new_\' + last_option_index + \'" id="delete_option_new_\' + last_option_index + \'" class="btn btn-danger delete_option" style="float:right;">\'+
\'\\n          <i class="icon-trash icon-white"></i>\'+
\'\\n          <span>Entfernen</span>\'+
\'\\n      </button>\';
                if(type != 0)
                html += 
\'\\n                     <a style="float:right;margin-right:5px;" class="btn btn-success" data-toggle="modal" href="#new_sub_option_new_\' + last_option_index + \'" ><i class="icon-plus icon-white"></i>Neue Option</a>\';      
                html += 
\'\\n      <button type="button" option="new_\' + last_option_index + \'" id="restore_option_new_\' + last_option_index + \'" class="btn btn-success restore_option" style="float:right; display:none;">\'+
\'\\n          <i class="icon-refresh icon-white"></i>\'+
\'\\n          <span>Wiederherstellen</span>\'+
\'\\n      </button>\'+
\'\\n  </legend>\'+
\'\\n<div class="inner_field">\';
                if(type == 0){
                        html +=
\'\\n                            Name: <input type="text" name="save['.$field[1].'][new_\' + last_option_index + \'][name]" value="\' + name + \'" />&nbsp;&nbsp;&nbsp;\'+
\'\\n                            Operation: \'+
 \'\\n                           <select name="save['.$field[1].'][new_\' + last_option_index + \'][operation]">\'+
\'\\n                               <option value="0"\';
                                if (operation == 0) 
                        html +=\' selected="selected"\';
                        html +=\'>normal</option>\'+
\'\\n                                  <option value="1"\';
                                if (operation == 1)
                        html +=\' selected="selected"\';
                        html +=\'>prozent</option>\'+
\'\\n                             </select>&nbsp;&nbsp;&nbsp;\'+
\'\\n                             Wert: <input type="text" name="save['.$field[1].'][new_\' + last_option_index + \'][value]" value="\' + value + \'" />\'+
\'\\n                             <input type="hidden" name="save['.$field[1].'][new_\' + last_option_index + \'][type]" value="\' + type + \'" />\'+
\'\\n                             <input id="save_new_\' + last_option_index + \'" type="hidden" name="save['.$field[1].'][new_\' + last_option_index + \'][save]" value="1" />\';
                }
                else{
                        html +=
\'\\n                       Name: <input type="text" name="save['.$field[1].'][new_\' + last_option_index + \'][name]" value="\' + name + \'" />&nbsp;&nbsp;&nbsp;\'+
\'\\n                            Type: \'+
\'\\n                            <select name="save['.$field[1].'][new_\' + last_option_index + \'][type]">\'+
\'\\n                                <option value="1"\';
                                if (type == 1) html +=\' selected="selected"\';
                        html +=\'>'.$types[1].'</option>\'+
\'\\n                                           <option value="2"\';
                                if (type == 2) html +=\' selected="selected"\';
                        html +=\'>'.$types[2].'</option>\'+
\'\\n                                           <option value="3"\';
                                if (type == 3) html +=\' selected="selected"\';
                        html +=\'>'.$types[3].'</option>\'+
\'\\n                            </select>\'+
\'\\n                            <input id="save_new_\' + last_option_index + \'" type="hidden" name="save['.$field[1].'][new_\' + last_option_index + \'][save]" value="1" />\'+
\'\\n                    <div class="modal hide" id="new_sub_option_new_\' + last_option_index + \'">\'+
\'\\n                        <div class="modal-header">\'+
\'\\n                            <button type="button" class="close" data-dismiss="modal">×</button>\'+
\'\\n                            <h3>Neue Gruppen Option</h3>\'+
\'\\n                        </div>\'+
\'\\n                        <div class="modal-body">\'+
\'\\n                            Name: <input type="text" id="sub_new_option_name_new_\' + last_option_index + \'" /><br/>\'+
\'\\n                            Operation: \'+
\'\\n                            <select  id="sub_new_option_operation_new_\' + last_option_index + \'">\'+
\'\\n                                <option value="0">normal</option>\'+
\'\\n                                <option value="1">prozent</option>\'+
\'\\n                            </select><br/>\'+
\'\\n                            Wert: <input type="text"   id="sub_new_option_value_new_\' + last_option_index + \'"/>\'+
\'\\n                        </div>\'+
\'\\n                        <div class="modal-footer">\'+
\'\\n                            <a href="#" class="btn" data-dismiss="modal">Schließen</a>\'+
\'\\n                            <a onclick="$(\\\'#optionfield_new_\' + last_option_index + \'\\\').children(\\\'.inner_field\\\').append(get_sub_option(\\\'new_\' + last_option_index + \'\\\',$(\\\'#sub_new_option_name_new_\' + last_option_index + \'\\\').val(), $(\\\'#sub_new_option_operation_new_\' + last_option_index + \'\\\').val(), $(\\\'#sub_new_option_value_new_\' + last_option_index + \'\\\').val()));$(\\\'#sub_new_option_name_new_\' + last_option_index + \'\\\').val(\\\'\\\');$(\\\'#sub_new_option_operation_new_\' + last_option_index + \'\\\').val(0);$(\\\'#sub_new_option_value_new_\' + last_option_index + \'\\\').val(\\\'\\\');init_buttons();" class="btn btn-primary">Hinzufügen</a>\'+
\'\\n                        </div>\'+
\'\\n                    </div>\';
                }                
                html += 
\'\\n   </div>\'+
\'\\n</fieldset>\';
            return html;
            }
            function get_sub_option(parent, name, operation, value){
                last_option_index = last_option_index + 1;
                var html = 
\'\\                            <div style="width:825px; padding-left:25px;" id="sub_optionfield_new_\' + last_option_index + \'">\'+
\'\\                           <br/>\'+
\'\\                            <div style="width:800px; margin-bottom:10px; cursor:default;" class="btn btn-small sub_header">\'+
\'\\                                <div style="float:left; width:auto;"><h4>\' + name + \'</h4></div>\'+
\'\\                                <button type="button" option="new_\' + last_option_index + \'" id="delete_sub_option_[new_\' + last_option_index + \'" class="btn btn-danger btn-small delete_sub_option" style="float:right; padding:1px 9px;">\'+
\'\\                                    <i class="icon-trash icon-white"></i>\'+
\'\\                                    <span>Entfernen</span>\'+
\'\\                                </button>\'+
\'\\                                <button type="button" option="new_\' + last_option_index + \'" id="restore_sub_option_new_\' + last_option_index + \'" class="btn btn-success btn-small restore_sub_option" style="float:right; display:none; padding:1px 9px;">\'+
\'\\                                    <i class="icon-refresh icon-white"></i>\'+
\'\\                                    <span>Wiederherstellen</span>\'+
\'\\                                </button>\'+
\'\\                            </div>\'+
\'\\                            <div class="inner_field">\'+
\'\\                            Name: <input type="text" name="save['.$field[1].'][\' + parent + \'][childs][new_\' + last_option_index + \'][name]" value="\' + name + \'" />&nbsp;&nbsp;&nbsp;\'+
\'\\                            Operation: \'+
\'\\                            <select name="save['.$field[1].'][\' + parent + \'][childs][new_\' + last_option_index + \'][operation]">\'+
\'\\                                <option value="0"\';
                                if (operation == 0) html +=\' selected="selected"\';
                        html +=\'>normal</option>\'+
\'\\                                <option value="1"\';
                                if (operation == 1) html +=\' selected="selected"\';
                        html +=\'>prozent</option>\'+
\'\\                            </select>&nbsp;&nbsp;&nbsp;\'+
\'\\                            Wert: <input type="text" name="save['.$field[1].'][\' + parent + \'][childs][new_\' + last_option_index + \'][value]" value="\' + value + \'" />\'+
\'\\                            <input id="sub_save_new_\' + last_option_index + \'" type="hidden" name="save['.$field[1].'][\' + parent + \'][childs][new_\' + last_option_index + \'][save]" value="1" />\'+
\'\\                            </div>\'+
\'\\                            </div>\';
            return html;
            }
            function init_buttons(){
            $(".delete_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#new_option_" + option).hide();
                $("#restore_option_" + option).show();
                $("#optionfield_" + option).children(".inner_field").slideUp("slow");
                $("#optionfield_" + option).children("legend").addClass("btn-danger");
                $("#save_" + option).val("0");
            });
            $(".restore_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#new_option_" + option).show();
                $("#delete_option_" + option).show();
                $("#optionfield_" + option).children(".inner_field").slideDown("slow");
                $("#optionfield_" + option).children("legend").removeClass("btn-danger");
                $("#save_" + option).val("1");
            });
            $(".delete_sub_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#restore_sub_option_" + option).show();
                $("#sub_optionfield_" + option).children(".inner_field").slideUp("slow");
                $("#sub_optionfield_" + option).children(".sub_header").addClass("btn-danger");
                $("#sub_save_" + option).val("0");
            });
            $(".restore_sub_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#delete_sub_option_" + option).show();
                $("#sub_optionfield_" + option).children(".inner_field").slideDown("slow");
                $("#sub_optionfield_" + option).children(".sub_header").removeClass("btn-danger");
                $("#sub_save_" + option).val("1");
            });
            }
            init_buttons();
            $(".unuse_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#use_option_" + option).show();
                $("#optionfield_" + option).children(".inner_field").slideUp("slow");
                $("#optionfield_" + option).children("legend").addClass("btn-warning").removeClass("btn-success");
                $("#save_" + option).val("0");
            });
            $(".use_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#unuse_option_" + option).show();
                $("#optionfield_" + option).children(".inner_field").slideDown("slow");
                $("#optionfield_" + option).children("legend").addClass("btn-success").removeClass("btn-warning");
                $("#save_" + option).val("1");
            });
            $(".unuse_sub_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#use_sub_option_" + option).show();
                $("#sub_optionfield_" + option).children(".inner_field").slideUp("slow");
                $("#sub_optionfield_" + option).children(".sub_header").addClass("btn-warning").removeClass("btn-success");
                $("#sub_save_" + option).val("0");
            });
            $(".use_sub_option").click(function(){
                var option = $(this).attr("option");
                $(this).hide();
                $("#unuse_sub_option_" + option).show();
                $("#sub_optionfield_" + option).children(".inner_field").slideDown("slow");
                $("#sub_optionfield_" + option).children(".sub_header").addClass("btn-success").removeClass("btn-warning");
                $("#sub_save_" + option).val("1");
            });
        </script>
        ';
        return $html;
    }

     public function getEntityStructured($id,$fields,$table = FALSE){
        if (!$table) $table = $this->table;
        $query = "SELECT `id`";
        if ($fields){
            foreach($fields as $sub_fields){
                foreach ($sub_fields as $field){
                    if ($field[0] == "DateRange" || $field[0] == "DateRangeBig") $query .= ", `".$field[2]."`, `".$field[3]."`";
                    else if ($field[0] == "Hidden") $query .= ", `".$field[1]."`";
                    else if ($field[0] == "BoolSelectRelation" || $field[0] == "OrderedBoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Attributes" || $field[0] == "Options" || $field[0] == "Uploads" ) {}
                    else $query .= ", `".$field[2]."`";
                }
            }   
        
        } 
        $query .= " FROM `".$table."` WHERE id=".$id.";";
        return $this->db->query_fetch($query);
    }  

    public function copyEntity($id,$fields,$table = FALSE){
        if (!$table) $table = $this->table;
        $query = "SELECT * FROM `".$table."` WHERE id=".$id.";";
        $old_entity = $this->db->query_fetch($query);
        unset($old_entity["id"]);
        unset($old_entity["update"]);
        unset($old_entity["editor"]);
        if (!$table) $table = $this->table;
        $bool_selects = array();
        foreach ($fields as $field){
            if($field[0] == "Image"){
                $image_cl = new image();
                if($image_cl->is_set($old_entity[$field[2]])){
                $ext = $image_cl->get_ext($old_entity[$field[2]]);
                $time = microtime(true) * 100 ;
                $image_array_tmp = unserialize($old_entity[$field[2]]);
                $this->ftp->changeDir($_SESSION["_registry"]["ftp_config"]["self"]["root"]);
                $this->ftp->chmod("uploads",0777);
                copy($this->base_root."uploads/".$image_array_tmp[0] , $this->base_root."uploads/".$time.$ext);
                copy($this->base_root."uploads/".$image_array_tmp[1] , $this->base_root."uploads/".$time."_thumb".$ext);
                $image_array_tmp[0] = $time.$ext;
                $image_array_tmp[1] = $time."_thumb".$ext;
                $old_entity[$field[2]] = serialize($image_array_tmp);
                $this->ftp->chmod("uploads",0755);
                unset ($image_array_tmp);
                unset ($ext);
                unset ($time);
                }
            }
            else if($field[0] == "File"){
                if($old_entity[$field[2]]){
                $ext=substr($old_entity[$field[2]] ,-3);
                $ext2=substr($old_entity[$field[2]] ,-4);
                if ($ext2[0]!=".")  $ext=$ext2;
                $ext = ".".$ext;
                $ext=strtolower($ext);
                $time = microtime(true) * 100 ;
                $this->ftp->changeDir($_SESSION["_registry"]["ftp_config"]["self"]["root"]);
                $this->ftp->chmod("uploads",0777);
                copy($this->base_root."uploads/".$old_entity[$field[2]] , $this->base_root."uploads/".$time.$ext);
                $old_entity[$field[2]] = $time.$ext;
                $this->ftp->chmod("uploads",0755);
                unset ($ext);
                unset ($time);
                }
            }
            else if($field[0] == "BoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Attributes"){
                $bool_selects[] = $field;

            }
        }
        $query = "INSERT INTO ".$table." (
                    `id` ,
                    `update` ,
                    `editor`";
        
         foreach ($old_entity as $field => $value){
             $query .= " ,
                    `$field`";
             if ($field == "order") $old_entity[$field] = $this->db->get_max($field,$table) + 1;
         }
         $query .= " 
                )VALUES (
                    NULL , NULL , '".$_SESSION["_registry"]["user"]["name"]."'";
         foreach ($old_entity as $field => $value){
             $query .= " , '".$value."'";
         }
         $query .= "          );
                ";
        $new_id = $this->db->lastindex_query($query);
        foreach ($bool_selects as $select){
            $old_selects = $this->db->select("SELECT * FROM ".$select[2]." WHERE ".$select[4]." = ".$id." ;");
            foreach ($old_selects as $new_select){
                if(isset($new_select["value"])){
                    $this->db->query("INSERT INTO  ".$select[2]." (`".$select[3]."`, `".$select[4]."`,`value`) VALUES (".$new_select[$select[3]].",".$new_id.",'".$new_select["value"]."');");
                }
                else{
                    $this->db->query("INSERT INTO  ".$select[2]." (`".$select[3]."`, `".$select[4]."`) VALUES (".$new_select[$select[3]].",".$new_id.");");
                }
            }
        }
        return $new_id;
    }  

    public function save_field($id, $field, $table, $first){
                if ($field[0] == "Uploads"){}
                else if($field[0] == "Options"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE `entity_type` = 1 AND `entity_id` = $id ;");
                    if($_POST["save"][$field[1]]){
                        if($_POST["save"][$field[1]]["groups"]){
                            $groups = $_POST["save"][$field[1]]["groups"];
                            unset($_POST["save"][$field[1]]["groups"]);
                            foreach($groups as $item_id => $item_option){
                                if($item_option["save"] == 1){
                                    $this->db->query("INSERT INTO `".$field[2]."` (`id`, `type`, `group`, `entity_type`, `entity_id`, `name`, `operation`, `value`) VALUES (NULL, '4', '".$item_id."', '1', '".$id."', NULL, '".$item_option["operation"]."', '".$item_option["value"]."');");
                                    if($item_option["childs"])
                                    foreach($item_option["childs"] as $child_id => $child){
                                        if($child["save"] == 1)
                                        $this->db->query("INSERT INTO `".$field[2]."` (`id`, `type`, `group`, `entity_type`, `entity_id`, `name`, `operation`, `value`) VALUES (NULL, '4', ".$child_id.", '1', '".$id."', NULL, '".$child["operation"]."', '".$child["value"]."');");
                                    }  
                                }
                            }
                        }
                        foreach($_POST["save"][$field[1]] as $item_option){
                            if($item_option["save"] == 1){
                                if($item_option["type"] == 0)
                                    $this->db->query("INSERT INTO `".$field[2]."` (`id`, `type`, `group`, `entity_type`, `entity_id`, `name`, `operation`, `value`) VALUES (NULL, '".$item_option["type"]."', NULL, '1', '".$id."', '".$item_option["name"]."', '".$item_option["operation"]."', '".$item_option["value"]."');");
                                else{
                                    $this->db->query("INSERT INTO `".$field[2]."` (`id`, `type`, `group`, `entity_type`, `entity_id`, `name`, `operation`, `value`) VALUES (NULL, '".$item_option["type"]."', NULL, '1', '".$id."', '".$item_option["name"]."', NULL, NULL);");
                                    $group_id = $this->db->lastindex();
                                    if($item_option["childs"])
                                    foreach($item_option["childs"] as $child_id => $child){
                                        if($child["save"] == 1)
                                        $this->db->query("INSERT INTO `".$field[2]."` (`id`, `type`, `group`, `entity_type`, `entity_id`, `name`, `operation`, `value`) VALUES (NULL, NULL, ".$group_id.", '1', '".$id."', '".$child["name"]."', '".$child["operation"]."', '".$child["value"]."');");
                                    }
                                }  
                            }
                        }
                    }
                }
                else if($field[0] == "DateRange" || $field[0] == "DateRangeBig"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".$_POST["save"][$field[2]]."', `".$field[3]."` = '".$_POST["save"][$field[3]]."'";   
                    $querys ++; 
                }
                else if($field[0] == "Number"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".str_replace(array($this->lang["numbers"]["thousands_sep"],$this->lang["numbers"]["dec_point"]), array("","."), $_POST["save"][$field[2]])."'";   
                    $querys ++; 
                }
                else if($field[0] == "HTML" || $field[0] == "HTMLMin"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".$_POST["save"][$field[2]]."'";   
                    $querys ++; 
                }
                
                else if($field[0] == "BoolSelect"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".serialize($_POST["save"][$field[2]])."'";   
                    $querys ++; 
                }
                
                else if($field[0] == "BoolSelectRelation" || $field[0] == "Attributes"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE ".$field[4]." = $id ;");
                    if($_POST["save"][$field[1]]){
                    foreach($_POST["save"][$field[1]] as $item_id){
                        $sub_query = "INSERT INTO `".$field[2]."` (
                                    `".$field[3]."` ,
                                    `".$field[4]."` 
                                )
                                VALUES (
                                    '$item_id', '$id'
                                );";
                        $this->db->query($sub_query);
                    }
                }
                }
                else if($field[0] == "OrderedBoolSelectRelation"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE ".$field[4]." = $id ;");
                    foreach($_POST["save"][$field[1]] as $item_id){
                        $sub_query = "INSERT INTO `".$field[2]."` (
                                    `".$field[3]."` ,
                                    `".$field[4]."` ,
                                                                        `order`
                                )
                                VALUES (
                                    '$item_id', '$id', '".$_POST["order"][$field[1]][$item_id]."'
                                );";
                        $this->db->query($sub_query);
                    }
                }
                else if($field[0] == "InputsRelation"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE ".$field[4]." = $id ;");
                    foreach($_POST["save"][$field[1]] as $item_id => $value){
                        $sub_query = "INSERT INTO `".$field[2]."` (
                                    `".$field[3]."` ,
                                    `".$field[4]."` ,
                                                                        `value`
                                )
                                VALUES (
                                    '$item_id', '$id','".addslashes($value)."'
                                );";
                        $this->db->query($sub_query);
                    }
                }
                else {
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".$_POST["save"][$field[2]]."'";    
                    $querys ++;  
                }
                return $query;
    }
}
?>
