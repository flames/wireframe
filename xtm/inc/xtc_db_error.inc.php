<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_db_error.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(database.php,v 1.19 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (xtc_db_error.inc.php,v 1.4 2003/08/19); www.nextcommerce.org 

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/
   
  function xtc_db_error($query, $errno, $error) { 
  
    // Deliver 503 Error on database error (so crawlers won't index the error page)
    if (!defined('DIR_FS_ADMIN')) {
      header("HTTP/1.1 503 Service Temporarily Unavailable");
      header("Status: 503 Service Temporarily Unavailable");
      header("Connection: Close");
    }
    
    if (isset($_SESSION['customers_status']['customers_status_id']) && $_SESSION['customers_status']['customers_status_id'] == 0) {
      die('<font color="#000000"><strong>' . $errno . ' - ' . $error . '<br /><br />' . $query . '<br /><br /><small><font color="#ff0000">[XT SQL Error]</font></small><br /><br /></strong></font>');
    } else {
      die('<font color="#ff0000"><strong>Es ist ein Fehler aufgetreten!<br />There was an error!<br />Il y avait une erreur!</strong></font>');
    }
  }
 ?>