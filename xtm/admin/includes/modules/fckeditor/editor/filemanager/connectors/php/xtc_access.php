<?php
/*
FCKEditor Filemanger xtc_access v.0.94(c) 2012 by web28 - www.rpa-com.de
*/
//require_once('../../../../../../configure.php');
require_once(DIR_FS_INC . 'xtc_db_connect.inc.php');

xtc_db_connect();// or die('Unable to connect to database server!');

$Config['Enabled'] = false;

$secure_id = $_GET['sid'];
if (!empty($secure_id)) {
  $secure_id = mysql_real_escape_string($secure_id);
  $secure_id = strip_tags($secure_id);
  $result = mysql_query('
                        SELECT value
                          FROM sessions s
                         WHERE s.sesskey = "'. $secure_id .'"
                           LIMIT 1
                        ');

  if(mysql_num_rows($result) > 0) {
    $val_array = mysql_fetch_array($result);
    $val = $val_array['value'];
    $variables = array();
    $a = preg_split( "/(\w+)\|/", $val, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
    for( $i = 0; $i < count( $a ); $i = $i+2 ) {
      $variables[$a[$i]] = unserialize( $a[$i+1] );
    }
    if (isset($variables['customers_status']['customers_status_id']) && $variables['customers_status']['customers_status_id'] == 0) {
      $Config['Enabled'] = true;
    }
  }
}

?>