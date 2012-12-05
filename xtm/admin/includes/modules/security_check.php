<?php
/* --------------------------------------------------------------
   $Id: security_check.php 2005 2011-06-08 15:48:45Z dokuman $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   (c) 2003 nextcommerce (security_check.php,v 1.2 2003/08/23); www.nextcommerce.org
   (c) 2006 xt-commerce (security_check.php 1221 2005-09-20); www.xt-commerce.com

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$file_warning = '';
$folder_warning = '';

//BOF - 2011-06-08 - DokuMan - Change the way, PHP checks for file permissions - files must not be writeable
if (is_writeable(DIR_FS_CATALOG.'includes/configure.php')) {
  $file_warning .= '<br />'.DIR_FS_CATALOG.'includes/configure.php';
}

if (is_writeable(DIR_FS_CATALOG.'includes/configure.org.php')) {
  $file_warning .= '<br />'.DIR_FS_CATALOG.'includes/configure.org.php';
}

if (is_writeable(DIR_FS_ADMIN.'includes/configure.php')) {
  $file_warning .= '<br />'.DIR_FS_ADMIN.'includes/configure.php';
}

if (is_writeable(DIR_FS_ADMIN.'includes/configure.org.php')) {
  $file_warning .= '<br />'.DIR_FS_ADMIN.'includes/configure.org.php';
}

//EOF - 2011-06-08 - DokuMan - Change the way, PHP checks for file permissions - files must not be writeable

// BOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
//if ($file_warning != '' or $folder_warning != '') {
$payment_query = xtc_db_query("SELECT *
                               FROM ".TABLE_CONFIGURATION."
                               WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
while ($payment_data = xtc_db_fetch_array($payment_query)) {
  $installed_payment = $payment_data['configuration_value'];
}

$shipping_query = xtc_db_query("SELECT *
                                FROM ".TABLE_CONFIGURATION."
                                WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
while ($shipping_data = xtc_db_fetch_array($shipping_query)) {
  $installed_shipping = $shipping_data['configuration_value'];
}

if ($file_warning != '' or $folder_warning != '' or $installed_payment == '' or $installed_shipping == '') {
// EOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
?>

<table style="border: 1px solid; border-color: #ff0000;" bgcolor="fdac00" border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<div class="main">
        <table width="100%" border="0">
          <tr>
            <td width="1"><?php echo xtc_image(DIR_WS_ICONS.'big_warning.gif', ICON_BIG_WARNING); ?></td>
            <td class="main">
<?php
// BOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
  if ($file_warning != '') {
    echo TEXT_FILE_WARNING;
    echo '<strong>'.$file_warning.'</strong><br />';
  }

  if ($folder_warning != '') {
    echo TEXT_FOLDER_WARNING;
    echo '<strong>'.$folder_warning.'</strong><br />';
  }

  if ($installed_payment == '') {
    echo TEXT_PAYMENT_ERROR.'<br />';
  }

  if ($installed_shipping == '') {
    echo TEXT_SHIPPING_ERROR;
  }
// EOF - Tomcraft - 2010-05-25 - Fixed display of error messages from security_check.php in admin area when file and folder-permissions are set correctly
?>
            </td>
          </tr>
        </table>
      </div>
</td>
</tr>
</table>
<?php
}
?>