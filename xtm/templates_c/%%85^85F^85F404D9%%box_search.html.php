<?php /* Smarty version 2.6.26, created on 2012-09-29 17:00:01
         compiled from xtc5/boxes/box_search.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'xtc5/boxes/box_search.html', 1, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['language'])."/lang_".($this->_tpl_vars['language']).".conf",'section' => 'boxes'), $this);?>

<?php echo $this->_tpl_vars['FORM_ACTION']; ?>

<table border="0" class="search_header" cellspacing="0" cellpadding="0">
     <tr>
          <td colspan="2"><strong><?php echo $this->_config[0]['vars']['heading_search']; ?>
:</strong></td>
     </tr>
     <tr>
          <td><?php echo $this->_tpl_vars['INPUT_SEARCH']; ?>
</td>
          <td><?php echo $this->_tpl_vars['BUTTON_SUBMIT']; ?>
</td>
     </tr>
     <tr>
          <td colspan="2"><a href="<?php echo $this->_tpl_vars['LINK_ADVANCED']; ?>
"><?php echo $this->_config[0]['vars']['text_advanced_search']; ?>
</a></td>
     </tr>
</table>
<?php echo $this->_tpl_vars['FORM_END']; ?>
 