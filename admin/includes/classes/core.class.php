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
class core {
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
    
    protected function init(){
        $this->db       =  $_SESSION["_registry"]["db"];
        $this->lang     =  $_SESSION["_registry"]["lang"];
        $this->time     =  $_SESSION["_registry"]["time"];
        $this->base_url =  $_SESSION["_registry"]["system_config"]["site"]["base_url"];
        $this->base_root =  $_SESSION["_registry"]["root"]."/";
        $this->permissions = new permissions();
        if(is_file($this->path."localisation/de.ini")) $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        if(is_file($this->path."modul.ini")) $this->config   = parse_ini_file( $this->path."modul.ini");
        /*try
        {
            $this->ftp = FTP::getInstance();
            $this->ftp->connect($_SESSION["_registry"]["ftp_config"]["self"], false, true );
        }
        catch (FTPException $error) {echo $error->getMessage();}*/
    }
///////////////////////////////////////////////
/////////////Table View///////////////////////
/////////////////////////////////////////////
    /**
    * Generiert ein Listing aus einer Tabelle
    *
    * @param  array  $fields Feldarray
    * @param  array  $filters Filterarray
    * @param  array  $buttons Buttons der Einträge
    * @param  array  $extraButtons Hauptbuttons
    * @param  string  $table
    * @return string  html code der Tabelle
    */
    public function listTable($fields,$filters=FALSE,$buttons=FALSE,$extraButtons = FALSE, $table = FALSE){
        $_SESSION["_registry"]["variables"]["backlink"] = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $this->extraButtons = $extraButtons;
        $html .= $this->list_buttons();
        $req_table = $this->getTable($fields,$filters,$table);

           $html .= '         <table id="tableListing" cellspacing="0" cellpadding="0" class="table table-striped table-bordered">
            <thead>
                        <tr>';
        if($this->db->is_field('order', $this->table))
        $html .= '
                            <th style="width:15px;">#</th>';
        $html .= '
                            <th style="width:15px;"></th>';
        if ($fields){
            $first = TRUE;
            foreach($fields as $field){
                $html .= '
                            <th';
                if ($field[0] == "Status" || $field[0] == "Status3")$html .= ' style="width:25px; text-align:center;" '; 
                $html .= '>'.$field[1].'</th>';
            }
            $i = count($buttons) - 1;
            if (is_array($buttons)){
                $btn_row_width = 0;
                foreach ($buttons as $button){
                    if ($button != "OrderParent" && $button != "Order") $btn_row_width += 44;
                    else $btn_row_width += 86;
                }
            $html .= '<th style="width:'.$btn_row_width.'px;">Aktionen</th>';
                $html .= '</tr>    </thead><tbody>';

            $i = 1;
            foreach ($req_table as $row){
                $html .= '
                        <tr>';
        if($this->db->is_field('order', $this->table))
        $html .= '
                            <td style="text-align:center;">'.$row["order"].'</td>';
        $html .= '
                            <td><input type="checkbox" name="marked[]" value="'.$row["id"].'" /></td>';
                        foreach ($fields as $field){
                            if ($field[0] != "Input") {
                                $call = "getField"."_".$field[0];
                                $fieldData = $this->$call($field,$row);
                            }
                            else $fieldData = $row[$field[2]];
                            $html .= '
                                <td';
                            if ($field[0] == "Status" || $field[0] == "Status3") $html .=' style="text-align:center;"';
                            else $html .=' style="padding-left:10px;"';
                            $html.='>'.$fieldData.'</td>';
                        }
                        $buttons_html = '';
                        foreach ($buttons as $button){
                            $call = "getButton"."_".$button;
                            $fieldData = $this->$call($row);
                            if ($fieldData)
                                $buttons_html .= $fieldData;
                        }
                            $html .= '<td class="action_buttons"><div class="btn-group" style="margin:0 0 0 0;">'.$buttons_html.'</div></td>';
                        }
                $html .= '
                        </tr>';
                $i++;
            }
            
        }
        $html .= '
                    </tbody></table>
        <script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    $("#tableListing").dataTable({
        "oLanguage": {"sUrl": URL_ROOT + "admin/js/datatables/dataTables.german.txt"},
        "sPaginationType": "bootstrap",
        "aoColumns": [';
        if($this->db->is_field('order', $this->table))
        $html .= '
        null,';
        $html .= '
      { "bSortable": false },
      ';
      for($i = 1; $i <= count($fields); $i++){
            $html .= "null,
            ";
      }
      $html .= '
      { "bSortable": false }
        ]';
        if($this->db->is_field('order', $this->table))
        $html .= ',
        "aaSorting": [[ 0, "asc" ]]';
        $html .= '
    } );
} );
        </script>
';
        return $html;
    }
    
    /**
    * Liest die Tabelle aus
    *
    * @param  array  $fields Feldarray
    * @param  array  $filters Filterarray 
    * @param  string  $table
    * @return array  assoziatives array der Tabelle
    */
     public function getTable($fields=FALSE,$filters=FALSE, $table = FALSE){
        if (!$table) $table = $this->table;
        $query = "SELECT id";
        if ($fields){
            foreach($fields as $field){
                if ($field[0] == "DateRange") $query .= ', `'.$field[2].'`, `'.$field[3].'`';
                else if ($field[0] == "BoolSelectRelation") $query .= '';
                else $query .= ', `'.$field[2].'`';
            }
        }
        if($this->db->is_field('order', $this->table))$query .= ', `order`';
        $query .= " FROM ".$table;
        if ($filters || $search || $lang) $query .=" WHERE ";
        if ($filters){
            $query .= " (";
            $first = TRUE;
            foreach ($filters as $filter){
                if (!$first) $query .= " AND ";
                $query .= "`".$filter["0"]."`".$filter["1"]."'".$filter["2"]."'";
                $first = FALSE;
            }
            $query .= " )";
        } 
        if ($lang){
                if ($filters || $search) $query .=" AND ";
                $query .= " `language_id`=$lang";
        }
        $query .= ' ;';
        return $this->db->select($query);
    }
    ///////////////////////////////////////////////
    ///////////////Tree View//////////////////////
    ////////////////////////////////////////////
    /**
    * Generiert ein Tree aus einer Tabelle
    *
    * @param  array  $fields Feldarray
    * @param  array  $filters Filterarray
    * @param  array  $buttons Buttons der Einträge
    * @param  array  $extraButtons Hauptbuttons
    * @param  string  $table
    * @return string  html code der Tabelle
    */
    public function listTree($fields,$field,$filters=FALSE,$buttons=FALSE,$extraButtons = FALSE, $table = FALSE){
        $_SESSION["_registry"]["variables"]["backlink"] = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
        $this->extraButtons = $extraButtons;
        $html .= $this->list_buttons();
        $html .= '
        <link rel="stylesheet" type="text/css" media="screen,projection" href="/admin/js/treeview/jqtree.css" />  
        <script type="text/javascript" src="/admin/js/treeview/tree.jquery.js"></script> 
        <div id="tree1" data-url="'.$this->base_url.'/admin/includes/ajax.php?action=gen_tree&table='.$this->table.'&table_field=titel"><img src="'.$this->base_url.'/img/ajax-loader.gif"/></div>
        <div style="clear:both;"></div>
        <script type="text/javascript">
                    function toggle_node(node){
                        if(node.children.length !== 0){
                            if(!node.is_open){      
                                $(\'#tree1\').tree(\'openNode\', node);
                            }
                            else{
                                $(\'#tree1\').tree(\'closeNode\', node);
                            }
                        }
                        else{
                            location = "?edit="+node.id;
                        }
                    }
                    $(\'#tree1\').tree({
                            dragAndDrop: true
                    });
                    $(\'#tree1\').bind(
                        \'tree.click\',
                        function(event) {
                        var node = event.node;
                        toggle_node(node);
                        }
                    );
                    $(\'#tree1\').bind(
                        \'tree.move\',
                        function(event) {
                            $.post(URL_ROOT + "admin/includes/ajax.php", { action: \'move_tree\', moved : event.move_info.moved_node.id, target : event.move_info.target_node.id, position : event.move_info.position, table : \'wf_sites\'});
                            console.log(\'moved_node\', event.move_info.moved_node);
                            console.log(\'target_node\', event.move_info.target_node);
                            console.log(\'position\', event.move_info.position);
                            console.log(\'previous_parent\', event.move_info.previous_parent);
                        }
                );
        </script>';
        return $html;
    }  
    /**
    * Liest die Tabelle aus
    *
    * @param  array  $fields Feldarray
    * @param  array  $filters Filterarray 
    * @param  string  $table
    * @return array  assoziatives array der Tabelle
    */
     public function getTree($fields=FALSE,$field = "parent", $parent_id = 0,$table = FALSE){
        $tree = $this->db->select("SELECT * FROM ".$table." WHERE ".$field." = $parent_id;");
        return $tree;
    }
    ///////////////////////////////////////////////
    //Methoden zur Verwaltung normaler Entitäten// 
    /////////////////////////////////////////////
     public function showEntity($id,$fields,$table = FALSE){
         global $FORM_COUNT;
         if (!$table) $table = $this->table;
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "save_btn") $this->saveEntity($id, $fields, $table);
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "saveback") { $this->saveEntity($id, $fields, $table); echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';}
            else if (isset($_POST["reload"])) echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';
            else if (isset($_GET["e_copy"])) {
                $id = $this->copyEntity($id,$fields,$table);
            }
            else if ($id == "none") $id = $this->newEntity($fields, $table);     
            if ($fields){
            $entity = $this->getEntity($id, $fields, $table);
                    $html.= $this->edit_buttons($id,"edit_buttons_top");
                    $html .= '<form enctype="multipart/form-data" action="?edit='.$id.'" method="post" id="edit_form'.$FORM_COUNT.'" name="edit_form'.$FORM_COUNT.'" style="clear:both;">
                    <input type="hidden" name="btn'.$FORM_COUNT.'" id="btn'.$FORM_COUNT.'" value="">
                    <table id="showEntity" class="showEntity">';
            foreach($fields as $field){
                            if ($field[0] == "Uploads") {
                                $uploaders[] = $field;
                                continue;
                            }
                            elseif ($field[0] == "Hidden") {
                            $html .= '    <tr><td class="entity_title"></td><td class="entity_field">'.$fieldData.'</td></tr>';
                            continue;
                            }
                            elseif ($field[0] != "Input") {
                                $call = "getEditField"."_".$field[0];
                                $fieldData = $this->$call($id,$field,$entity);
                            }
                            else $fieldData = '<input type="text" name="save['.$field[2].']" value=\''.htmlspecialchars ($entity[$field[2]],ENT_QUOTES,"UTF-8").'\' />';
                $html .= '
                            <tr><td class="entity_title">'.$field[1].'</td><td class="entity_field">'.$fieldData.'</td></tr>';
            }           
        }
        $html .= '  </table>
                    </form>';
            if($uploaders)
            foreach($uploaders as $uploader){
                                $call = "getEditField"."_".$uploader[0];
                                $html .= $this->$call($id,$uploader,$entity);
            }    
                    $html.= $this->edit_buttons($id,"edit_buttons_bottom");
        $FORM_COUNT++;
        return $html;
    } 


     public function getEntity($id,$fields,$table = FALSE){
        if (!$table) $table = $this->table;
        $query = "SELECT `id`";
        if ($fields){
            foreach($fields as $field){
                if ($field[0] == "DateRange" || $field[0] == "DateRangeBig") $query .= ", `".$field[2]."`, `".$field[3]."`";
                else if ($field[0] == "Hidden") $query .= ", `".$field[1]."`";
                else if ($field[0] == "BoolSelectRelation" || $field[0] == "OrderedBoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Uploads" || $field[0] == "Upload"  ) {}
                else $query .= ", `".$field[2]."`";
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
            if($field[0] == "BoolSelectRelation" || $field[0] == "InputsRelation") $bool_selects[] = $field; 
            elseif($field[0] == "Uploads") $uploaders[] = $field;
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
        if($bool_selects)
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
        if($uploaders)
        foreach ($uploaders as $uploader){
            $medias = $this->db->select("SELECT * FROM wf_media WHERE `table` = '".$this->table."' AND item_id = '".$id."' AND type = '".$uploader[2]."';");
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $options = array(
                        'script_url' => $this->base_root.'admin/uploads/',
                        'upload_dir' => $this->base_root.'/uploads/',
                        'upload_url' => $this->base_root.'uploads/',
                        'param_name' => 'files',
                        'delete_type' => 'DELETE',
                        'max_file_size' => null,
                        'min_file_size' => 1,
                        'accept_file_types' => '/.+$/i',
                        'max_number_of_files' => null,
                        'max_width' => null,
                        'max_height' => null,
                        'min_width' => 1,
                        'min_height' => 1,
                        'discard_aborted_uploads' => true,
                        'orient_image' => false,
                        'image_versions' => array(
                            'thumbnail' => array(
                                'upload_dir' => $this->base_root.'/uploads/thumbs/',
                                'upload_url' => $this->base_root.'/uploads/thumbs/',
                                'max_height' => 80
                            )
                        )
                    );
            if ($uploader[3]) {
                $options = array_replace_recursive($options, $uploader[3]);
            }
            $upload = new Upload($options);
            foreach ($medias as $media){
                $filename = $DIR_ROOT.'/uploads/'.$media["file"];
                $file = array();
                if(is_file($filename)){
                    $file["filename"] = $media["file"];
                    $file["type"] = finfo_file($finfo, $filename);
                    $file["size"] = filesize ($filename);
                    $file = $upload->copy_file($filename, $file["filename"], $file["size"], $file["type"], $media["id"]);
                    $this->db->query("  INSERT INTO  `wf_media` (`id` ,`table` ,`item_id` ,`type` ,`file`)
                                         VALUES                  (NULL ,'".$table."','".$new_id."','".$uploader[2]."','".$file->name."');
                                    ");
                }
                else{
                    $this->db->query("DELETE FROM `wf_media` WHERE `id` = ".$media["id"]." LIMIT 1");
                }
            }
            finfo_close($finfo);
        }
        return $new_id;
    }  

     public function saveEntity($id, $fields=FALSE, $table = FALSE){    
        if (!$table) $table = $this->table;
        $querys = 0;
        $query = "UPDATE $table SET ";
        if ($fields){
            $first = TRUE;
            foreach($fields as $field){
                $query .= $this->save_field($id, $field, $table, $first);
                $first = FALSE;
            }
        }     
        $query .= ' WHERE id='.$id.' LIMIT 1;';
        return $this->db->query($query);
    } 

    ////////////////////////////////////////////////////////
    //Ende der Methoden zur Verwaltung normaler Entitäten// 
    //////////////////////////////////////////////////////

    /////////////////////////////////////////////////////
    //Methoden zur Verwaltung strukturierter Entitäten// 
    ///////////////////////////////////////////////////

     public function showEntityStructured($id,$fields,$table = FALSE){
         global $FORM_COUNT;
         if (!$table) $table = $this->table;
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "save_btn") $this->saveEntityStructured($id, $fields, $table);
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "saveback") { $this->saveEntityStructured($id, $fields, $table); echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';}
            else if (isset($_POST["reload"])) echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';
            else if (isset($_GET["e_copy"])) {
                $id = $this->copyEntityStructured($id,$fields,$table);
            }
            else if ($id == "none") $id = $this->newEntity($fields, $table);     
            if ($fields){
            $entity = $this->getEntityStructured($id, $fields, $table);
                    $html.= $this->edit_buttons($id,"edit_buttons_top");
            $html .= '
                <ul class="nav nav-tabs" id="myTab"  style="clear:both;">';
                $first = true;
                foreach($fields as $name => $sub_fields){
                    $html .= '
                    <li class="';
                    if ($first) $html .= ' active';
                    $first = false;
                    $html .='"><a href="#content_'.str_replace(" ", "", $name).'" data-toggle="tab">'.$name.'</a></li>';
                    foreach($sub_fields as $field_id => $field){
                            if ($field[0] == "Uploads") {
                                $uploaders[] = $field;
                                unset($fields[$name][$field_id]);
                                $html .= '
                    <li><a href="#content_'.str_replace(" ", "", $field[1]).'" data-toggle="tab">'.$field[1].'</a></li>';
                            }
                    }
                }
            $html .= '
                </ul>
                <div class="tab-content">
            <form enctype="multipart/form-data" action="?edit='.$id.'" method="post" id="edit_form'.$FORM_COUNT.'" name="edit_form'.$FORM_COUNT.'">
                <input type="hidden" name="btn'.$FORM_COUNT.'" id="btn'.$FORM_COUNT.'" value="">';
                $first = true;
                foreach($fields as $name => $sub_fields){
                    $html .= '
                    <div class="tab-pane';
                    if ($first) $html .= ' active"';
                    else  $html .= '" style="display:none;"';
                    $first = false;
                    $html .=' id="content_'.str_replace(" ", "", $name).'">';
                    $html .= '
                        <table class="showEntity" >';
                    foreach($sub_fields as $field){
                            if ($field[0] == "Hidden") {
                            $html .= '    <tr><td class="entity_title"></td><td class="entity_field">'.$fieldData.'</td></tr>';
                            continue;
                            }
                            elseif ($field[0] != "Input") {
                                $call = "getEditField"."_".$field[0];
                                $fieldData = $this->$call($id,$field,$entity);
                            }
                            else $fieldData = '<input type="text" name="save['.$field[2].']" value=\''.htmlspecialchars ($entity[$field[2]],ENT_QUOTES,"UTF-8").'\' />';
                            $html .= '
                            <tr><td class="entity_title">'.$field[1].'</td><td class="entity_field">'.$fieldData.'</td></tr>';
                    }           
                $html .= '  
                    </table>
                </div>';
            }

        }
        $html .= '
            </form>';
                if($uploaders){
            foreach($uploaders as $uploader){
                    $html .= '
                    <div class="tab-pane" style="display:none;" id="content_'.str_replace(" ", "", $uploader[1]).'">';
                                $call = "getEditField"."_".$uploader[0];
                                $html .= $this->$call($id,$uploader,$entity);
                    $html .= '
                    </div>';
            }  
        $html .= '
                </div>
            ';
        }
        $html .='
            <script>
            $(\'#myTab a\').click(function (e) {
                e.preventDefault();
                $(".tab-pane").fadeOut("slow");
                $($(this).attr(\'href\')).fadeIn("slow");
            })
            </script>';
                    $html.= $this->edit_buttons($id,"edit_buttons_bottom");
        $FORM_COUNT++;
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
                    else if ($field[0] == "BoolSelectRelation" || $field[0] == "OrderedBoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Uploads" || $field[0] == "Upload"  ) {}
                    else $query .= ", `".$field[2]."`";
                }
            }   
        
        } 
        $query .= " FROM `".$table."` WHERE id=".$id.";";
        return $this->db->query_fetch($query);
    }  

    
    public function copyEntityStructured($id,$fields,$table = FALSE){
        global $URL_ROOT,$DIR_ROOT;
        if (!$table) $table = $this->table;
        $query = "SELECT * FROM `".$table."` WHERE id=".$id.";";
        $old_entity = $this->db->query_fetch($query);
        unset($old_entity["id"]);
        unset($old_entity["update"]);
        unset($old_entity["editor"]);
        if (!$table) $table = $this->table;
        $bool_selects = array();

        foreach ($fields as $sub_fields){
            foreach ($sub_fields as $field){
                if($field[0] == "BoolSelectRelation" || $field[0] == "InputsRelation") $bool_selects[] = $field;
                elseif($field[0] == "Uploads") $uploaders[] = $field;
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
        if($bool_selects)
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
        if($uploaders)
        foreach ($uploaders as $uploader){
            $medias = $this->db->select("SELECT * FROM wf_media WHERE `table` = '".$this->table."' AND item_id = '".$id."' AND type = '".$uploader[2]."';");
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $options = array(
                        'script_url' => $this->base_root.'admin/uploads/',
                        'upload_dir' => $this->base_root.'/uploads/',
                        'upload_url' => $this->base_root.'uploads/',
                        'param_name' => 'files',
                        'delete_type' => 'DELETE',
                        'max_file_size' => null,
                        'min_file_size' => 1,
                        'accept_file_types' => '/.+$/i',
                        'max_number_of_files' => null,
                        'max_width' => null,
                        'max_height' => null,
                        'min_width' => 1,
                        'min_height' => 1,
                        'discard_aborted_uploads' => true,
                        'orient_image' => false,
                        'image_versions' => array(
                            'medium' => array(
                                'upload_dir' => $this->base_root.'/uploads/medium/',
                                'upload_url' => $this->base_root.'/uploads/medium/',
                                'max_width' => 248,
                                'max_height' => 248,
                                'jpeg_quality' => 95
                            ),
                            'thumbnail' => array(
                                'upload_dir' => $this->base_root.'/uploads/thumbs/',
                                'upload_url' => $this->base_root.'/uploads/thumbs/',
                                'max_height' => 80
                            )
                        )
                    );
            if ($uploader[3]) {
                $options = array_replace_recursive($options, $uploader[3]);
            }
            $upload = new Upload($options);
            foreach ($medias as $media){
                $filename = $DIR_ROOT.'/uploads/'.$media["file"];
                $file = array();
                if(is_file($filename)){
                    $file["filename"] = $media["file"];
                    $file["type"] = finfo_file($finfo, $filename);
                    $file["size"] = filesize ($filename);
                    $file = $upload->copy_file($filename, $file["filename"], $file["size"], $file["type"], $media["id"]);
                    $this->db->query("  INSERT INTO  `wf_media` (`id` ,`table` ,`item_id` ,`type` ,`file`)
                                         VALUES                  (NULL ,'".$table."','".$new_id."','".$uploader[2]."','".$file->name."');
                                    ");
                }
                else{
                    $this->db->query("DELETE FROM `wf_media` WHERE `id` = ".$media["id"]." LIMIT 1");
                }
            }
            finfo_close($finfo);
        }
        return $new_id;
    }  

     public function saveEntityStructured($id, $fields=FALSE, $table = FALSE){    
        if (!$table) $table = $this->table;
        $querys = 0;
        $query = "UPDATE $table SET ";
        if ($fields){
            $first = TRUE;
            foreach($fields as $sub_fields){
                foreach ($sub_fields as $field){
                    $query .= $this->save_field($id, $field, $table, $first);
                    $first = FALSE;
                }
            }
        }     
        $query .= ' WHERE id='.$id.' LIMIT 1;';
        return $this->db->query($query);
    }  

    //////////////////////////////////////////////////////////////
    //Ende der Methoden zur Verwaltung strukturierter Entitäten// 
    ////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////
    //Methoden zur Verwaltung Multilang Entitäten// 
    ///////////////////////////////////////////////////

     public function showEntityMultilang($id,$fields,$langs,$lang_fields,$table = FALSE){
         global $FORM_COUNT;
         if (!$table) $table = $this->table;
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "save_btn") $this->saveEntityMultilang($id, $fields, $langs, $lang_fields, $table);
            if (isset($_POST["btn".$FORM_COUNT]) && $_POST["btn".$FORM_COUNT] == "saveback") { $this->saveEntityMultilang($id, $fields, $langs, $lang_fields, $table); echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';}
            else if (isset($_POST["reload"])) echo '<script>window.location = "'.$_SESSION["_registry"]["variables"]["backlink"].'";</script>';
            else if (isset($_GET["e_copy"])) {
                $id = $this->copyEntityMultilang($id,$fields, $langs, $lang_fields,$table);
            }
            else if ($id == "none") $id = $this->newEntity($fields, $table);     
            if ($fields){
            $entity = $this->getEntityMultilang($id, $fields,$langs,$lang_fields,$table);
                    $html.= $this->edit_buttons($id,"edit_buttons_top");
            $html .= '
                <ul class="nav nav-tabs" id="myTab"  style="clear:both;">';
                $first = true;
                foreach($langs as $lang){
                    $html .= '
                    <li class="';
                    if ($first) $html .= ' active';
                    $first = false;
                    $html .='"><a href="#content_'.$lang.'" data-toggle="tab">'.$lang.'</a></li>';
                }
            $html .= '
                </ul>
                <div class="tab-content">
            <form enctype="multipart/form-data" action="?edit='.$id.'" method="post" id="edit_form'.$FORM_COUNT.'" name="edit_form'.$FORM_COUNT.'">
                <input type="hidden" name="btn'.$FORM_COUNT.'" id="btn'.$FORM_COUNT.'" value="">';
                $first = true;
                foreach($langs as $lang){
                    if ($lang != "de") $sub_lang = '_'.$lang;
                    $html .= '
                    <div class="tab-pane';
                    if ($first) $html .= ' active"';
                    else  $html .= '" style="display:none;"';
                    $first = false;
                    $html .=' id="content_'.$lang.'">';
                    $html .= '
                        <table class="showEntity" >';
                    foreach($fields as $key=>$field){
                            $field[2] .= $sub_lang;
                            if(!$sub_lang || in_array($key, $lang_fields)){
                            if ($field[0] == "Hidden") {
                            $html .= '    <tr><td class="entity_title"></td><td class="entity_field">'.$fieldData.'</td></tr>';
                            continue;
                            }
                            elseif ($field[0] != "Input") {
                                $call = "getEditField"."_".$field[0];
                                $fieldData = $this->$call($id,$field,$entity);
                            }
                            else $fieldData = '<input type="text" name="save['.$field[2].']" value=\''.htmlspecialchars ($entity[$field[2]],ENT_QUOTES,"UTF-8").'\' />';
                            $html .= '
                            <tr><td class="entity_title">'.$field[1].'</td><td class="entity_field">'.$fieldData.'</td></tr>';
                        }
                    }           
                $html .= '  
                    </table>
                </div>';
            }

        }
        $html .= '
            </form>';
        $html .='
            <script>
            $(\'#myTab a\').click(function (e) {
                e.preventDefault();
                $(".tab-pane").fadeOut("slow");
                $($(this).attr(\'href\')).fadeIn("slow");
            })
            </script>';
                    $html.= $this->edit_buttons($id,"edit_buttons_bottom");
        $FORM_COUNT++;
        return $html;
    } 


     public function getEntityMultilang($id,$fields,$langs,$lang_fields,$table = FALSE){
        if (!$table) $table = $this->table;
        $query = "SELECT `id`";
        if ($fields){
            foreach($fields as $field){
                if ($field[0] == "DateRange" || $field[0] == "DateRangeBig") $query .= ", `".$field[2]."`, `".$field[3]."`";
                else if ($field[0] == "Hidden") $query .= ", `".$field[1]."`";
                else if ($field[0] == "BoolSelectRelation" || $field[0] == "OrderedBoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Uploads" || $field[0] == "Upload"  ) {}
                else $query .= ", `".$field[2]."`";
            }   
        } 
        $query .= " FROM `".$table."` WHERE id=".$id." LIMIT 1;";
        $result = $this->db->query_fetch($query);
            foreach($langs as $lang){
                foreach($fields as $key => $field){
                    if($lang != "de" && in_array($key, $lang_fields)){
                        $result[$field[2]."_".$lang] = $this->db->query_fetch_single("SELECT value FROM wf_multilang WHERE ref_id = $id AND ref_field = '".$field[2]."' AND ref_table = '".$table."' AND lang = '".$lang."'");
                    }
                }
            }
        return $result;
    }  

    public function saveEntityMultilang($id, $fields, $langs, $lang_fields, $table = FALSE){  
        if (!$table) $table = $this->table;
        $querys = 0;
        unset($langs[0]);
        $query = "UPDATE $table SET ";
        if ($fields){
            $first = TRUE;
            foreach($fields as $key => $field){
                $query .= $this->save_field($id, $field, $table, $first);
                foreach ($langs as $lang){
                    if(in_array($key, $lang_fields)){
                        if($_POST["save"][$field[2].'_'.$lang]){
                        $lang_id = $this->db->query_fetch_single("SELECT id FROM wf_multilang WHERE ref_id = $id AND ref_field = '".$field[2]."' AND ref_table = '".$table."' AND lang = '".$lang."'");
                        if($lang_id) $this->db->query("UPDATE wf_multilang SET value = '".mysql_escape_string($_POST["save"][$field[2].'_'.$lang])."' WHERE id = $lang_id");
                        else $this->db->query(" INSERT INTO `wf_multilang` (
                                                            `id` , `lang` , `ref_id` , `ref_table` , `ref_field` , `value`)
                                                VALUES (    NULL , '$lang', '$id', '$table', '".$field[2]."', '".mysql_escape_string($_POST["save"][$field[2].'_'.$lang])."');");
                        }
                    }

                }
                $first = FALSE;
            }
        }     
        $query .= ' WHERE id='.$id.' LIMIT 1;';
        return $this->db->query($query);
    } 

    public function copyEntityMultilang($id,$fields,$table = FALSE){
        $new_id = $this->copyEntity($id,$fields,$table);
        $lang_fields = $this->db->select("SELECT * FROM wf_multilang WHERE ref_id = $id;");
        foreach ($lang_fields as $lang_field){
                            $this->db->query(" INSERT INTO `wf_multilang` (
                                                            `id` , `lang` , `ref_id` , `ref_table` , `ref_field` , `value`)
                                                VALUES (    NULL , '".$lang_field["lang"]."', '$new_id', '".$lang_field["ref_table"]."', '".$lang_field["ref_field"]."', '".mysql_escape_string($lang_field["value"])."');");
        }
        return $new_id;
    }

    //////////////////////////////////////////////////////////////
    //Ende der Methoden zur Verwaltung Multilang Entitäten// 
    ////////////////////////////////////////////////////////////

    public function save_field($id, $field, $table, $first){
                if ($field[0] == "Uploads"){}
                elseif ($field[0] == "Upload"){
$options = array(
            'script_url' => $this->base_root.'admin/uploads/',
            'upload_dir' => $this->base_root.'/uploads/',
            'upload_url' => $this->base_root.'uploads/',
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,
            'image_versions' => array(
                'medium' => array(
                    'upload_dir' => $this->base_root.'/uploads/medium/',
                    'upload_url' => $this->base_root.'/uploads/medium/',
                    'max_width' => 248,
                    'max_height' => 248,
                    'jpeg_quality' => 95
                ),
                'thumbnail' => array(
                    'upload_dir' => $this->base_root.'/uploads/thumbs/',
                    'upload_url' => $this->base_root.'/uploads/thumbs/',
                    'max_height' => 80
                )
            )
        );
                    $upload = new Upload($options);
                    $upload->direct_upload($field[2],array("table" => $table, "item_id" => $id));
                }
                else if($field[0] == "DateRange" || $field[0] == "DateRangeBig"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".mysql_escape_string($_POST["save"][$field[2]])."', `".$field[3]."` = '".mysql_escape_string($_POST["save"][$field[3]])."'";   
                    $querys ++; 
                }
                else if($field[0] == "Number"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".str_replace(array($this->lang["numbers"]["thousands_sep"],$this->lang["numbers"]["dec_point"]), array("","."), $_POST["save"][$field[2]])."'";   
                    $querys ++; 
                }             
                else if($field[0] == "BoolSelect"){
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".serialize($_POST["save"][$field[2]])."'";   
                    $querys ++; 
                }
                
                else if($field[0] == "BoolSelectRelation"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE ".$field[4]." = $id ;");
                    if(is_array($_POST["save"][$field[1]])){
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
                else if($field[0] == "Alias"){
                    if(!$this->db->affected_query("SELECT id FROM ".$this->table." WHERE id = $id AND alias = '".$_POST["save"][$field[2]]."'") || !$_POST["save"][$field[2]]){
                        $site = $this->db->query_fetch("SELECT * FROM ".$this->table." WHERE id = $id");
                        if(!$_POST["save"][$field[2]] || $_POST["save"][$field[2]] == "") {
                            $alias = str_replace("%2F","//",urlencode($site["titel"]));
                        }
                        else $alias = str_replace("%2F","//",urlencode($_POST["save"][$field[2]]));
                        $counter = $this->db->affected_query("SELECT id FROM wf_sites WHERE `alias` LIKE '$alias';");
                        if($counter) $alias = $alias."_".$counter;
                        if (!$first) $query .= ", "; 
                        $query .= "`".$field[2]."` = '".$alias."'";    
                        $querys ++;  
                    }
                }
                else {
                    if (!$first) $query .= ", "; 
                    $query .= "`".$field[2]."` = '".mysql_escape_string($_POST["save"][$field[2]])."'";    
                    $querys ++;  
                }
                return $query;
    }    
    
     public function newEntity($fields=FALSE, $table = FALSE){
        if (!$table) $table = $this->table;
        $query = "INSERT INTO ".$table." (
                    `id` ,
                    `update` ,
                    `editor`";
        if ($this->db->is_field('order', $table)){
            $order =  ", '".($this->db->get_max('order', $table) + 1)."'";
            $query .= " ,
                    `order`";
        }
            $query .= ")VALUES (
                    NULL , NOW( ) , '".$_SESSION["_registry"]["user"]["name"]."'".$order."
                    );
                ";
        return $this->db->lastindex_query($query);
    }
}
?>
