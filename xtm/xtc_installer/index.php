<?php
  /* --------------------------------------------------------------
   $Id: index.php 1220 2005-09-16 15:53:13Z mz $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   (c) 2006 xt:Commerce (index.php 1220 2005-09-16); www.xtcommerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------*/
   
  define('PHP_VERSION_MIN', '5.0.0');
  define('PHP_VERSION_MAX', '5.3.99');

  require('includes/application.php');

  //BOF  - web28 - 2011-05-19 - SUPPORT
  $support = '&nbsp;';
  if (isset($_GET['support'])) {
    $support  = 'URL: ' . $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']. '<br />';
    $support .= '$_SERVER[PHP_SELF]: ' . $_SERVER['PHP_SELF']. '<br />';
    $support .= '$_SERVER[DOCUMENT_ROOT]: ' . $_SERVER['DOCUMENT_ROOT']. '<br />';
    $support .= '$_SERVER[SCRIPT_FILENAME]: ' . $_SERVER['SCRIPT_FILENAME']. '<br />';
    $support .= 'DIR_FS_DOCUMENT_ROOT: ' . DIR_FS_DOCUMENT_ROOT. '<br />';
  }
  //EOF  - web28 - 2011-05-19 - SUPPORT

  // include needed functions
  require_once(DIR_FS_INC.'xtc_image.inc.php');
  require_once(DIR_FS_INC.'xtc_draw_separator.inc.php');
  require_once(DIR_FS_INC.'xtc_redirect.inc.php');
  require_once(DIR_FS_INC.'xtc_href_link.inc.php');

  //BOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  //include('language/english.php');
  include('language/'.$lang.'.php');
  //BOF - web28 - 2010.02.11 - NEW LANGUAGE HANDLING IN application.php
  define('HTTP_SERVER','');
  define('HTTPS_SERVER','');
  define('DIR_WS_CATALOG','');
  define('DIR_WS_BASE',''); //web28 - 2010-12-13 - FIX for $messageStack icons

  $messageStack = new messageStack();
  $process = false;

  if (isset($_POST['action']) && ($_POST['action'] == 'process')) {
    $process = true;
    $_SESSION['language'] = xtc_db_prepare_input($_POST['LANGUAGE']);
    $error = false;
    if ( ($_SESSION['language'] != 'german') && ($_SESSION['language'] != 'english') ) {
      $error = true;
      $messageStack->add('index', SELECT_LANGUAGE_ERROR);
    }
    if ($error == false) {
      xtc_redirect(xtc_href_link('install_step1.php?lg='. xtc_db_prepare_input($_POST['LANGUAGE']), '', 'NONSSL'));
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>xtcModified Installer</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css">
body { background: #eee; font-family: Arial, sans-serif; font-size: 12px;}
table,td,div { font-family: Arial, sans-serif; font-size: 12px;}
h1 { font-size: 18px; margin: 0; padding: 0; margin-bottom: 10px; }
<!--
.messageStackError, .messageStackWarning { font-family: Verdana, Arial, sans-serif; font-weight: bold; font-size: 10px; background-color: #; }
-->
</style>
</head>
<body>
<table width="800" style="border:30px solid #fff;" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="95" colspan="2" >
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td width="1"><img src="images/logo.gif" alt="" /></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td align="right" valign="top">
      <br />
      <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><img src="images/title_index.gif" width="705" height="180" border="0" alt="" /><br />
            <br /><br /><div style="border:1px solid #ccc; background:#fff; padding:10px;"><?php echo TEXT_WELCOME_INDEX; ?><br /><br /><a href="http://www.xtc-modified.org/spenden" target="_blank"><img src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" alt="<?php echo TEXT_INFO_DONATIONS_IMG_ALT; ?>" border="0" /></a></div><br /><br />
          </td>
        </tr>
<?php
  // file and folder permission checks
  $error_flag = false;
  $folder_flag = false;
  $message='';
  $ok_message='';
  // config files
  if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.php')) {
    $error_flag=true;
    $message .= TEXT_WRONG_FILE_PERMISSION.DIR_FS_CATALOG . 'includes/configure.php<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'includes/configure.org.php')) {
    $error_flag=true;
    $message .= TEXT_WRONG_FILE_PERMISSION.DIR_FS_CATALOG . 'includes/configure.org.php<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.php')) {
    $error_flag=true;
    $message .= TEXT_WRONG_FILE_PERMISSION.DIR_FS_CATALOG . 'admin/includes/configure.php<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/includes/configure.org.php')) {
    $error_flag=true;
    $message .= TEXT_WRONG_FILE_PERMISSION.DIR_FS_CATALOG . 'admin/includes/configure.org.php<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'sitemap.xml')) {
    $error_flag=true;
    $message .= TEXT_WRONG_FILE_PERMISSION .DIR_FS_CATALOG . 'sitemap.xml<br />';
  }
  $status='<strong>OK</strong>';
  if ($error_flag==true)
    $status='<strong><font color="#ff0000">'.TEXT_ERROR.'</font></strong>';
  $ok_message.= TEXT_FILE_PERMISSION_STATUS .'.............................. '.$status.'<br /><hr noshade />';
  // smarty folders
  if (!is_writeable(DIR_FS_CATALOG . 'admin/backups/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'admin/backups/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'admin/images/graphs')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'admin/images/graphs<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'cache/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'cache/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'export/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'export/<br />';
  }
  // image folders
  if (!is_writeable(DIR_FS_CATALOG . 'images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/categories/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/categories/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/banner/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/banner/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/info_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/product_images/info_images/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/original_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/product_images/original_images/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/popup_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/product_images/popup_images/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/product_images/thumbnail_images/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/product_images/thumbnail_images/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'images/manufacturers/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'images/manufacturers/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'import/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'import/<br />';
  }

  if (!is_writeable(DIR_FS_CATALOG . 'media/content/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'media/content/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'media/products/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'media/products/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'media/products/backup/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'media/products/backup/<br />';
  }
  if (!is_writeable(DIR_FS_CATALOG . 'templates_c/')) {
    $error_flag=true;
    $folder_flag=true;
    $message .= TEXT_WRONG_FOLDER_PERMISSION.DIR_FS_CATALOG . 'templates_c/<br />';
  }
  $status='<strong>OK</strong>';
  if ($folder_flag==true)
    $status='<strong><font color="#ff0000">'.TEXT_ERROR.'</font></strong>';
  $ok_message.= TEXT_FOLDER_PERMISSION_STATUS . '.............................. '.$status.'<br /><hr noshade />';
  // check PHP-Version
  $php_flag = false;
  //BOF - Dokuman - 2009-09-02: update PHP-Version check
  if (function_exists('version_compare')) {      
    if(version_compare(phpversion(), PHP_VERSION_MIN, "<")){
      $error_flag = true;
      $php_flag = true;
      $message .= '<strong>ACHTUNG! Ihre PHP-Version ist zu alt. Der Shop setzt mindestens die Version '. PHP_VERSION_MIN .' voraus.<br /><br />
                 Ihre PHP-Version: ' . phpversion() . '</strong>.';
    }
    if(version_compare(phpversion(), PHP_VERSION_MAX, ">")){
      $error_flag = true;
      $php_flag = true;
      $message .= '<strong>ACHTUNG! Ihre PHP-Version ist zu neu. Der Shop funktioniert nur bis Version '. PHP_VERSION_MAX .' einwandfrei.<br /><br />
                 Ihre PHP-Version: ' . phpversion() . '</strong>.';
    }
  } else {
     $error_flag = true;
    $php_flag = true;
    $message .= '<strong>ACHTUNG! Ihre PHP-Version ist zu alt. Der Shop setzt mindestens die Version '. PHP_VERSION_MIN .' voraus.<br /><br />
                 Ihre PHP-Version: ' . phpversion() . '</strong>.';
  }
  //EOF - Dokuman - 2009-09-02: update PHP-Version check

  $status='<strong>OK</strong>';
  if ($php_flag==true)
    $status='<strong><font color="#ff0000">'.TEXT_ERROR.'</font></strong>';
  $ok_message.='PHP VERSION .............................. '.phpversion(). '&nbsp;&nbsp;&nbsp;'.$status.'<br /><hr noshade>';
 $gd=gd_info();
  if ($gd['GD Version']=='')
    $gd['GD Version']='<strong><font color="#ff0000">'.TEXT_ERROR.TEXT_NO_GDLIB_FOUND.'</font></strong>';
  $status= '<strong>'.$gd['GD Version'].'</strong> ('.TEXT_GDLIBV2_SUPPORT.')';
  // display GDlibversion
  $ok_message.='GDlib VERSION .............................. '.$status.'<br /><hr noshade>';
  if ($gd['GIF Read Support']==1 or $gd['GIF Support']==1) {
    $status='OK';
  } else {
    $status='<strong><font color="#ff0000">'.TEXT_ERROR.'</font></strong><br />'.TEXT_GDLIB_MISSING_GIF_SUPPORT;
  }
  $ok_message.= TEXT_GDLIB_GIF_VERSION .' .............................. '.$status.'<br /><hr noshade>';
if ($error_flag==true) {
?>
<tr>
        <td>
<h1><?php echo TEXT_CHMOD_REMARK_HEADLINE; ?>:</h1>
                <div style="background:#fff; padding:10px; border:1px solid #ccc">
                  <?php echo TEXT_CHMOD_REMARK; ?>
                </div><br />
<div style="background:#ff0000; color:#ffffff; padding:10px; border:1px solid #cf0000">
<?php echo $message; ?>
</div>
</td>
</tr>
 <?php } ?>
          <?php if ($ok_message!='') { ?>
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td style="border: 1px solid; border-color: #4CC534; padding:10px;" bgcolor="#C2FFB6">
                <strong><?php echo TEXT_CHECKING; ?>:</strong>
                <br /><br />
                <?php
                  echo $ok_message;
                ?>
              </td>
            </tr>
          <?php } ?>
      </table>
      <p><img src="images/break-el.gif" width="100%" height="1"alt="" /></p>
      <table width="98%" border="0" align="right" cellpadding="0" cellspacing="0">
        <tr>
          <td>
          <strong><?php echo TITLE_SELECT_LANGUAGE; ?></strong><br />
            <img src="images/break-el.gif" width="100%" height="1" alt="" /><br />
            <?php if ($messageStack->size('index') > 0) {?>
              <br />
              <table border="0" cellpadding="0" cellspacing="0" bgcolor="f3f3f3">
                <tr>
                  <td><?php echo $messageStack->output('index'); ?></td>
                </tr>
             </table>
             <?php }?>
             <form name="language" method="post" action="index.php">
              <table width="300" border="0" cellpadding="0" cellspacing="4">
                  <tr>
                    <td width="98"><img src="images/icons/arrow02.gif" width="13" height="6" alt="" />Deutsch</td>
                    <td width="192"><img src="images/icons/icon-deu.gif" width="30" height="16" alt="" />
                      <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'german'); ?>
                    </td>
                  </tr>
                  <tr>
                    <td><img src="images/icons/arrow02.gif" width="13" height="6" alt="" />English</td>
                    <td><img src="images/icons/icon-eng.gif" width="30" height="16" alt="" />
                    <?php echo xtc_draw_radio_field_installer('LANGUAGE', 'english'); ?> </td>
                  </tr>
                </table>
              <?php if ($error_flag==false) { ?>
                 <input type="hidden" name="action" value="process" />
                 <br /><input type="image" src="images/button_continue.gif"> <?php } ?><br />
            </form>
          </td>
        </tr>
      </table>
  </tr>
</table>
<br />
<div align="center" style="font-family:Arial, sans-serif; font-size:11px;"><?php echo '<a href="http://www.xtc-modified.org" target="_blank">xtcModified</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a>'; ?></div>
<div align="center" style="padding-top:5px; font-size:11px;">Installer 105sp1c</div>
<div align="center" style="padding-top:5px; font-size:11px;"><?php echo $support; ?></div>
</body>
</html>