<?php
/* -----------------------------------------------------------------------------------------
   $Id: login_admin.php 3021 2012-06-12 14:01:30Z web28 $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2008 Gambio OHG - login_admin.php 2008-08-10 gambio
   Gambio OHG
   http://www.gambio.de
   Copyright (c) 2008 Gambio OHG

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
  // USAGE: /login_admin.php?repair=se_friendly
  // USAGE: /login_admin.php?repair=sess_write
  // USAGE: /login_admin.php?repair=sess_default
  // USAGE: /login_admin.php?repair=xtc5_template

  // USAGE: /login_admin.php?show_error=none
  // USAGE: /login_admin.php?show_error=all
  // USAGE: /login_admin.php?show_error=shop
  // USAGE: /login_admin.php?show_error=admin

  // further documentation, see also:
  // http://www.xtc-modified.org/wiki/Login_in_den_Administrationsbereich_nach_%C3%84nderungen_nicht_mehr_m%C3%B6glich

//BOC web28 parameter validation
$error = false;
//repair
$allwowed_repair_array = array('se_friendly','sess_write','sess_default','xtc5_template');
if (isset($_GET['repair']) && !empty($_GET['repair']) && !in_array($_GET['repair'],$allwowed_repair_array)) {
  $error = true;
}
if (isset($_POST['repair']) && !empty($_POST['repair']) && !in_array($_POST['repair'],$allwowed_repair_array)) {
  $error = true;
}
//show_error
$allowed_show_error_array = array('none','shop','admin','all');
if (isset($_GET['show_error']) && !empty($_GET['show_error']) && !in_array($_GET['show_error'],$allowed_show_error_array)) {
  $error = true;
}
if (isset($_POST['show_error']) && !empty($_POST['show_error']) && !in_array($_POST['show_error'],$allowed_show_error_array)) {
  $error = true;
}
//parameter error
if ($error) {
  unset($_GET['repair']);
  unset($_GET['show_error']);
  unset($_POST['repair']);
  unset($_POST['show_error']);
}
//EOC web28 parameter validation

if(isset($_GET['repair']) || isset($_GET['show_error'])) {
  $action = 'login_admin.php';
} else {
  $action = 'login.php?action=process';
}

if(isset($_POST['repair'])  || isset($_POST['show_error'])) {

  //BOC loading only necessary functions
  // Set the local configuration parameters - mainly for developers or the main-configure
  if (file_exists('includes/local/configure.php')) {
    include('includes/local/configure.php');
  } else {
    require('includes/configure.php');
  }

  require_once(DIR_WS_INCLUDES . 'database_tables.php');
  require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_close.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_error.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_query.inc.php');
  require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_fetch_array.inc.php');
  require_once(DIR_FS_INC . 'xtc_db_input.inc.php');
  require_once(DIR_FS_INC . 'xtc_validate_password.inc.php');
  require_once(DIR_WS_CLASSES.'class.inputfilter.php');
  //EOC loading only necessary functions

  xtc_db_connect() or die('Unable to connect to database server!');

  //$_POST security
  $InputFilter = new InputFilter();
  $_POST = $InputFilter->process($_POST);
  $_POST = $InputFilter->safeSQL($_POST);

  $check_customer_query = xtc_db_query('
                                       SELECT customers_id,
                                              customers_password,
                                              customers_email_address
                                         FROM '. TABLE_CUSTOMERS .'
                                        WHERE customers_email_address = "'. xtc_db_input($_POST['email_address']) .'"
                                          AND customers_status = 0');

  $check_customer = xtc_db_fetch_array($check_customer_query);
  if(!xtc_validate_password(xtc_db_input($_POST['password']),
                            $check_customer['customers_password'],
                            $check_customer['customers_email_address'])) {
    die('Zugriff verweigert.');
  } else {
    if (xtc_not_null($_POST['repair'])) {
      //repair
      switch($_POST['repair']) {
        case 'se_friendly':
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "false"
            WHERE  configuration_key   = "SEARCH_ENGINE_FRIENDLY_URLS"
          ');
          die('Report: Die Einstellung "Suchmaschinenfreundliche URLs verwenden" wurde deaktiviert.');
          break;

        case 'sess_write':
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "'.DIR_FS_CATALOG.'cache"
            WHERE  configuration_key   = "SESSION_WRITE_DIRECTORY"
          ');
          die('Report: SESSION_WRITE_DIRECTORY wurde auf das Cache-Verzeichnis gerichtet.');
          break;

        case 'sess_default':
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "False"
            WHERE  configuration_key   = "SESSION_FORCE_COOKIE_USE"
          ');
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "False"
            WHERE  configuration_key   = "SESSION_CHECK_SSL_SESSION_ID"
          ');
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "False"
            WHERE  configuration_key   = "SESSION_CHECK_USER_AGENT"
          ');
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "False"
            WHERE  configuration_key   = "SESSION_CHECK_IP_ADDRESS"
          ');
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "False"
            WHERE  configuration_key   = "SESSION_RECREATE"
          ');
          die('Report: Die Session-Einstellungen wurden auf die Standardwerte zurückgesetzt.');
          break;

        case 'xtc5_template':
          xtc_db_query('
            UPDATE configuration
            SET    configuration_value = "xtc5"
            WHERE  configuration_key = "CURRENT_TEMPLATE"
          ');
          die('Report: CURRENT_TEMPLATE wurde auf das xtc5-Standardtemplate zurückgesetzt.');
          break;

        default:
          die('Report: repair-Befehl ungültig.');
      }
    }
    //error_reporting
    if (xtc_not_null($_POST['show_error'])) {

      $error_type = DIR_FS_DOCUMENT_ROOT . 'export/_error_reporting.' . $_POST['show_error'];
      $filenames = scandir(DIR_FS_DOCUMENT_ROOT . 'export/');
      foreach ($filenames as $filename) {
        if (strpos($filename, '_error_reporting')!== false) {
          $actual_reporting = $filename;
        }
      }
      if ($actual_reporting) {
        rename(DIR_FS_DOCUMENT_ROOT . 'export/'.$actual_reporting, $error_type);
        die('Report: error_reporting wurde ge&auml;ndert auf: '. $_POST['show_error']);
      } else {
        $errorHandle = fopen($error_type, 'w') or die('Report: error_reporting kann nicht ver&auml;ndert werden. ('. $_POST['show_error'].')');
        fclose($errorHandle);
        die('Report: error_reporting wurde ge&auml;ndert auf: '. $_POST['show_error']);
      }
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-15" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<title>Admin-Login</title>
<meta http-equiv="content-language" content="de" />
<meta http-equiv="cache-control" content="no-cache" />
</head>
<body>
<br/><br/>
<form name="login" method="post" action="<?php echo $action; ?>">
  <table border="0" align="center" cellpadding="5" cellspacing="0" bgcolor="#F0F0F0" style="border:1px #aaaaaa solid;">
    <tr>
      <td class="main"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">E-Mail</font></td>
      <td><div><input type="text" name="email_address" style="width:150px" maxlength="50" /></div></td>
    </tr>
    <tr>
      <td class="main"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Passwort</font>&nbsp;</td>
      <td><div><input type="password" name="password" style="width:150px" maxlength="30" /></div></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit" name="Submit" value="Anmelden" />
      <input type="hidden" name="repair" value="<?php if(isset($_GET['repair'])){ echo $_GET['repair']; } ?>" />
      <input type="hidden" name="show_error" value="<?php if(isset($_GET['show_error'])){ echo $_GET['show_error']; }?>" /></td>
    </tr>
  </table>
</form>
    <p style="text-align: center"><a style="font-family: Verdana, Geneva, Arial, Helvetica, sans-serif; font-size:12px;color: #893769" href="http://www.xtc-modified.org/wiki/Login_in_den_Administrationsbereich_nach_%C3%84nderungen_nicht_mehr_m%C3%B6glich" target="_blank">Hilfe</a></p>
</body>
</html>
