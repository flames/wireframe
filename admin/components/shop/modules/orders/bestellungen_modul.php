<?php
class bestellungen_modul extends modul{   
    function __construct(){
        $this->path             = pathinfo(__FILE__,PATHINFO_DIRNAME)."/";
        $this->lang["module"]   = parse_ini_file( $this->path."localisation/de.ini");
        $this->table            = "wf_order_details";
        $this->shop 			= new shop();
        $this->init();
    }

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
     	$this->order = $this->shop->get_order($id);
        if (!$table) $table = $this->table;
        $query = "SELECT `id`";
        if ($fields){
            foreach($fields as $sub_fields){
                foreach ($sub_fields as $field){
                    if ($field[0] == "DateRange" || $field[0] == "DateRangeBig") $query .= ", `".$field[2]."`, `".$field[3]."`";
                    else if ($field[0] == "Hidden") $query .= ", `".$field[1]."`";
                    else if ($field[0] == "BoolSelectRelation" || $field[0] == "OrderedBoolSelectRelation" || $field[0] == "InputsRelation" || $field[0] == "Uploads" || $field[0] == "Bestellung" || $field[0] == "Kundendaten") {}
                    else $query .= ", `".$field[2]."`";
                }
            }   
        
        } 
        $query .= " FROM `".$table."` WHERE id=".$id.";";
        return $this->db->query_fetch($query);
    }  

    public function save_field($id, $field, $table, $first){
                if ($field[0] == "Uploads" || $field[0] == "Bestellung" || $field[0] == "Kundendaten"){}
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
                
                else if($field[0] == "BoolSelectRelation"){
                    $this->db->query("DELETE FROM ".$field[2]." WHERE ".$field[4]." = $id ;");
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

    public function getEditField_Bestellung($id,$field,$entity){
        $html = $this->shop->render_order($this->order);
        return $html;
    }

    public function getEditField_Kundendaten($id,$field,$entity){
        $html = $this->shop->render_user($this->order);
        return $html;
    }
}
?>
