<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of modul
 *
 * @author Abadon
 */
class modul extends core{
    protected       $db;
    protected       $time;
    protected       $lang;
    protected       $base_url;
    protected       $base_root;
    protected       $ftp;
    protected       $permission;
    protected       $permissions;
    protected       $config;
    public          $table;
    private         $name;
    public          $path;
    public          $extraButtons;
    
    
    function __construct(){
        $this->init();
    }
    
     public function list_buttons(){
        $html = '
                    <p class="headline">'.$this->lang["backend"]["module"]["available_entitys"].'</p>
                                            <div class="btn-group">';
                                if ($this->permissions->hasPermission($this->config["permission"].".edit") || $_SESSION["_registry"]["section"] == "frontend") $html .= '
                <button type="submit" class="btn btn-success fileinput-button" onclick="window.location = \'?edit=none\'">
                    <i class="icon-plus icon-white"></i>
                    <span>Neuer Eintrag</span>
                </button>';
                                $html .= '

                <button type="submit" class="btn btn-primary start" onClick="location.reload();">
                    <i class="icon-refresh icon-white"></i>
                    <span>Aktualisieren</span>
                </button></div>';
                                if ($this->permissions->hasPermission($this->config["permission"].".del")) $html .= '
                                    
                <button type="button" id="delete_mass" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Markierte Einträge löschen</span>
                </button>
                                <script type="text/javascript">
                                    $("#delete_mass").click(function() {
                                        var r=confirm("'.$this->lang["backend"]["delete_mass"].'");
                                        if (r==true) delete_mass(\''.$this->table.'\');
                                    });
                                </script>';
                $html .= '';
                         if (isset($this->extraButtons["Lang"])){
                            if (isset($_GET["lang"]) && $_GET["lang"] != "") $lang = $_GET["lang"]; else  $lang = 1;
                            if ($lang == 1){
                                $html .= '<a href="?lang=2" ><img style="margin-bottom:-10px;" src="'.$this->base_url.'admin/img/langswitcher_de.png" style="cursor:pointer;"></a>';
                            }
                            else if ($lang == 2){
                                $html .= '<a href="?lang=1" ><img style="margin-bottom:-10px;" src="'.$this->base_url.'admin/img/langswitcher_en.png" style="cursor:pointer;"></a>';
                            }
                         }
            return $html;
    }
    /**
    * wandelt datetime in das standartformat der gewählten sprache oder in definiertes
    *
    * @param  string  $field Feldarray array(typ,name,spalte[,timeformat])
    * @param  string  $row Datensatz
    * @return string  html code der Tabelle
    */
   public function getField_DateTime($field,$row){
        if (!isset($field[3])) $format = FALSE;
        else $format = $field[3];
        return $this->time->convertDateTime($row[$field[2]],$format);
    }
    /**
    * wandelt datum in das standartformat der gewählten sprache oder in definiertes
    *
    * @param  string  $field Feldarray array(typ,name,spalte[,timeformat])
    * @param  string  $row Datensatz
    * @return string  html code der Tabelle
    */
    public function getField_Date($field,$row){
        if (!isset($field[3])) $format = FALSE;
        else $format = $field[3];
        if ($row[$field[2]] != "0000-00-00") return $this->time->convertDate($row[$field[2]],$format);
    }   
    
    public function getField_DateRange($field,$row){
        if (!isset($field[4])) $format = FALSE;
        else $format = $field[4];
        if ($row[$field[2]] != "0000-00-00") $from = $this->time->convertDate($row[$field[2]],$format);
        if ($row[$field[3]] != "0000-00-00") $till = $this->time->convertDate($row[$field[3]],$format);
        return $from.' - '. $till;
    }  
    
    /**
    * wandelt zeit in das standartformat der gewählten sprache oder in definiertes
    *
    * @param  string  $field Feldarray array(typ,name,spalte[,timeformat])
    * @param  string  $row Datensatz
    * @return string  html code der Tabelle
    */
    public function getField_Time($field,$row){
        if (!isset($field[3])) $format = FALSE;
        else $format = $field[3];
        return $this->time->convertTime($row[$field[2]],$format);
    }
    /**
    * holt anhand des inhaltes den inhalt aus einer anderen Tabelle
    *
    * @param  string  $field Feldarray array(typ,name,spalte,zielspalte,tabelle,zieleigenschaft)
    * @param  string  $row Datensatz
    * @return string  html code der Tabelle
    */
    public function getField_Table($field,$row){
        if (!isset($field[3]) || !isset($field[4]) || !isset($field[5])) return '<p style="color:red;">FIELD-ERROR</p>';
        else {
            return $this->db->query_fetch_single("SELECT $field[3] FROM $field[4] WHERE $field[5] = '".$row[$field[2]]."' LIMIT 1;");
        }
    }
    
    /**
    * holt anhand des inhaltes den inhalt aus einer anderen Tabelle
    *
    * @param  string  $field Feldarray array(typ,name,spalte,zielspalte,tabelle,zieleigenschaft)
    * @param  string  $row Datensatz
    * @return string  html code der Tabelle
    */
    public function getField_TableFilter($field,$row){
        if (!isset($field[3]) || !isset($field[4]) || !isset($field[5])) return '<p style="color:red;">FIELD-ERROR</p>';
        else {
            return $this->db->query_fetch_single("SELECT $field[3] FROM $field[4] WHERE $field[5] = '".$row[$field[2]]."' AND $field[6] LIMIT 1;");
        }
    }
    
    /**
    * gibt eine rote oder grüne kugel aus
    *
    * @param  string  $field Feldarray array(typ,name,spalte,zielspalte,tabelle,zieleigenschaft)
    * @param  string  $row Datensatz
    * @return string  ausgabe der grafik
    */
    public function getField_Status($field,$row){
        $status = '<img src="'.$this->base_url.'admin/img/';
        switch ($row[$field[2]]){
            case 0: $status .= 'kugel_rot.gif" alt="'.$this->lang["backend"]["inactive"].'"'; break;
            case 1: $status .= 'kugel_gruen.gif" alt="'.$this->lang["backend"]["active"].'"'; break;
        }
        $status.= ' />';
        return $status;
    }
    /**
    * gibt eine rote grüne oder gelbe kugel aus
    *
    * @param  string  $field Feldarray array(typ,name,spalte,zielspalte,tabelle,zieleigenschaft)
    * @param  string  $row Datensatz
    * @return string  ausgabe der grafik
    */
    public function getField_Status3($field,$row){
        $status = '<img src="'.$this->base_url.'admin/img/';
        switch ($row[$field[2]]){
            case "": $status .= 'kugel_gelb.gif" alt="'.$this->lang["backend"]["wait"].'"'; break;
            case 2: $status .= 'kugel_gelb.gif" alt="'.$this->lang["backend"]["wait"].'"'; break;
            case 1: $status .= 'kugel_gruen.gif" alt="'.$this->lang["backend"]["active"].'"'; break;
            case 0: $status .= 'kugel_rot.gif" alt="'.$this->lang["backend"]["inactive"].'"'; break;
        }
        $status.= ' />';
        return $status;
    }
    
   public function getField_Lang($field,$row){
        switch ($row[$field[2]]){
            case 1: $status .= 'deutsch'; break;
            case 2: $status .= 'englisch'; break;
        }
        return $status;
    }
    
    public function getField_Url($field,$entity){
        if ($entity[$field[2]] != ""){
            $html = '<a href="'.$entity[$field[2]].'" target="_blank" >'.$entity[$field[2]].'</a>';
        }
        return $html;
    }
    
    public function getButton_Edit($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".read") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="Bearbeiten" type="button" class="btn btn-success" onclick="window.location = \'?edit='.$row["id"].'\'">
                        <i class="icon-edit icon-white"></i>
                        <span></span>
                    </button>';
        return $button;
    }    
    public function getButton_Order($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".read") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="nach oben" type="button" class="btn btn-primary" onclick="ajax_action(\'change_order\',\''.$this->table.'\','.$row["id"].','.($row["order"] - 1).')">
                        <i class="icon-arrow-up icon-white"></i>
                        <span></span>
                    </button>
                    <button title="nach unten" type="button" class="btn btn-primary" onclick="ajax_action(\'change_order\',\''.$this->table.'\','.$row["id"].','.($row["order"] + 1).')">
                        <i class="icon-arrow-down icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    } 
    public function getButton_OrderParent($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".read")) return false;
        $button = '

                    <button title="nach oben" type="button" class="btn btn-primary" onclick="ajax_action(\'change_order_parent\',\''.$this->table.'\','.$row["id"].',\''.($row["order"] - 1).','.$row["parent"].'\')">
                        <i class="icon-arrow-up icon-white"></i>
                        <span></span>
                    </button>
                    <button title="nach unten" type="button" class="btn btn-primary" onclick="ajax_action(\'change_order_parent\',\''.$this->table.'\','.$row["id"].',\''.($row["order"] + 1).','.$row["parent"].'\')">
                        <i class="icon-arrow-down icon-white"></i>
                        <span></span>
                    </button>
                    ';
        return $button;
    } 
    public function getButton_Copy($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".edit") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="Kopieren" type="button" class="btn btn-success" onclick="window.location = \'?edit='.$row["id"].'&e_copy=1\'">
                        <i class="icon-share icon-white"></i>
                        <span></span>
                    </button>';
        return $button;
    }         

    public function getButton_Delete($row){ 
        if (!$this->permissions->hasPermission($this->config["permission"].".del") && $_SESSION["_registry"]["section"] != "frontend") return false;
        $button = '
                    <button title="Löschen" type="button" id="delete_button_'.$row["id"].'" class="del_button btn btn-danger" onclick="var r=confirm(\''.$this->lang["backend"]["delete_entry"].'\'); if (r==true) ajax_action(\'delete\',\''.$this->table.'\',\''.$row["id"].'\');">
                        <i class="icon-trash icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    }  

    public function getButton_Status($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".edit") && $_SESSION["_registry"]["section"] != "frontend") return false;
        if (!$row["status"]) $status = 1;
        else $status = 0;
        $button = '
                    <button title="Status" type="button" class="btn btn-info" onclick="ajax_action(\'change_status\',\''.$this->table.'\','.$row["id"].','.$status.')">
                        <i class="icon-off icon-white"></i>
                        <span></span>
                    </button>
';
        return $button;
    }         
    public function getButton_Status3($row){
        if (!$this->permissions->hasPermission($this->config["permission"].".edit")) return false;
        $button = '
                        <button id="status3_'.$row["id"].'" title="Status" class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">
                            <i class="icon-off icon-white"></i>
                        </button>
                        <ul id="status3_container_'.$row["id"].'" class="dropdown-menu status_dropdown">
                                <li><img title="gesperrt" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_rot.gif" title="inactive" onclick="ajax_action(\'change_status\',\''.$this->table.'\','.$row["id"].',0)"/></li>
                                <li><img title="inaktiv" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_gruen.gif" title="active" onclick="ajax_action(\'change_status\',\''.$this->table.'\','.$row["id"].',1)"/></li>                          
                                <li><img title="aktiv" style="cursor:pointer;" src="'.$this->base_url.'admin/img/kugel_gelb.gif" title="wait" onclick="ajax_action(\'change_status\',\''.$this->table.'\','.$row["id"].',2)"/></li>
                        </ul>      
                        <script>
                            var position = $("#status3_'.$row["id"].'").position();
                            $("#status3_container_'.$row["id"].'").css("left",position.left + "px");
                        </script>

';
        return $button;
    }           

    public function edit_buttons($id,$css_id){
                    $html.='<div id="'.$css_id.'" class="btn-group" style="margin:0 0 10px 0;">';
        if ($this->permissions->hasPermission($this->config["permission"].".edit")) $html.='
                <button class="btn btn-success start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'save_btn\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-check icon-white"></i>
                    <span>Speichern</span>
                </button>
                <button class="btn btn-primary start" onclick="$(\'#btn'.$FORM_COUNT.'\').val(\'saveback\'); $(\'#edit_form'.$FORM_COUNT.'\').submit();">
                    <i class="icon-share icon-white"></i>
                    <span>Speichern und zurück</span>
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
    public function getEditField_DateRangeBig($id,$field,$entity){
        if (!isset($field[4])) $format = FALSE;
        else $format = $field[4];
        if ($entity[$field[2]] != "0000-00-00") $from = $this->time->convertDate($entity[$field[2]],$format);
    if ($entity[$field[3]] != "0000-00-00") $till = $this->time->convertDate($entity[$field[3]],$format);
        $html = '<input type="hidden" name="save['.$field[2].']" value="'.$entity[$field[2]].'" id="hidden_'.$field[2].'"/>
                 <input type="hidden" name="save['.$field[3].']" value="'.$entity[$field[3]].'" id="hidden_'.$field[3].'"/>
                 <input type="text" id="show'.$field[2].'" value="'.$from.'" /></td><td> - </td><td><input type="text" id="show'.$field[3].'" value="'.$till.'" />
                 <script>
                    $(function() {
                        var dates = $( "#show'.$field[2].', #show'.$field[3].'" ).datepicker({
                            defaultDate: "",
                            minDate: "",
                            changeMonth: true,
                            numberOfMonths: 1,
                            onSelect: function( selectedDate ) {
                            var option = this.id == "show'.$field[2].'" ? "minDate" : "maxDate",
                            instance = $( this ).data( "datepicker" ),
                            date = $.datepicker.parseDate(
                            instance.settings.dateFormat ||
                            $.datepicker._defaults.dateFormat,
                            selectedDate, instance.settings );
                            dates.not( this ).datepicker( "option", option, date );
                            $("#show'.$field[2].'").datepicker( "option", "altField", "#hidden_'.$field[2].'" );    
                            $("#show'.$field[2].'").datepicker( "option", "dateFormat", "'.$this->time->getFormat_calendar().'" );  
                            $("#show'.$field[2].'").datepicker( "option", "altFormat", "yy-mm-dd" );    
                            $("#show'.$field[3].'").datepicker( "option", "altField", "#hidden_'.$field[3].'" );
                            $("#show'.$field[3].'").datepicker( "option", "dateFormat", "'.$this->time->getFormat_calendar().'" );  
                            $("#show'.$field[3].'").datepicker( "option", "altFormat", "yy-mm-dd" );    
            }
        });
        
    });
                </script>';
        return $html;
    }

    public function getEditField_OrderedBoolSelectRelation($id,$field,$entity){
        $items = $this->db->select("SELECT ".$field[3].", ".$field[4].", `order` FROM ".$field[2]." WHERE ".$field[4]." = $id ".$field[4]." ORDER BY `order`;",MYSQLI_ASSOC,FALSE,$field[3]);
        $query = "SELECT id, ".$field[5]." FROM ".$field[6].";";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = "<table>";
        foreach ($selects as $select){
            $html .= '<tr><td><input type="checkbox" class="small_input" name="save['.$field[1].']['.$select["id"].']" value="'.$select["id"].'" ';
            if (isset($items[$select["id"]][$field[4]]))  $html .= 'checked="checked"'; 
            $html .= '>'.$select[$field[5]].'</input></td><td><input type="text" name="order['.$field[1].']['.$select["id"].']" value="'.$items[$select["id"]]["order"].'" style="width:20px;"/></td></tr>';
        }
        $html .= '</table>';
       return $html;
    }

    public function getEditField_Hidden($id,$field,$entity){
        $html = '<input type="hidden" name="save['.$field[1].']" value="'.$field[2].'" />';
        return $html;
    }   
    public function getEditField_Number($id,$field,$entity){
        if($entity[$field[2]]) $value = number_format($entity[$field[2]], 2, $this->lang["numbers"]["dec_point"], $this->lang["numbers"]["thousands_sep"]); else $value="";
        $html = '<input type="text" name="save['.$field[2].']" value="'.$value.'" />';
        return $html;
    } 

    public function getEditField_Html($id,$field,$entity){
        $html = '
<textarea id="text_'.$field[2].'" name="save['.$field[2].']">'.$entity[$field[2]].'</textarea>
<script type="text/javascript">
$(function() {
    delete editor_'.$field[2].'; 
    var editor_'.$field[2].' = $("#text_'.$field[2].'").wysiwyg({
        controls: {
            html: { visible : true },
        },
        initialContent : "",
        maxLength : '.$field[3].',
        maxHeight : '.ceil($field[3] / 100) .'
    });
    $.wysiwyg.fileManager.setAjaxHandler("/admin/js/jwysiwyg/plugins/fileManager/handlers/PHP/file-manager.php");
    $("#text_'.$field[2].'-wysiwyg-iframe").css("height","'.ceil($field[3] / 10) .'px").css("min-height","300px");
});  
</script>
';
        return $html;
    }    
    public function getEditField_HtmlMin($id,$field,$entity){
        $html = '
<textarea id="text_'.$field[2].'" name="save['.$field[2].']">'.$entity[$field[2]].'</textarea>
<script type="text/javascript">
$(function() {
    $("#text_'.$field[2].'").wysiwyg({\'iFrameClass\': \'wysiwygframe\'});
});  
</script>
';
        return $html;
    }

    public function getEditField_Text($id,$field,$entity){
        $html = '<textarea name="save['.$field[2].']" cols="50" rows="10">'.$entity[$field[2]].'</textarea>';
        return $html;
    }
    

    public function getEditField_Select($id,$field,$entity){
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">';
            foreach ($field[3] as $key => $select){
            $html .= '<option value="'.$key.'"';
            if ($entity[$field[2]] == $key) $html .=' selected="selected"';
            $html .='>'.$select.'</option>';
            }
        $html .= '</select>';
        return $html;   
    }
    
    public function getEditField_TableSelect($id,$field,$entity){
        $query = "SELECT id, ".$field[3]." FROM ".$field[4].";";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">
                    <option value="">None</option>';
        foreach ($selects as $select){
            $html .= '<option value="'.$select[$field[5]].'" ';
                if ($entity[$field[2]] == $select[$field[5]]) $html .= 'selected="selected"';
            $html .= '>'.$select[$field[3]].'</option>';
        }
        $html .= '</select>';
        if ($field[6])
            $html .=       '
                <script>
                        $( "#'.$field[2].'" ).change(function() {
                            $("#edit_form").submit();
                        });
                </script>';
        return $html;
    }

    public function getEditField_TableSelectFilter($id,$field,$entity){
        $query = "SELECT id, ".$field[3]." FROM ".$field[4]." WHERE $field[6];";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">
                    <option value="">None</option>';
        foreach ($selects as $select){
            $html .= '<option value="'.$select[$field[5]].'" ';
                if ($entity[$field[2]] == $select[$field[5]]) $html .= 'selected="selected"';
            $html .= '>'.$select[$field[3]].'</option>';
        }
        $html .= '</select>';
        if ($field[7])
            $html .=       '
                <script>
                        $( "#'.$field[2].'" ).change(function() {
                            $("#edit_form").submit();
                        });
                </script>';
        return $html;
    }

    public function getEditField_TableSelectWhere($id,$field,$entity){
        $query = "SELECT id, ".$field[3]." FROM ".$field[4]." WHERE ".$field[6][0]." ".$field[6][1]." '".$entity[$field[6][2]]."';";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = '<select name="save['.$field[2].']" id="'.$field[2].'">
                    <option value="">None</option>';
        foreach ($selects as $select){
            $html .= '<option value="'.$select[$field[5]].'" ';
                if ($entity[$field[2]] == $select[$field[5]]) $html .= 'selected="selected"';
            $html .= '>'.$select[$field[3]].'</option>';
        }
        $html .= '</select>';
        if ($field[7])
            $html .=       '
                <script>
                        $( "#'.$field[2].'" ).change(function() {
                            $("#edit_form").submit();
                        });
                </script>';
        return $html;
    }
    
    public function getEditField_Date($id,$field,$entity){
        if (!isset($field[3])) $format = FALSE;
        else $format = $field[3];
        if ($entity[$field[2]] != "0000-00-00") $date = $this->time->convertDate($entity[$field[2]],$format);
        $html = '<input type="hidden" name=save['.$field[2].']" value="'.$entity[$field[2]].'" id="hidden_'.$field[2].'"/>
                 <input type="text" id="show'.$field[2].'" value="'.$date.'" />
                 <script>
                    $(function() {
                        $( "#show'.$field[2].'" ).datepicker({
                            dateFormat: "'.$this->time->getFormat_calendar().'",
                            altField: "#hidden_'.$field[2].'",
                            altFormat: "yy-mm-dd"
                        });
                    });
                </script>';
        return $html;
    }
    
    public function getEditField_DateRange($id,$field,$entity){
        if (!isset($field[4])) $format = FALSE;
        else $format = $field[4];
        if ($entity[$field[2]] != "0000-00-00") $from = $this->time->convertDate($entity[$field[2]],$format);
        if ($entity[$field[3]] != "0000-00-00") $till = $this->time->convertDate($entity[$field[3]],$format);
        $html = '<input type="hidden" name="save['.$field[2].']" value="'.$entity[$field[2]].'" id="hidden_'.$field[2].'"/>
                 <input type="hidden" name="save['.$field[3].']" value="'.$entity[$field[3]].'" id="hidden_'.$field[3].'"/>
                 <input type="text" id="show'.$field[2].'" value="'.$from.'" /> - <input type="text" id="show'.$field[3].'" value="'.$till.'" />
                 <script>
                    $(function() {
                        var dates = $( "#show'.$field[2].', #show'.$field[3].'" ).datepicker({
                            defaultDate: "",
                            minDate: "",
                            changeMonth: true,
                            numberOfMonths: 1,
                            onSelect: function( selectedDate ) {
                            var option = this.id == "show'.$field[2].'" ? "minDate" : "maxDate",
                            instance = $( this ).data( "datepicker" ),
                            date = $.datepicker.parseDate(
                            instance.settings.dateFormat ||
                            $.datepicker._defaults.dateFormat,
                            selectedDate, instance.settings );
                            dates.not( this ).datepicker( "option", option, date );
                            $("#show'.$field[2].'").datepicker( "option", "altField", "#hidden_'.$field[2].'" );    
                            $("#show'.$field[2].'").datepicker( "option", "dateFormat", "'.$this->time->getFormat_calendar().'" );  
                            $("#show'.$field[2].'").datepicker( "option", "altFormat", "yy-mm-dd" );    
                            $("#show'.$field[3].'").datepicker( "option", "altField", "#hidden_'.$field[3].'" );
                            $("#show'.$field[3].'").datepicker( "option", "dateFormat", "'.$this->time->getFormat_calendar().'" );  
                            $("#show'.$field[3].'").datepicker( "option", "altFormat", "yy-mm-dd" );    
            }
        });
        
    });
                </script>';
        return $html;
    }

    public function getEditField_Upload($id,$field,$entity){  
        $FILENAME = $this->db->query_fetch_single("SELECT `file` FROM `wf_media` WHERE `table` LIKE '".$this->table."' AND `item_id` = $id AND `type` LIKE '".$field[2]."' LIMIT 1");
        $val_save = $field[2];
    if (!is_file($this->base_root.'/uploads/'.$FILENAME))
    {   $html = '<input type="file" name="save['.$val_save.']">';
        return $html;
    }

    $info[]=array("jpg","Image");
    $info[]=array("gif","Image");
    $info[]=array("png","Image");
    $info[]=array("pdf","PDF-File");
    $info[]=array("zip","Compressed File");
    $info[]=array("htm","HTML-File");
    $info[]=array("html","HTML-File");
    $info[]=array("doc","Word Document");
    $info[]=array("rar","Compressed File");
    $info[]=array("txt","Text-File");
    $info[]=array("mp3","MP3 Music-file");
    $info[]=array("exe","Executable file");
    $info[]=array("tar","Tar Compressed file");
    $info[]=array("swf","Flash file");
    
    $ext=substr($FILENAME,-3);
    $ext2=substr($FILENAME,-4);
    if ($ext2[0]!=".")  $ext=$ext2;
    $ext=strtolower($ext);
    for ($t=0;$t<count($info);$t++)
    {   if ($ext==$info[$t][0]) 
        {   $html .= $info[$t][0].' - '.$info[$t][1];
            if ($info[$t][1]=="Image")
            {   $image = new ImageEditor();
                                $image->loadImageFile($this->base_root.'/uploads/'.$FILENAME);
                $fsize=filesize($this->base_root.'/uploads/'.$FILENAME);
                $units="Bytes";
                if ($fsize>1024)
                {   $units="KBytes";
                    $fsize=round(($fsize/1024)*100)/100;
                }
                if ($fsize>1024)
                {   $units="MBytes";
                    $fsize=round(($fsize/1024)*100)/100;
                }
                $html .= '&nbsp;&nbsp;Gr&ouml;&szlig;e: '.$image->width.'x'.$image->height.' ('.$fsize.' '.$units.')<br/>';
                                $html .= '       <a href="'.$this->base_url.'uploads/'.$FILENAME.'" rel="shadowbox">
                                                    <img src="'.$this->base_url.'uploads/thumbs/'.$FILENAME.'" />                  
                                                </a><br>
                                                <input type="submit" name="delete_file'.$FORM_COUNT.'['.$field[2].']" value="delete" />';
            } else
            {   $fsize=filesize($this->base_root.'/uploads/'.$FILENAME); 
                $units="Bytes";
                if ($fsize>1000)
                {   $units="KBytes";
                    $fsize=round(($fsize/1024)*100)/100;
                }
                if ($fsize>1000)
                {   $units="MBytes";
                    $fsize=round(($fsize/1024)*100)/100;
                }
                $html .= '&nbsp;&nbsp;Gr&ouml;&szlig;e: '.$fsize.' '.$units.'<br>';
                $html .= '<a href="'.$this->base_url.'uploads/'.$FILENAME.'">[download]</a><br>
                                          <input type="submit" name="delete_file'.$FORM_COUNT.'['.$field[2].']" value="delete" />';
            }
            return $html;
        } 
    }
    $html .= '*.'.$ext.' - unbekannter Dateityp';
    $fsize=filesize($FILENAME);
    $units="Bytes";
    if ($fsize>1000)
    {   $units="KBytes";
        $fsize=round(($fsize/1024)*100)/100;
    }
    if ($fsize>1000)
    {   $units="MBytes";
        $fsize=round(($fsize/1024)*100)/100;
    }
    $html .= '&nbsp;&nbsp;Gr&ouml;&szlig;e: '.$fsize.' '.$units.'<br>';
    $html .= '<a href="'.$this->base_url.'uploads/'.$FILENAME.'">[download]</a><br>
                  <input type="submit" name="delete_file'.$FORM_COUNT.'['.$field[2].']" value="delete" />';
    return $html;
    }

    public function getEditField_Uploads($id,$field,$entity){
        global $URL_ROOT,$DIR_ROOT;
        $html = '
        <form id="fileupload_'.$field[2].'" action="'.$URL_ROOT.'admin/uploads/?table='.$this->table.'&id='.$entity["id"].'&type='.$field[2].'" method="POST" enctype="multipart/form-data">
                <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                <input type="hidden" name="up_table" value="'.$this->table.'" />
                <input type="hidden" name="up_item_id" value="'.$entity["id"].'" />
                <input type="hidden" name="up_type" value="'.$field[2].'" />
                <input type="hidden" name="custom_options" value=\''.json_encode($field[3]).'\' />
        <div class="row fileupload-buttonbar" style="margin-left:0;">
            <div class="btn-group">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Datei hinzufügen</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Hochladen</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Abbrechen</span>
                </button>
                </div>
               <div class="fileupload-progress fade" style="float:left; width:310px;">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
                </div>
                <div class="ups_del_group">
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Löschen</span>
                </button>
                <input type="checkbox" class="toggle">
                </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
        </form>
        <!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>{%=locale.fileupload.cancel%}</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&\'gallery\'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>{%=locale.fileupload.destroy%}</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>
<script type="text/javascript">
$(function () {
    \'use strict\';
    // Initialize the jQuery File Upload widget:
    $(\'#fileupload_'.$field[2].'\').fileupload();

    // Enable iframe cross-domain access via redirect option:
    $(\'#fileupload_'.$field[2].'\').fileupload(
        \'option\',
        \'redirect\',
        window.location.href.replace(
            /\/[^\/]*$/,
            \'/cors/result.html?%s\'
        )
    );

    if (window.location.hostname === \'blueimp.github.com\') {
        // Demo settings:
        $(\'#fileupload_'.$field[2].'\').fileupload(\'option\', {
            url: \'//jquery-file-upload.appspot.com/\',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            process: [
                {
                    action: \'load\',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: \'resize\',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: \'save\'
                }
            ]
        });
        // Upload server status check for browsers with CORS support:
        if ($.support.cors) {
            $.ajax({
                url: \'//jquery-file-upload.appspot.com/\',
                type: \'HEAD\'
            }).fail(function () {
                $(\'<span class="alert alert-error"/>\')
                    .text(\'Upload server currently unavailable - \' +
                            new Date())
                    .appendTo(\'#fileupload_'.$field[2].'\');
            });
        }
    } else {
        // Load existing files:
        $(\'#fileupload_'.$field[2].'\').each(function () {
            var that = this;
            $.getJSON(this.action, function (result) {
                if (result && result.length) {
                    $(that).fileupload(\'option\', \'done\')
                        .call(that, null, {result: result});
                }
            });
        });
    }

});
</script>';
        return $html;
    }
        
    public function getEditField_Bool($id,$field,$entity){
        $html = '<input type="checkbox" class="small_input" name="save['.$field[2].']" value="1" ';
        if ($entity[$field[2]]) $html .= 'checked="checked" ';
        $html .= "/>";
        return $html;
    }
    
    public function getEditField_Colorpicker($id,$field,$entity){
        $html .= '<input type="hidden" maxlength="6" size="6" id="'.$field[2].'" value="'.$entity[$field[2]].'" name="save['.$field[2].']" />
                    <div id="colorSelector_'.$field[2].'"><div style="background-color: #'.$entity[$field[2]].'; width:25px; height:25px; border: 1px solid black;"></div></div>
                <script>
                    $(\'#colorSelector_'.$field[2].'\').ColorPicker({
                        color: \'#'.$entity[$field[2]].'\',
                        onShow: function (colpkr) {
                            $(colpkr).fadeIn(500);
                            return false;
                        },
                        onHide: function (colpkr) {
                            $(colpkr).fadeOut(500);
                            return false;
                        },
                        onChange: function (hsb, hex, rgb) {
                            $("#'.$field[2].'").val(hex);
                            $("#colorSelector_'.$field[2].' div").css("backgroundColor", "#" + hex);
                        }
                    });
                </script>';
        return $html;
    }
    
    public function getEditField_BoolSelect($id,$field,$entity){
        $query = "SELECT id, ".$field[3]." FROM ".$field[4].";";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $items = unserialize($entity[$field[2]]);
        foreach ($selects as $select){
            $html .= '<input type="checkbox" class="small_input" name="save['.$field[2].']['.$select["id"].']" value="1" ';
            if ($items[$select["id"]] == 1)  $html .= 'checked="checked"'; 
            $html .= '/>&nbsp;&nbsp;'.$select[$field[3]].'<br>';
        }
       return $html;
    }
    
    public function getEditField_BoolSelectRelation($id,$field,$entity){
        $items = $this->db->select_pair ($field[2],$field[3],$field[4],FALSE,FALSE, $field[4]."=$id" );
        $query = "SELECT id, ".$field[5]." FROM ".$field[6].";";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        foreach ($selects as $select){
            $html .= '<input type="checkbox" class="small_input" name="save['.$field[1].']['.$select["id"].']" value="'.$select["id"].'" ';
            if (isset($items[$select["id"]]))  $html .= 'checked="checked"'; 
            $html .= '/>&nbsp;&nbsp;'.$select[$field[5]].'<br>';
        }
       return $html;
    }
    
    public function getEditField_Info($id,$field,$entity){
        $html = '<input type="text" readonly="readonly" name="save['.$field[2].']" value="'.$entity[$field[2]].'" />';
        return $html;
    }

    public function get_box($name,$value,$id,$limit,$base_url){
        $html = '
<textarea id="'.$id.'" name="'.$name.'">'.$value.'</textarea>
<script type="text/javascript">
var hb_min_'.$id.' = $("#'.$id.'").css("height","'.($limit / 10).'px").htmlbox({
        toolbars:[
        [
        // Bold, Italic, Underline, Strikethrough, Sup, Sub
        "separator","bold","italic","underline","strike","sup","sub",
        // Left, Right, Center, Justify
        "separator","justify","left","center","right",      
                //Strip tags
        "separator","removeformat",
                // Hyperlink, Remove Hyperlink
        "separator","link","unlink" 
            ]
    ],
    idir:"'.$base_url.'admin/js/HtmlBox/images",    
    limit:'.$limit.',
    skin:"blue",
        about: false
});  
</script>
';
        return $html;
        }
    public function getEditField_InputsRelation($id,$field,$entity){
        $items = $this->db->select("SELECT ".$field[3].", ".$field[4].", value FROM ".$field[2]." WHERE ".$field[4]." = $id ;",MYSQLI_ASSOC,FALSE,$field[3]);
        $query = "SELECT id, ".$field[5]." FROM merkmale_cats_rel JOIN merkmale WHERE cat_id = ".$entity["parent"]." AND id = merkmal_id ORDER BY `order`;";
        $selects = $this->db->select($query,MYSQLI_ASSOC, FALSE);
        $html = "<table>";
        foreach ($selects as $select){
            $html .= '<tr><td>'.$select[$field[5]].'</td></tr><tr><td>'.$this->get_box('save['.$field[1].']['.$select["id"].']',$items[$select["id"]]['value'],$select["id"],$field[7],$this->base_url).'</td></tr>';
        }
        $html .= '</table>';
       return $html;
    }
}
?>