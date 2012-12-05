<?php
class shop {
    protected       $db;
    protected       $time;
    protected       $lang;
    protected       $base_url;
    protected       $base_root;
    protected       $config;  
    
    function __construct(){
        $this->init();
    }
    
    protected function init(){
        $this->db           =  $_SESSION["_registry"]["db"];
        $this->lang         =  $_SESSION["_registry"]["lang"];
        $this->time         =  $_SESSION["_registry"]["time"];
        $this->base_url     =  $_SESSION["_registry"]["system_config"]["site"]["base_url"];
        $this->base_root    =  $_SESSION["_registry"]["root"]."/";
    }
    protected function has_options($id, $type="prod"){
        if ($type=="group") $ent_type ="0"; else $ent_type ="1";
        if($this->db->query_fetch_single("SELECT `id` FROM `wf_options` WHERE `entity_type` =$ent_type  AND `entity_id` =$id LIMIT 1;")) return true;
    }
    protected function get_options($id, $type, $group = false){
    	switch($type){
    		case "group":
    			        $group_options = $this->db->select("SELECT * FROM `wf_options` WHERE `group` IS NULL AND `entity_type` =0 AND `entity_id` =".$entity["group"]." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
        				foreach($group_options as $option_id => $item_option){
            				if($item_option["type"] != 0){
            					$childs = $this->db->select("SELECT * FROM `wf_options` WHERE `type` IS NULL AND `group` =".$option_id." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
            					foreach($childs as $child) $item_option["childs"][$child["id"]] = $child;
            				}
            				$options[$option_id] = $item_option;
    					}
    			        break;
    		case "prod":
                        if($group){
                            $group_options = $this->db->select("SELECT * FROM `wf_options` WHERE `group` IS NULL AND `entity_type` =0 AND `entity_id` =".$group." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
    					foreach($group_options as $option_id => $item_option){
            				$ref = $this->db->query_fetch("SELECT `operation`,`value` FROM `wf_options` WHERE `type` = 4 AND `group` =".$option_id." AND `entity_id` =".$id." LIMIT 1;");
            				if($ref) {
            					foreach($ref as $key => $value) if($value) {$item_option[$key] = $value;}
            					if($item_option["type"] != 0){
            						$childs = $this->db->select("SELECT * FROM `wf_options` WHERE `type` IS NULL AND `group` =$option_id ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
            						foreach($childs as $child){
            					        $sub_ref = $this->db->query_fetch("SELECT `operation`,`value` FROM `wf_options` WHERE `type` = 4 AND `group` =".$option_id." AND `entity_id` =".$id." LIMIT 1;");
                            			if($sub_ref) {
                            				foreach($sub_ref as $key => $value){
                            					if($value) $child[$key] = $value;
                            					$item_option["childs"][$child["id"]] = $child;
                            				}
                            			}
            						}
            					}
            					$options[$option_id] = $item_option;
            				}
    					}}
                        $item_options = $this->db->select("SELECT * FROM `wf_options` WHERE `group` IS NULL AND `entity_type` =1 AND `entity_id` =".$id." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
    					foreach($item_options as $option_id => $item_option){
            				if($item_option["type"] != 0){
            					$childs = $this->db->select("SELECT * FROM `wf_options` WHERE `type` IS NULL AND `group` =".$option_id." ORDER BY id;",MYSQLI_ASSOC,FALSE,"id");
            					foreach($childs as $child) $item_option["childs"][$child["id"]] = $child;
            				}
            				$options[$option_id] = $item_option;
    					}
    					break;
    	}
    		return $options;
    }

    protected function render_options($prod,$usr_options = false){
        if(!$prod["options"]) return false;
        $options = $prod["options"];
        $html = '
            <input type="hidden" name="action" value="get_prod_price" />';
        foreach($options as $option){
            switch ($option["type"]){
                case 0 :
                            $html .= '
                            <span class="prod_option"><h4><input '; if($usr_options[$option["id"]]) $html .= 'checked'; $html.=' type="checkbox" name="options['.$option["id"].']" value="'.$option["value"].'" /> '.$option["name"].' ('.number_format($this->get_option_price($prod["price"], $option),2,',','').' CHF)</h4></span><br/>';
                            break;
                case 1 :
                            $html .= '
                            <span class="prod_option"><h4>'.$option["name"].'</h4>';
                            foreach($option["childs"] as $child){                           
                            $html .= 
                                 '<input '; if($usr_options[$option["id"]]["childs"][$child["id"]]) $html .= 'checked';  $html .= ' type="checkbox" name="options['.$option["id"].'][childs]['.$child["id"].']" value="'.$child["value"].'" /> '.$child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)<br/>';
                            }
                            $html .= '
                            </span><br/>';
                            break;
                case 2 :
                            $html .= '
                            <span class="prod_option"><h4>'.$option["name"].'</h4>';
                            foreach($option["childs"] as $child){                           
                            $html .= 
                                '<input '; if($usr_options[$option["id"]] == $child["id"]) $html .= 'checked'; $html.=' type="radio" name="options['.$option["id"].']" value="'.$child["id"].'" /> '.$child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)<br/>';
                            }
                            $html .= '
                            </span><br/>';
                            break;
                case 3 :
                            $html .= '
                            <span class="prod_option"><h4>'.$option["name"].'<br/>
                            <select name="options['.$option["id"].']">
                            <option> - Bitte wählen - </option>';
                            foreach($option["childs"] as $child){                           
                            $html .= '
                            <option '; if($usr_options[$option["id"]] == $child["id"]) $html .= 'selected="selected"'; $html.=' value="'.$child["id"].'">'.$child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)</option>';
                            }
                            $html .= '
                            </select></h4>
                            </span><br/>';
                            break;
            }
        }
        return $html;
    }

    protected function get_group($id){
    	$group = $this->db->query_fetch("SELECT *  FROM wf_groups WHERE id = $id LIMIT 1;");
    	$group["attr"] = $this->db->select("SELECT id, type, name FROM wf_group_attr WHERE group_id = $id ORDER BY id;");
    	$group["options"] = $this->get_options($id,"group");
    	return $group;
    }

    protected function get_product($id){
    	$product = $this->db->query_fetch("SELECT * FROM wf_prods WHERE id = $id LIMIT 1;");
    	$product["images"] = $this->db->select_single("SELECT file FROM wf_media WHERE `table` = 'wf_prods' AND item_id = $id AND type = 'prod_pics';");
    	$product["attr"] = $this->db->select("SELECT t1.name,t1.type,t2.value FROM wf_group_attr AS t1, wf_prod_attr AS t2  WHERE t2.attr_id = t1.id AND t2.prod_id = $id ORDER BY t1.id ;");
        $product["options"] = $this->get_options($id,"prod",$product["group"]);
    	return $product;
    }

    protected function get_product_short($id){
    	$product = $this->db->query_fetch("SELECT id, art_num, name, price, short_desc  FROM wf_prods WHERE id = $id LIMIT 1;");
    	$product["thumb"] = $this->db->query_fetch_single("SELECT CONCAT('".$this->base_url."uploads/thumbs/', file) FROM wf_media WHERE `table` = 'wf_prods' AND item_id = $id AND type = 'prod_pics' LIMIT 1;");
    	return $product;
    }

    protected function get_products($prod_ids,$type){
    	switch ($type){
    		case "short": foreach($prod_ids as $prod) $prods[] = $this->get_product_short($prod); return $prods;
    		case "full":  foreach($prod_ids as $prod) $prods[] = $this->get_product($prod); return $prods;
    	}
    }

    public function show_prods($prods,$type,$qty){
    	$prods = $this->get_products($prods,"short");
    	$row_count = 0;
    	foreach($prods as $prod){
    		$row_count++;
    		if($row_count == $qty){
    			$extra_class = ' last';
    			$row_count = 0;	
    		}
    		else $extra_class = '';
    		$html .= '
    		<div class="well prod_box'.$extra_class.'">
    			<img src="'.$prod["thumb"].'" />
    			<h4>'.$prod["name"].'</h4>
    			'.$prod["short_desc"].'
    			<div class="shop_box_foot">
    			<span class="price label label-info">ab '.number_format($prod["price"],2,',','').' CHF</span>
    			<a class="btn btn-success prod_details" href="'.$prod["id"].'/'.str_replace("%2F","//",urlencode($prod["name"])).'/"><i class="icon-zoom-in icon-white"></i>Details</a>
    			<div class="clear"></div>
    			</div>
    		</div>';
    	}
    	echo $html;
    }

    public function show_prod_details($id,$type){
    	$prod = $this->get_product($id);
    	$html ='
    		<div class="product_details">
    			<div id="gallery" class="gallery" data-target="#modal-gallery" data-toggle="modal-gallery">
    				<img src="'.$this->base_url.'uploads/medium/'.$prod["images"][0].'" class="main_pic">';
    	if(count($prod["images"]) > 1){
    		foreach($prod["images"] as $thumb){
    			$html .='
    		<a rel="gallery" href="'.$this->base_url.'uploads/'.$thumb.'">
    			<img class="thumb"  href="'.$this->base_url.'uploads/medium/'.$thumb.'" src="'.$this->base_url.'uploads/thumbs/'.$thumb.'">
    		</a>
    			';
    		}
    	}
    	$html .='
    			</div>
    			<div class="well prod_info">
    			<h1>'.$prod["name"].'<span class="price label label-info">ab '.$prod["price"].' CHF</span></h1>
    			'.$prod["desc"].'';
        if($prod["attr"]){ 
        $html .='
        <h3>Merkmale</h3>';
            foreach($prod["attr"] as $attr){
                $html .= '
                    <span class="badge badge-info">'.$attr["name"].'</span>';
            }
        }
        if ($this->has_options($id)){
        $html.='
                <h3>Optionen</h3>';
        $html.= '<form id="option_form"><input type="hidden" name="id" value="'.$id.'" />'.$this->render_options($prod).'</form>';
        }
        $html.='
                <form id="to_basket">
                <input type="hidden" name="action" value="add_to_basket" />
                <input type="hidden" name="id" value ="'.$prod["id"].'" />
                <input type="text" name="qty" value="1" style="width:25px" /> Stück
                <a class="btn btn-success to_basket">
                    In den Warenkorb
                </a>
                </form>
                <span class="btn btn-info fin_price">Ihr Preis: '.number_format($prod["price"],2,',','').' CHF</span>
    			</div>
    		</div>
            <script type="text/javascript">
                $("#option_form :input").change(function() {
                    $.post(URL_ROOT + "includes/ajax.php", $("#option_form").serialize(),function(data) {
                        $(".fin_price").html("Ihr Preis: " + data + " CHF");
                    });
                });
                $("#to_basket").click(function() {
                    $.post(URL_ROOT + "includes/ajax.php", $("#option_form").serialize() +"&"+ $("#to_basket").serialize() ,function(data) {
                        alert("Artikel wurde erfolgreich in den Warenkorb gelegt.\\n" + data);
                        $(".minibasket_text").html(data);
                    });
                });
            </script>
<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn modal-download" target="_blank">
            <i class="icon-download"></i>
            <span>Download</span>
        </a>
        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
            <i class="icon-play icon-white"></i>
            <span>Slideshow</span>
        </a>
        <a class="btn btn-info modal-prev">
            <i class="icon-arrow-left icon-white"></i>
            <span>Previous</span>
        </a>
        <a class="btn btn-primary modal-next">
            <span>Next</span>
            <i class="icon-arrow-right icon-white"></i>
        </a>
    </div>
</div>
    	';
    	return $html;
    }

    protected function get_option_price($baseprice, $option){
        switch ($option["operation"]){
                case 0: $option_price = $option["value"]; break;
                case 1: $option_price = $baseprice / 100 * $option["value"]; break;
        }
        return $option_price;
    }

    public function get_price($id, $usr_options, $format = false){
        $prod = $this->get_product($id);
        $options = $prod["options"];
        $price = $prod["price"];
        foreach ($options as $key => $option){
                if($option["type"] == 0 && isset($usr_options[$key]) && $usr_options[$key]) $price += $this->get_option_price($prod["price"], $option); 
                if($option["type"] == 1){
                    foreach($option["childs"] as $child_key => $child) {
                        if(isset($usr_options[$key]["childs"][$child_key]) && $usr_options[$key]["childs"][$child_key]) {
                            $price += $this->get_option_price($prod["price"], $child);
                        }
                    }
                }
                else{
                    foreach($option["childs"] as $child_key => $child) {
                        if(isset($usr_options[$key]) && $usr_options[$key] == $child_key) {
                            $price += $this->get_option_price($prod["price"], $child);
                        }
                    }
                }
                               
        }
    if($format) return number_format($price,2,',','');
    else return $price;
    }

    /////////////////////
    //BASKET FUNCTIONS//
    ///////////////////

    public function generate_adress($adress_array){        
        $adress = '';
        if($adress_array["Firma"]) $adress .= $adress_array["Firma"].'<br/>';
        $adress .= $adress_array["Anrede"].' '.$adress_array["Vorname"].' '.$adress_array["Nachname"].'<br/>';
        $adress .= $adress_array["Straße"].' '.$adress_array["Hausnummer"].'<br/>';
        $adress .= $adress_array["PLZ"].' '.$adress_array["Ort"].'<br/>';
        $adress .= $this->db->query_fetch_single("SELECT name_de FROM `wf_countrys` WHERE id = '".$adress_array["Land"]."' LIMIT 1;").'<br/>';
        if($adress_array["zus. Info"]) $adress .= 'Zustellerinfo: '.$adress_array["zus. Info"].'<br/>';
        return $adress;
    }

    public function get_minibasket(){
        $basket_items = $this->db->select("SELECT * FROM `wf_basket` WHERE session_id = '".session_id()."' AND order_id = 0;");    
        $item_count = 0;
        $basket_price = 0;
        if(!$basket_items) return "Ihr Warenkorb ist leer.";
        foreach($basket_items as $basket_item){
            $item_count += $basket_item["qty"];
            $basket_price += $this->get_price($basket_item["article"], unserialize($basket_item["options"])) * $basket_item["qty"];
        }
        return "In ihrem Warenkorb befinden sich $item_count Produkte für ".number_format($basket_price,2,',','')." CHF.";
    }

    public function get_basket($step = 1){
        global $user;
        $basket_items = $this->db->select("SELECT * FROM `wf_basket` WHERE session_id = '".session_id()."' AND order_id = 0;");    
        $item_count = 0;
        $basket_price = 0;
        if(!$basket_items) return "<h3><br/>Ihr Warenkorb ist leer.</h3>";
        if($step == 1){
        $buttons = '
                        <button onclick="goto_step(2);" class="btn btn-success basket_navi_right" type="button" title="Zur Kasse">
                            <i class="icon-shopping-cart icon-white"></i>
                            <span>Zur Kasse</span>
                        </button>';
        $basket = '
            <table>
                <thead>
                <tr>
                    <th>Anzahl</th>
                    <th>Artikel</th>
                    <th>Einzelpreis</th>
                    <th>Gesamtpreis</th>
                    <th style="padding-left:15px; width:145px;">Optionen</th>
                </tr>
                </thead>';
        foreach($basket_items as $basket_item){
            $prod = $this->get_product($basket_item["article"]);
            $item_count += $basket_item["qty"];
            $price = $this->get_price($basket_item["article"], unserialize($basket_item["options"]));
            $basket_price += $price * $basket_item["qty"];
            $basket .= '
                <tr>
                    <td>'.$basket_item["qty"].'</td>
                    <td>'.$prod["name"].' ('.$prod["art_num"].')</td>
                    <td>'.number_format($price,2,',','').' CHF</td>
                    <td>'.number_format($price * $basket_item["qty"],2,',','').' CHF</td>
                    <td style="padding-left:15px; width:145px;">
                        <button data-toggle="modal" href="#basket_Modal_'.$basket_item["id"].'" class="btn btn-success" type="button" title="Bearbeiten">
                            <i class="icon-edit icon-white"></i>
                            <span>Bearbeiten</span>
                        </button>
                        <button onclick="var r=confirm(\'Wollen sie den Artikel wirklich entfernen?\'); if (r==true) delete_article('.$basket_item["id"].');" class="del_button btn btn-danger" type="button" title="Löschen">
                        <i class="icon-trash icon-white"></i>
                        <span></span>
                    </button>
                    </td>
                </tr>';
            $modals .= '
            <div class="modal hide" id="basket_Modal_'.$basket_item["id"].'">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h3>Warenkorb bearbeiten - '.$prod["name"].' ('.$prod["art_num"].')</h3>
                </div>
                <div class="modal-body">
                <form class="update_basket_form">
                    <input type="hidden" name="action" value="update_basket" />
                    <input type="hidden" name="id" value="'.$basket_item["id"].'" />
                    <input type="text" name="qty" value="'.$basket_item["qty"].'" style="width:25px" /> Stück
                </form>
                <form class="option_form">
                    '.$this->render_options($prod,unserialize($basket_item["options"])).'
                </form>
                </div>
                <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Close</a>
                <a href="#" class="btn btn-primary update_basket">Speichern</a>
                </div>
            </div>
            '; 
        }
        $basket .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-top:1px solid black;">'.number_format($basket_price,2,',','').' CHF</td>
                    <td></td>
                </tr>';
        $basket .='</table>';
        $script = '                
                $(".update_basket").click(function() {
                    var option_form = $(this).parent().parent().children(".modal-body").children(".option_form");
                    var update_form = $(this).parent().parent().children(".modal-body").children(".update_basket_form");
                    $.post(URL_ROOT + "includes/ajax.php", option_form.serialize() +"&"+ update_form.serialize() ,function(data) {
                        $(".minibasket_text").html(data);
                    });
                    $.post(URL_ROOT + "includes/ajax.php", {action : "get_basket", step : "1"} ,function(data) {
                        $("#basket_content").html(data);
                        $(".modal-backdrop").hide();
                    });
                });
                function delete_article(id){
                    $.post(URL_ROOT + "includes/ajax.php", {action : "delete_basket", id : id} ,function(data) {
                        $(".minibasket_text").html(data);
                    });
                    $.post(URL_ROOT + "includes/ajax.php", {action : "get_basket", step : "1"} ,function(data) {
                        $("#basket_content").html(data);
                    });
                }
        ';
        }
        elseif($step == 2){
            if(!$_SESSION["_registry"]["user"]["name"]){
                $basket = '
                    <h3><br/>Sie müssen angemeldet sein um fortzufahren</h3>
                    Wenn sie schon Kunde sind können sie sich hier <a data-toggle="modal" href="#Login_Modal">Anmelden</a>, ansonsten müssen sie sich zuerst <a data-toggle="modal" href="#Register_Modal">registrieren</a>.
                ';
            }
            else{
                 $adress = array(
                    "Anrede" => $user['nodes']['anrede'],
                    "Vorname" => $user['nodes']['vorname'],
                    "Nachname" => $user['nodes']['nachname'],
                    "Firma" => $user['nodes']['firma'],
                    "Straße" => $user['nodes']['strasse'],
                    "Hausnummer" => $user['nodes']['nr'],
                    "PLZ" => $user['nodes']['plz'],
                    "Ort" => $user['nodes']['ort'],
                    "zus. Info" => $user['nodes']['info'],
                    "Land" => $user['nodes']['land']
                );
                $basket = 'Bitte überprüfen sie ihre Rechnungs- und Lieferadresse. Sie können diese in ihrem <a data-toggle="modal" href="#Profile_Modal">Benutzerkonto</a> ändern.';
                if($user['nodes']['use_del']){
                    $del_adress = array(
                        "Anrede" => $user['nodes']['del_anrede'],
                        "Vorname" => $user['nodes']['del_vorname'],
                        "Nachname" => $user['nodes']['del_nachname'],
                        "Firma" => $user['nodes']['del_firma'],
                        "Straße" => $user['nodes']['del_strasse'],
                        "Hausnummer" => $user['nodes']['del_nr'],
                        "PLZ" => $user['nodes']['del_plz'],
                        "Ort" => $user['nodes']['del_ort'],
                        "zus. Info" => $user['nodes']['del_info'],
                        "Land" => $user['nodes']['del_land']
                    );
                    $basket .= '
                    <div style="width:50%; float:left;">
                    <h3>Rechnungsadresse</h3>
                    '.$this->generate_adress($adress).'
                    </div><div style="width:50%; float:left;">
                    <h3>Lieferadresse</h3>
                    '.$this->generate_adress($del_adress).'
                    </div>';
                }
                else{
                    $basket .= '
                    <h3>Rechnungs- und Lieferadresse</h3>
                    '.$this->generate_adress($adress);
                }
            $basket .= '<h3>Versandart</h3>
                            <input type="radio" name="del_type" value="1"'; if($_SESSION["_registry"]["basket"]["shipping"] == "1") $basket .= ' checked="checked" '; $basket .= '> Standart-Versand (5 CHF)<br/>
                            <input type="radio" name="del_type" value="2"'; if($_SESSION["_registry"]["basket"]["shipping"] == "2") $basket .= ' checked="checked" '; $basket .= '> Express-Versand (10 CHF)<br/>
                            <input type="radio" name="del_type" value="3"'; if($_SESSION["_registry"]["basket"]["shipping"] == "3") $basket .= ' checked="checked" '; $basket .= '> Kurier (20 CHF)';
            $buttons = '
                    <button onclick="goto_step(1);" class="btn btn-info basket_navi_left" type="button" title="zurück zum Warenkorb">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>zurück zum Warenkorb</span>
                    </button>
                    <button onclick="if($(\'input[name=del_type]:checked\').val()) goto_step(3); else alert(\'Bitte wählen sie eine Versandart.\')" class="btn btn-success basket_navi_right" type="button" title="Zahlungsoptionen">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>Zahlungsoptionen</span>
                    </button>';
            $script = '
            $("#Profile_Modal").on("hidden", function () {
                goto_step(2);
            })';
            }
        }
        elseif($step == 3){
           if( $_POST["shipping"]) $_SESSION["_registry"]["basket"]["shipping"] = $_POST["shipping"];
            $basket .= '<h3>Zahlungsart</h3>
                            <input type="radio" name="pay_type" value="1"'; if($_SESSION["_registry"]["basket"]["payment"] == "1") $basket .= ' checked="checked" '; $basket .= '> Vorkasse<br/>
                            <input type="radio" name="pay_type" value="2"'; if($_SESSION["_registry"]["basket"]["payment"] == "2") $basket .= ' checked="checked" '; $basket .= '> Paypal<br/>
                            <input type="radio" name="pay_type" value="3"'; if($_SESSION["_registry"]["basket"]["payment"] == "3") $basket .= ' checked="checked" '; $basket .= '> Nachname';
            $buttons = '
                    <button onclick="goto_step(2);" class="btn btn-info basket_navi_left" type="button" title="zurück zum Warenkorb">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>zurück zu Versandoptionen</span>
                    </button>
                    <button onclick="if($(\'input[name=pay_type]:checked\').val()) goto_step(4); else alert(\'Bitte wählen sie eine Zahlungsart.\')" class="btn btn-success basket_navi_right" type="button" title="Zahlungsoptionen">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>Überprüfung</span>
                    </button>';
        }
        elseif($step == 4){
            if( $_POST["payment"]) $_SESSION["_registry"]["basket"]["payment"] = $_POST["payment"];
            $basket = 'Bitte überprüfen sie alle Angaben bevor sie die Bestellung endgültig ausführen.';
                    $basket = '
            <h3>Artikel</h3>
            <table>
                <thead>
                <tr>
                    <th>Anzahl</th>
                    <th>Artikel</th>
                    <th>Einzelpreis</th>
                    <th>Gesamtpreis</th>
                </tr>
                </thead>';
            foreach($basket_items as $basket_item){
                $prod = $this->get_product($basket_item["article"]);
                $item_count += $basket_item["qty"];
                $price = $this->get_price($basket_item["article"], unserialize($basket_item["options"]));
                $basket_price += $price * $basket_item["qty"];
                $basket .= '
                <tr>
                    <td>'.$basket_item["qty"].'</td>
                    <td>'.$prod["name"].' ('.$prod["art_num"].')</td>
                    <td>'.number_format($price,2,',','').' CHF</td>
                    <td>'.number_format($price * $basket_item["qty"],2,',','').' CHF</td>
                </tr>';
            }
            $basket .= '
            </table>';
                $adress = array(
                    "Anrede" => $user['nodes']['anrede'],
                    "Vorname" => $user['nodes']['vorname'],
                    "Nachname" => $user['nodes']['nachname'],
                    "Firma" => $user['nodes']['firma'],
                    "Straße" => $user['nodes']['strasse'],
                    "Hausnummer" => $user['nodes']['nr'],
                    "PLZ" => $user['nodes']['plz'],
                    "Ort" => $user['nodes']['ort'],
                    "zus. Info" => $user['nodes']['info'],
                    "Land" => $user['nodes']['land']
                );
                if($user['nodes']['use_del']){
                    $del_adress = array(
                        "Anrede" => $user['nodes']['del_anrede'],
                        "Vorname" => $user['nodes']['del_vorname'],
                        "Nachname" => $user['nodes']['del_nachname'],
                        "Firma" => $user['nodes']['del_firma'],
                        "Straße" => $user['nodes']['del_strasse'],
                        "Hausnummer" => $user['nodes']['del_nr'],
                        "PLZ" => $user['nodes']['del_plz'],
                        "Ort" => $user['nodes']['del_ort'],
                        "zus. Info" => $user['nodes']['del_info'],
                        "Land" => $user['nodes']['del_land']
                    );
                    $basket .= '
                    <div style="width:50%; float:left;">
                    <h3>Rechnungsadresse</h3>
                    '.$this->generate_adress($adress).'
                    </div><div style="width:50%; float:left;">
                    <h3>Lieferadresse</h3>
                    '.$this->generate_adress($del_adress).'
                    </div>';
                }
                else{
                    $basket .= '
                    <h3>Rechnungs- und Lieferadresse</h3>
                    '.$this->generate_adress($adress);
                }
                $buttons = '
                    <button onclick="goto_step(3);" class="btn btn-info basket_navi_left" type="button" title="zurück zum Warenkorb">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>zurück zu Zahlungsart</span>
                    </button>
                    <button onclick="goto_step(5)" class="btn btn-success basket_navi_right" type="button" title="Zahlungsoptionen">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>Bezahlen</span>
                    </button>';
        }
        elseif($step == 5){
            $order_id = ($this->db->get_max("order_id", "wf_basket") + 1);
            $this->db->query("UPDATE wf_basket SET user_id = ".$_SESSION["_registry"]["user"]["id"].", order_id = ".$order_id." WHERE  session_id = '".session_id()."' AND order_id = 0;");
            $this->db->query("INSERT INTO `wf_order_details` (`id`, `user`, `payment`, `shipping`, `status`, `date`) VALUES ('".$order_id."', '".$_SESSION["_registry"]["user"]["id"]."', '".$_SESSION["_registry"]["basket"]["payment"]."', '".$_SESSION["_registry"]["basket"]["shipping"]."', '1', CURRENT_TIMESTAMP);");
            $basket .= 'Sie haben erfolgreich eingekauft';
            $buttons = '
                    <a href="'.$this->base_url.'" class="btn btn-success basket_navi_right" title="Zurück">
                        <i class="icon-shopping-cart icon-white"></i>
                        <span>Zurück zur Seite</span>
                    </a>';
            $_SESSION["basket"]["step"] = 5;
        }
        return $buttons.'<div style="clear:both; width:100%;">'.$basket.'</div>'.$buttons.$modals.'<script type="text/javascript">'.$script.'</script>';
    }

    public function add_basket($product_id, $options, $qty){
        $basket_item = $this->db->query_fetch("SELECT id, qty FROM `wf_basket` WHERE session_id = '".session_id()."' AND article = ".$product_id." AND options = '".serialize($options)."' AND order_id = 0 LIMIT 1;");    
        if($basket_item) {
            return $this->update_basket($basket_item["id"], $options, $qty + $basket_item["qty"]);
        }
        else{
            $this->db->query("INSERT INTO `wf_basket` 
                                        (`id` ,`session_id` ,`article` ,`options` ,`qty` ,`timestamp`)
                            VALUES 
                                        (NULL , '".session_id()."', '".$product_id."', '".serialize($options)."', '".$qty."',CURRENT_TIMESTAMP);");
            return $this->get_minibasket(); 
        }
    }

    public function update_basket($id, $options, $qty){
        $article = $this->db->query_fetch_single("SELECT `article` FROM `wf_basket` WHERE id = '".$id."' LIMIT 1;");
        $basket_item = $this->db->query_fetch_single("SELECT `id` FROM `wf_basket` WHERE id != '".$id."' AND session_id = '".session_id()."' AND article = ".$article." AND options = '".serialize($options)."' LIMIT 1;");
        if(!$basket_item) $this->db->query("UPDATE `wf_basket` SET qty = ".$qty.", options = '".serialize($options)."' WHERE id = ".$id." LIMIT 1;");
        else{
            $this->db->query("DELETE FROM `wf_basket` WHERE id = ".$id." LIMIT 1;");
            $this->db->query("UPDATE `wf_basket` SET qty = qty + ".$qty." WHERE id = ".$basket_item." LIMIT 1;");
        }
        return $this->get_minibasket(); 
    }

    public function delete_basket($id){
        $this->db->query("DELETE FROM `wf_basket` WHERE id = ".$id." LIMIT 1;");  
        return $this->get_minibasket();    
    }

    /////////////////////
    //ORDER FUNCTIONS//
    ///////////////////

    protected function render_prod_static($prod,$usr_options = false){
        $options = $prod["options"];
        $html .= '<h4>'.$prod["qty"].' x '.$prod["name"].' (a '.number_format($prod["price"],2,',','').' CHF)</h4>';
        foreach($options as $option){
            switch ($option["type"]){
                case 0 :
                            $html .= '
                            <span class="prod_option">'; if($usr_options[$option["id"]]) $html.= $option["name"].' ('.number_format($this->get_option_price($prod["price"], $option),2,',','').' CHF)</span><br/>';
                            break;
                case 1 :
                            $html .= '
                            <span class="prod_option">'.$option["name"].': ';
                            $first = TRUE;
                            foreach($option["childs"] as $child){                           
                                if($usr_options[$option["id"]]["childs"][$child["id"]]) {
                                    if(!$first) $html .= "<br/>";
                                    $html .= $child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)';
                                    $first = FALSE;
                                }
                            }
                            $html .= '
                            </span><br/>';
                            break;
                case 2 :
                            $html .= '
                            <span class="prod_option">'.$option["name"].': ';
                            foreach($option["childs"] as $child){                           
                                if($usr_options[$option["id"]] == $child["id"]) $html .= $child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)';
                            }
                            $html .= '
                            </span><br/>';
                            break;
                case 3 :
                            $html .= '
                            <span class="prod_option">'.$option["name"].': ';
                            foreach($option["childs"] as $child){                           
                                if($usr_options[$option["id"]] == $child["id"])
                                    $html .= $child["name"].' ('.number_format($this->get_option_price($prod["price"], $child),2,',','').' CHF)';
                            }
                            $html .= '
                            </span><br/><br/>';
                            break;
            }
        }
        return $html;
    }

    public function get_order($order_id){
        global $user;
        $basket_items = $this->db->select("SELECT * FROM `wf_basket` WHERE order_id = '".$order_id."';");    
        $order_details = $this->db->query_fetch("SELECT * FROM `wf_order_details` WHERE id = '".$order_id."';");  
        $order = array(
                        "order_id" => $order_details["id"],
                        "user" => user_details($this->db->query_fetch_single("SELECT name FROM `permissions_entity` WHERE id = '".$order_details["user"]."';")),
                        "payment" => $order_details["payment"],
                        "shipping" => $order_details["shipping"],
                        "status" => $order_details["status"],
                        "date" => $order_details["date"],
                        "products" => array()
                );
        $item_count = 0;
        $basket_price = 0;
        if(!$basket_items) return "<h3><br/>Keine gültige Bestellung.</h3>";
        foreach($basket_items as $basket_item){
            $prod = $this->get_product($basket_item["article"]);
            $item_count += $basket_item["qty"];
            $price = $this->get_price($basket_item["article"], unserialize($basket_item["options"]));
            $basket_price += $price * $basket_item["qty"];
            $prod["usr_options"] = unserialize($basket_item["options"]);
            $prod["price"] = $price;
            $prod["qty"] = $basket_item["qty"];
            $order["products"][] = $prod;
        }
        $order["price"] = $basket_price;
        $order["qty"] = $item_count;
        return $order;
    }

    public function render_order($order){
        $html .= '
        <fieldset>
            <legend>Bestellnummer: '.$order["order_id"].' - '.$order["date"].' - Status: '.$order["status"].'</legend>
            <div class="order_details" id="order_'.$prder["order_id"].'">';
            foreach($order["products"] as $product){
                $html .= $this->render_prod_static($product,$product["usr_options"]);
            }
        $html .='
            </div>   
            <h3>Gesamtpreis: '.number_format( $order["price"],2,',','').' CHF</h3>
        </fieldset><br/><br/>';
        return $html;
    }

    public function render_user($order){
        $user = $order["user"];
        $adress = array(
                    "Anrede" => $user['nodes']['anrede'],
                    "Vorname" => $user['nodes']['vorname'],
                    "Nachname" => $user['nodes']['nachname'],
                    "Firma" => $user['nodes']['firma'],
                    "Straße" => $user['nodes']['strasse'],
                    "Hausnummer" => $user['nodes']['nr'],
                    "PLZ" => $user['nodes']['plz'],
                    "Ort" => $user['nodes']['ort'],
                    "zus. Info" => $user['nodes']['info'],
                    "Land" => $user['nodes']['land']
                );
                if($user['nodes']['use_del']){
                    $del_adress = array(
                        "Anrede" => $user['nodes']['del_anrede'],
                        "Vorname" => $user['nodes']['del_vorname'],
                        "Nachname" => $user['nodes']['del_nachname'],
                        "Firma" => $user['nodes']['del_firma'],
                        "Straße" => $user['nodes']['del_strasse'],
                        "Hausnummer" => $user['nodes']['del_nr'],
                        "PLZ" => $user['nodes']['del_plz'],
                        "Ort" => $user['nodes']['del_ort'],
                        "zus. Info" => $user['nodes']['del_info'],
                        "Land" => $user['nodes']['del_land']
                    );
                    $html .= '
                    <div style="width:50%; float:left;">
                    <h3>Rechnungsadresse</h3>
                    '.$this->generate_adress($adress).'
                    </div><div style="width:50%; float:left;">
                    <h3>Lieferadresse</h3>
                    '.$this->generate_adress($del_adress).'
                    </div>';
                }
                else{
                    $html .= '
                    <h3>Rechnungs- und Lieferadresse</h3>
                    '.$this->generate_adress($adress);
                }
        return $html;
    }

    public function get_orders_by_user($user_id){
        $order_ids = $this->db->select_single("SELECT id FROM `wf_order_details` WHERE user = '".$user_id."' ORDER BY `date` DESC;"); 
        $orders = array();
        foreach ($order_ids as $order){
            $orders[] = $this->get_order($order);
        }
        return $orders;
    }
}
?>