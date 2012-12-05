<script type="text/javascript">
Shadowbox.init();
</script>
<div id="site">
<div id="header" class="well">
    <div id="head_left">
       <h1> Wireframe </h1>
    </div>
      <?php
        $active_components = $DB->select_pair("components","name","active","order",FALSE, "active = 1" );
        foreach($active_components as $file => $active){
                if (is_dir($DIR_ROOT."/admin/components/".$file) && isset($active_components[$file])) {
                    $component = parse_ini_file($DIR_ROOT."/admin/components/".$file."/component.ini",TRUE);
                    if ($permissions->hasPermission($component["permission"])){
                        $components[$file] = $component;
                        $components[$file]["url"] = $URL_ROOT."admin/".$file."/";
                        $components[$file]["path"] = "components/".$file."/";
                    }
                }
        }
        echo '<div id="head_middle" style="width:'.count($components) * 80 .'px;"><ul>';
        foreach($components as $key => $component){
            echo '<li><a href="'.$component["url"].'"><img src="'.$URL_ROOT."admin/components/".$key."/".$component["image"].'"/><br/>'.$component["name_de"].'</a></li>';
        }
        echo '</ul><div style="clear:both"></div>';
        if(isset($content[1])) include('components/'.$content[1].'/component.php');
      ?>
    </div>
    <div id="head_right">
      <form id="logout" action="" method="post" style="margin:0;">
        <?php echo "Angemeldet als: "; ?><?php echo $_SESSION["_registry"]["user"]["name"]; ?> (<?php echo $_SESSION["_registry"]["user"]["group"]; ?>)<br />
        <input type="hidden" name="logout" value="logout" /><input type="submit" value="Logout" class="button" style="float:right;" />
        <span id="zeit"></span><br />
        Wireframe Version <?php echo $_SESSION["_registry"]["system_config"]["information"]["version"]; ?><br />
      </form>
    </div>
</div>

<div id="breadcrump">
  <ul class="breadcrumb">
    <li>Sie befinden sich hier: </li>
    <?php echo $breadcrump; ?>
  </ul>
</div>

<div id="content">
    <div id="navigation" class="well">
      <ul class="nav nav-list">
        <?php 
        echo make_list($modules,$active_module); 
        ?>
      </ul>
    </div>
    <div id="main" class="well">
      <?php 
      
      if (isset($active_modul)) include  $active_modul;
      else if(isset($stats)) include $stats;
      else
      echo $LANG["backend"]["welcome"];
      ?>
    </div>
</div>
</div>