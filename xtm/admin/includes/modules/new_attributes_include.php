<?php
/* --------------------------------------------------------------
   $Id: new_attributes_include.php 2891 2012-05-18 18:54:35Z web28 $

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes_functions); www.oscommerce.com
   (c) 2003 nextcommerce (new_attributes_include.php,v 1.11 2003/08/21); www.nextcommerce.org
   (c) 2006 XT-Commerce

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b        Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com

   Released under the GNU General Public License
   --------------------------------------------------------------*/
   defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

   // include needed functions
   require_once(DIR_FS_INC .'xtc_get_tax_rate.inc.php');
   require_once(DIR_FS_INC .'xtc_get_tax_class_id.inc.php');
   require(DIR_FS_CATALOG.DIR_WS_CLASSES . 'xtcPrice.php');
   $xtPrice = new xtcPrice(DEFAULT_CURRENCY,$_SESSION['customers_status']['customers_status_id']);

// BOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
   if ($_GET['option_order_by']) {
     $option_order_by = $_GET['option_order_by'];
     $_POST['current_product_id'] = $_GET['current_product_id'];
   } else {
     $option_order_by = 'products_options_id';
   }
// EOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
?>
  <script type="text/javascript"><!--
  function go_option() {
    if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") {
      location = "<?php echo xtc_href_link(FILENAME_NEW_ATTRIBUTES, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1)).'&current_product_id='. $_POST['current_product_id']; ?>&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
    }
  }
  //--></script>
  <tr>
    <td class="pageHeading" colspan="3"><?php echo $pageTitle; ?></td>
  </tr>
  <tr><td class="main" colspan="3"><?php echo SORT_ORDER; ?>
  <form name="option_order_by" action="<?php echo FILENAME_NEW_ATTRIBUTES ?>">
  <select name="selected" onChange="go_option()">
  <option value="products_options_id"<?php if ($option_order_by == 'products_options_id') { echo ' SELECTED'; } ?>>
  <?php echo TEXT_OPTION_ID; ?></option>
  <option value="products_options_name"<?php if ($option_order_by == 'products_options_name') { echo ' SELECTED'; } ?>>
  <?php echo TEXT_OPTION_NAME; ?></option>
  <option value="products_options_sortorder"<?php if ($option_order_by == 'products_options_sortorder') { echo ' SELECTED'; } ?>>
  <?php echo TEXT_SORTORDER; ?></option>
  </select>
  </form>
  <br>
  <?php echo xtc_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5'); ?>
  </td></tr>
<form action="<?php echo FILENAME_NEW_ATTRIBUTES; ?>" method="post" name="SUBMIT_ATTRIBUTES" enctype="multipart/form-data"><input type="hidden" name="current_product_id" value="<?php echo $_POST['current_product_id']; ?>"><input type="hidden" name="action" value="change">
<?php
echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());

//BOF - web28 - 2010-12-14 - NEW edit products attributes
echo '<input type="hidden" name="products_options_id" value="' . $products_options_id . '">';
echo '<input type="hidden" name="option_order_by" value="' . $option_order_by . '">';
$_POST['cpath'] = isset($_GET['cpath']) ? $_GET['cpath'] : $_POST['cpath'];
if ($_POST['cpath'] != '') {
  $param ='cPath='. $_POST['cpath'] . '&current_product_id='. $_POST['current_product_id'];  
  echo '<input type="hidden" name="cpath" value="' . $_POST['cpath'] . '">';
} else {
  $param = '';
}
//EOF - web28 - 2010-12-14 - NEW edit products attributes

  require(DIR_WS_MODULES . 'new_attributes_functions.php');

  // Lets get all of the possible options
  // BOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION
  $query = "SELECT *
               FROM ".TABLE_PRODUCTS_OPTIONS."
               where products_options_id LIKE '%'
               AND language_id = '" . $_SESSION['languages_id'] . "'
               order by ". $option_order_by;
  // EOF - Tomcraft - 2009-11-11 - NEW SORT SELECTION

  $result = xtc_db_query($query);
  $matches = xtc_db_num_rows($result);

  if ($matches) {
    while ($line = xtc_db_fetch_array($result)) {
      $current_product_option_name = $line['products_options_name'];
      $current_product_option_id = $line['products_options_id'];
      // Print the Option Name
      echo '<tr class="dataTableHeadingRow">';
      echo '<td class="dataTableHeadingContent"><strong>' . $current_product_option_name . '</strong></td>';
      echo '<td class="dataTableHeadingContent"><strong>'.SORT_ORDER.'</strong></td>';
      echo '<td class="dataTableHeadingContent"><strong>'.ATTR_MODEL.'</strong></td>';
      echo '<td class="dataTableHeadingContent"><strong>'.ATTR_STOCK.'</strong></td>';
      echo '<td colspan="2" class="dataTableHeadingContent"><strong>'.ATTR_WEIGHT.'</strong></td>';
      //echo '<td class="dataTableHeadingContent"><strong>'.ATTR_PREFIXWEIGHT.'</strong></td>';
      echo '<td colspan="2" class="dataTableHeadingContent"><strong>'.ATTR_PRICE.'</strong></td>';
      //echo '<td class="dataTableHeadingContent"><strong>'.ATTR_PREFIXPRICE.'</strong></td>';
      echo "</tr>";

      // Find all of the Current Option's Available Values      
      $query2 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE products_options_id = '" . $current_product_option_id . "' ORDER BY products_options_values_id ASC"; //Tomcraft - 2009-11-11 - CHANGE DESC TO ASC
      $result2 = xtc_db_query($query2);
      $matches2 = xtc_db_num_rows($result2);

      if ($matches2) {
        $i = '0';
        while ($line = xtc_db_fetch_array($result2)) {
          $i++;
          $rowClass = rowClass($i);
          $current_value_id = $line['products_options_values_id'];
          $isSelected = checkAttribute($current_value_id, $_POST['current_product_id'], $current_product_option_id);
          if ($isSelected) {
            $CHECKED = ' CHECKED';
          } else {
            $CHECKED = '';
          }

          $query3 = "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." WHERE products_options_values_id = '" . $current_value_id . "' AND language_id = '" . $_SESSION['languages_id'] . "'";
          $result3 = xtc_db_query($query3);
          while($line = xtc_db_fetch_array($result3)) {
            $current_value_name = $line['products_options_values_name'];
            // Print the Current Value Name
            echo '<tr class="' . $rowClass . '">';
            echo '<td class="main">';
            echo '<input type="checkbox" name="optionValues[]" value="' . $current_value_id . '"' . $CHECKED . '>&nbsp;&nbsp;' . $current_value_name . '&nbsp;&nbsp;';
            echo '</td>';
            echo '<td class="main" align="left"><input type="text" name="' . $current_value_id . '_sortorder" value="' . $sortorder . '" size="4"></td>';
            echo '<td class="main" align="left"><input type="text" name="' . $current_value_id . '_model" value="' . $attribute_value_model . '" size="15"></td>';
            echo '<td class="main" align="left"><input type="text" name="' . $current_value_id . '_stock" value="' . $attribute_value_stock . '" size="10"></td>';
            echo '<td width="1%" class="main" align="left"><SELECT name="' . $current_value_id . '_weight_prefix"><OPTION value="+"' . $posCheck_weight . '>+<OPTION value="-"' . $negCheck_weight . '>-</SELECT></td>';
            echo '<td width="10%" class="main" align="left"><input type="text" name="' . $current_value_id . '_weight" value="' . $attribute_value_weight . '" size="10"></td>';
            
            // brutto Admin
            if (PRICE_IS_BRUTTO=='true'){
              $attribute_value_price_calculate = $xtPrice->xtcFormat(xtc_round($attribute_value_price*((100+(xtc_get_tax_rate(xtc_get_tax_class_id($_POST['current_product_id']))))/100),PRICE_PRECISION),false);
            } else {
              $attribute_value_price_calculate = xtc_round($attribute_value_price,PRICE_PRECISION);
            }
            echo '<td width="1%" class="main" align="left"><SELECT name="' . $current_value_id . '_prefix"> <OPTION value="+"' . $posCheck . '>+<OPTION value="-"' . $negCheck . '>-</SELECT></td>';
            echo '<td width="10%" class="main" align="left"><input type="text" name="' . $current_value_id . '_price" value="' . $attribute_value_price_calculate . '" size="10">';
            // brutto Admin
            if (PRICE_IS_BRUTTO=='true'){
               echo TEXT_NETTO .'<strong>'.$xtPrice->xtcFormat(xtc_round($attribute_value_price,PRICE_PRECISION),true).'</strong>  ';
            }

            echo '</td>';
            
            echo '</tr>';
            // Download function start
            if(strtoupper($current_product_option_name) == 'DOWNLOADS') {
              echo "<tr>";
             // echo '<td colspan="2">File: <input type="file" name="' . $current_value_id . "_download_file"></td>';
              echo '<td colspan="2">'.xtc_draw_pull_down_menu($current_value_id . '_download_file', xtc_getDownloads(), $attribute_value_download_filename, '').'</td>';
              echo '<td class="main">&nbsp;'.DL_COUNT.' <input type="text" name="' . $current_value_id . '_download_count" value="' . $attribute_value_download_count . '"></td>';
              echo '<td class="main">&nbsp;'.DL_EXPIRE.' <input type="text" name="' . $current_value_id . '_download_expire" value="' . $attribute_value_download_expire . '"></td>';
              echo "</tr>";
            }
            // Download function end
          }
          if ($i == $matches2 ) $i = '0';
        }
      } else {
        echo "<tr>";
        echo '<td class="main"><small>No values under this option.</small></td>';
        echo "</tr>";
      }
    }
  }
?>
  <tr>
    <td colspan="10" class="main"><br />
<?php
echo xtc_button(BUTTON_SAVE) . '&nbsp;';
echo xtc_button_link(BUTTON_BACK, xtc_href_link(FILENAME_NEW_ATTRIBUTES, $param));
?>
</td>
  </tr>
</form>