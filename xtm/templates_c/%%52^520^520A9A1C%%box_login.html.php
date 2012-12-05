<?php /* Smarty version 2.6.26, created on 2012-09-29 17:00:01
         compiled from xtc5/boxes/box_login.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'xtc5/boxes/box_login.html', 1, false),)), $this); ?>
<?php echo smarty_function_config_load(array('file' => ($this->_tpl_vars['language'])."/lang_".($this->_tpl_vars['language']).".conf",'section' => 'boxes'), $this);?>

<h2 class="boxheader"><?php echo $this->_config[0]['vars']['heading_login']; ?>
</h2>
<div class="boxbody"> <?php echo $this->_tpl_vars['FORM_ACTION']; ?>

  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="2"><?php echo $this->_config[0]['vars']['text_email']; ?>
:</td>
    </tr>
    <tr>
      <td colspan="2"><?php echo $this->_tpl_vars['FIELD_EMAIL']; ?>
</td>
    </tr>
    <tr>
      <td colspan="2"><?php echo $this->_config[0]['vars']['text_pwd']; ?>
:</td>
    </tr>
    <tr>
      <td><?php echo $this->_tpl_vars['FIELD_PWD']; ?>
</td>
      <td><?php echo $this->_tpl_vars['BUTTON']; ?>
</td>
    </tr>
    <tr>
      <td colspan="2"><div class="hr"></div>
        <a href="<?php echo $this->_tpl_vars['LINK_LOST_PASSWORD']; ?>
"><?php echo $this->_config[0]['vars']['text_password_forgotten']; ?>
</a></td>
    </tr>
  </table>
  <?php echo $this->_tpl_vars['FORM_END']; ?>
 </div>