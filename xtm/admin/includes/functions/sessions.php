<?php
/* --------------------------------------------------------------
   $Id: sessions.php 950 2005-05-14 16:45:21Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(sessions.php,v 1.16 2003/04/02); www.oscommerce.com
   (c) 2003	 nextcommerce (sessions.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

  if (!defined('SESSION_LIFE_ADMIN')) {
    define('SESSION_LIFE_ADMIN', '7200'); //120 Minuten
  }

  if (STORE_SESSIONS == 'mysql') {
    //BOF - web28 - 2010-07-20 - change admin login timeout
    //if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      //$SESS_LIFE = 1440;
    //}
    $SESS_LIFE = (int)SESSION_LIFE_ADMIN;
    //EOF - web28 - 2010-07-20 - change admin login timeout

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $qid = xtc_db_query("select value from " . TABLE_SESSIONS . " where sesskey = '" . $key . "' and expiry > '" . time() . "'");

      $value = xtc_db_fetch_array($qid);
      if ($value['value']) {
        return $value['value'];
      }

      return false;
    }

    function _sess_write($key, $val) {
      global $SESS_LIFE;

      $expiry = time() + $SESS_LIFE;
      $value = addslashes($val);

      $qid = xtc_db_query("select count(*) as total from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
      $total = xtc_db_fetch_array($qid);

      if ($total['total'] > 0) {
        return xtc_db_query("update " . TABLE_SESSIONS . " set expiry = '" . $expiry . "', value = '" . $value . "' where sesskey = '" . $key . "'");
      } else {
        return xtc_db_query("insert into " . TABLE_SESSIONS . " values ('" . $key . "', '" . $expiry . "', '" . $value . "')");
      }
    }

    function _sess_destroy($key) {
      return xtc_db_query("delete from " . TABLE_SESSIONS . " where sesskey = '" . $key . "'");
    }

    function _sess_gc($maxlifetime) {
      xtc_db_query("delete from " . TABLE_SESSIONS . " where expiry < '" . time() . "'");

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
    register_shutdown_function('session_write_close'); //DokuMan - 2010-01-28 - fix for procedural mysqli function
  }

  function xtc_session_start() {
    return session_start();
  }
// BOF - Hetfield - 2009-08-19 - removed deprecated function session_register to be ready for PHP >= 5.3
/*
function xtc_session_register($variable) {
    global $session_started;

    if ($session_started == true) {
      return session_register($variable);
    }
  }
*/
// EOF - Hetfield - 2009-08-19 - removed deprecated function session_register to be ready for PHP >= 5.3
// BOF - Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
/*
  function xtc_session_is_registered($variable) {
    return session_is_registered($variable);
  }
*/
// EOF - Hetfield - 2009-08-19 - removed deprecated function session_is_registered to be ready for PHP >= 5.3
// BOF - Hetfield - 2009-08-19 - removed deprecated function xtc_session_unregister to be ready for PHP >= 5.3
/*
  function xtc_session_unregister($variable) {
    return session_unregister($variable);
  }
*/
// EOF - Hetfield - 2009-08-19 - removed deprecated function xtc_session_unregister to be ready for PHP >= 5.3
  function xtc_session_id($sessid = '') {
    if (!empty($sessid)) {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function xtc_session_name($name = '') {
    if (!empty($name)) {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function xtc_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function xtc_session_destroy() {
    return session_destroy();
  }

  function xtc_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }

  function xtc_session_recreate() {
    if (PHP_VERSION >= 4.1) {
      $session_backup = $_SESSION;
      unset($_COOKIE[xtc_session_name()]);
      xtc_session_destroy();
      if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
        register_shutdown_function('session_write_close'); //DokuMan - 2010-01-28 - fix for procedural mysqli function
      }
      xtc_session_start();
      $_SESSION = $session_backup;
      unset($session_backup);
    }
  }
?>