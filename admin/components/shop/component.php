<?php
function get_installed_modules($path,$installedModules = FALSE,$submodules = FALSE){
    global $URL_ROOT;
    global $DIR_ROOT;
    global $permissions;
    $comp_path = "components/shop";
    if ($installedModules){ 
        if ($handle = opendir($comp_path."/modules/".$path)){
            while (false !== ($file = readdir($handle))) {
                if (preg_match("^\.{1,2}^",$file)) continue;
                if (is_dir($comp_path."/modules/".$path."/".$file) && isset($installedModules[$file]) && $installedModules[$file]) {
                    $modules_temp = parse_ini_file($comp_path."/modules/".$path."/".$file."/modul.ini",TRUE);
                    if ($permissions->hasPermission($modules_temp["permission"])){
                        $modules[$file] = $modules_temp;
                        $modules[$file]["url"] = $URL_ROOT."admin/shop".$path."/".$file."/";
                        $modules[$file]["path"] = $path."/".$file."/";
                        if(isset($modules[$file]["subs"])){
                            $modules[$file]["subs"] = get_installed_modules($path."/".$file,FALSE,$modules[$file]["subs"]);
                        }
                    }
                }
            }
        }
    }

    else{
        foreach($submodules as $sub){
            if (is_dir($comp_path."/modules".$path."/".$sub)) {
                $modules_temp = parse_ini_file($$comp_path."/modules".$path."/".$sub."/modul.ini",TRUE);
                if ($permissions->hasPermission($modules_temp["permission"])){
                    $modules[$sub] = $modules_temp;
                    $modules[$sub] = parse_ini_file($comp_path."/modules".$path."/".$sub."/modul.ini",TRUE);
                    $modules[$sub]["url"] = $URL_ROOT."admin/shop".$path."/".$sub."/";
                    $modules[$sub]["path"] = $path."/".$sub."/";
                    if(isset($modules[$sub]["subs"])){
                        $modules[$sub]["subs"] = get_installed_modules($path."/".$sub);
                    }
                }
            }
        }
    }
    return $modules;
}

function make_list($modules,$active_module, $parent = FALSE){
    global $URL_ROOT;
    global $REGISTRY;

    foreach ($modules as $key => $modul){
        $modul_name = $modul["name"];
        
        $is_active = FALSE;
        foreach ($active_module as $active){
            if ($active == $modul["name"]) $is_active = TRUE;
        }
        if (!$modul["icon"]) $modul["icon"] = "chevron-right";
        if ($modul["type"] != "navigation") {
            $html .= '
            <li class="';
            if (!$parent) $html.='nav-header '.$parent;
            if ($is_active) $html .= 'active';
            $html .= '"><a href="'.$modul["url"].'"';
            if (!$is_active) $html .= ' class="inactive"';
            $html .= '>';
            $html.='<i  style="margin-top:-2px;" class="icon-'.$modul["icon"];
            if ($is_active) $html .= ' icon-white';
            else $html .= ' icon-blue';
            $html .= '"></i>';
            $html .= $modul["display_name_".$REGISTRY["lang"]["language"]["shortname"]].'</a></li>
            ';
        }
        else {
            $html .= '
            <li class="nav-header ';
            if ($is_active) $html .= 'active';
            $html .= '"><a class="parent_nav';
            if (!$is_active) $html .= ' inactive';
            $html .= '" href="'.$modul["url"].'"><i class="icon-'.$modul["icon"];
            if ($is_active) $html .= ' icon-white';
            else $html .= ' icon-blue';
            $html .= '"></i>';
            $html .= $modul["display_name_".$REGISTRY["lang"]["language"]["shortname"]].'</a>';
            if (isset($modul["subs"])){
                $html .= '<ul class="nav-list ';
                if ($is_active) $html .= 'active';
                $html .= '">'.make_list($modul["subs"],$active_module, $modul["name"]).'</ul>';
            }
            $html .= '</li>
        ';
        }
    }

    return $html;
}
    $installedModules = $DB->select_pair("modules","name","active","order",false, "component = 2");
    $modules_temp = get_installed_modules("",$installedModules);
    foreach ($installedModules as $module_sort => $status){
        if (isset ($modules_temp[$module_sort])) 
            $modules[$module_sort] = $modules_temp[$module_sort];
    }    
    unset($content[0], $content[1]);
    $active_module =  array_values($content);
    $breadcrump = '<li><a href="'.$URL_ROOT.'admin/shop/">Shop</a></li>';
    $modul_temp = $modules;
    $first = TRUE;
        if (count($active_module) > 0 && isset($modul_temp[$active_module[0]]["display_name_".$REGISTRY["lang"]["language"]["shortname"]])) $breadcrump .= '<li>&nbsp;&gt;&nbsp;<a href="'.$modul_temp[$active_module[0]]["url"].'">'.$modul_temp[$active_module[0]]["display_name_".$REGISTRY["lang"]["language"]["shortname"]].'</a></li>';
        if (count($active_module) == 1) $modul_temp = $modul_temp[$active_module[0]];
        else{
            foreach ($active_module as $active_modul){
            if (!$first){
                $modul_temp =  $modul_temp[$last]["subs"][$active_modul];
                $breadcrump .= '<li>&nbsp;&gt;&nbsp;<a href="'.$modul_temp["url"].'">'.$modul_temp["display_name_".$REGISTRY["lang"]["language"]["shortname"]].'</a></li>';
            }
            $last = $active_modul;
            $first = FALSE;
            }
        }
    unset($active_modul);
    if ($modul_temp["file"] != "" && $modul_temp["path"] != ""){
        $active_modul = "components/shop/modules".$modul_temp["path"].$modul_temp["file"];
        if($_GET["edit"]) $active_modul .= "_edit";
        $active_modul .= ".php";
    }
?>