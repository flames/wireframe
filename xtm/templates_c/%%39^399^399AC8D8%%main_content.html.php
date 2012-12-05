<?php /* Smarty version 2.6.26, created on 2012-09-29 17:00:02
         compiled from xtc5/module/main_content.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'xtc5/module/main_content.html', 1, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['language'])."/lang_".($this->_tpl_vars['language']).".conf",'section' => 'index'), $this);?>

<?php echo $this->_tpl_vars['MODULE_error']; ?>

<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>
<div><?php echo $this->_tpl_vars['text']; ?>
</div>
<?php if ($this->_tpl_vars['MODULE_new_products']): ?>
<div><?php echo $this->_tpl_vars['MODULE_new_products']; ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['MODULE_upcoming_products']): ?>
<div><?php echo $this->_tpl_vars['MODULE_upcoming_products']; ?>
</div>
<?php endif; ?>