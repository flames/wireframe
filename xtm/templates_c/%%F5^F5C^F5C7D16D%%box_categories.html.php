<?php /* Smarty version 2.6.26, created on 2012-09-29 17:00:01
         compiled from xtc5/boxes/box_categories.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'xtc5/boxes/box_categories.html', 1, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['language'])."/lang_".($this->_tpl_vars['language']).".conf",'section' => 'boxes'), $this);?>

<h2 class="categoryheader"><?php echo $this->_config[0]['vars']['heading_categories']; ?>
</h2>
<ul id="categorymenu"><?php echo $this->_tpl_vars['BOX_CONTENT']; ?>
</ul>