<?php 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of folderant
 *
 * @author Abadon
 */

class folderant{
  protected $db;
  protected $lang;
  protected $config;
  protected $short_tags;
  protected $product_codes;
  protected $pdf2text;

   function __construct(){
        $this->db       =  $_SESSION["_registry"]["db"];
        $this->lang     =  $_SESSION["_registry"]["lang"];
        $this->config   =  $this->db->select_pair("wf_folderant_config","key","value");
        $this->pdf2text = new pdf2text();
    }

   public function get_path($file){
    return str_replace($this->config["source_path"], "", $file["fullpath"]);
  }

   public function get_short_tags(){
      return $this->short_tags;
   }

   public function get_product_codes(){
      return $this->product_codes;
   }

   public function get_langs(){
      return $this->langs;
   }

   public function get_latest(){
      return $this->db->select("SELECT id, name FROM wf_folderant_files ORDER BY time DESC LIMIT 5;");
   }

   public function get_popular(){

      return $this->db->select("SELECT id, name FROM wf_folderant_files ORDER BY downloads DESC LIMIT 5;");
   }

   public function count_files(){
      return $this->db->query_fetch_single("SELECT COUNT(*) FROM wf_folderant_files;");
   }

   public function count_folders(){
      return $this->db->query_fetch_single("SELECT COUNT(*) FROM wf_folderant_folders;");
   }

  public function search($text,$fulltext = FALSE, $tags = FALSE){
      $product_code = $this->db->query_fetch_single("SELECT ProductCode FROM `wf_ctp_articles` WHERE ItemCode LIKE '$text'  OR CodeBars = '$text' LIMIT 1;");
      $product_codes = $this->db->select_single("SELECT CONCAT('\\'',ProductCode,'\\'') AS ProductCode FROM `wf_ctp_products` WHERE ProductName LIKE '%$text%' OR ProductFrgnName LIKE '%$text%' OR ProductName SOUNDS LIKE '$text' OR ProductFrgnName SOUNDS LIKE '$text' OR ProductCode = '$text';");
      $result = array(array(),array(),array());
      if($fulltext) $fulltext_query = "OR  MATCH (name,content) AGAINST ('".$text."')";
      if($tags) $tag_query = "AND short_tag in (".implode(",", $tags).")";
      if($product_code){
         $result[0] = $this->db->select_single("SELECT id FROM wf_folderant_files WHERE prod_num = '$product_code' $tag_query;");
      }
      if($product_codes){
         $result[1] = $this->db->select_single("SELECT id FROM wf_folderant_files WHERE prod_num IN (".implode(",", $product_codes).") $tag_query;");
      }
    $result[2] = $this->db->select_single("SELECT id FROM wf_folderant_files WHERE 
      ((label SOUNDS LIKE '".$text."' AND label != '')
      OR label LIKE '%".$text."%'
      OR name LIKE '%".$text."%'
      OR prod_num LIKE '%".$text."%'
      $fulltext_query)
         $tag_query
      ;");
      $results = array_keys(array_flip(call_user_func_array('array_merge', $result)));
    return $results;
  }

  public function get_file($id){
    ob_clean();
      $this->db->query("UPDATE wf_folderant_files SET downloads = downloads + 1 WHERE id = $id LIMIT 1;");
      $this->db->query("INSERT INTO `wf_folderant_downloads` (
                                                                              `id` ,
                                                                              `date` ,
                                                                              `ip` ,
                                                                              `ref` ,
                                                                              `file`
                                                                            )
                                                                            VALUES (
                                                                              NULL , CURRENT_TIMESTAMP , '".$_SERVER["REMOTE_ADDR"]."', '".$_SERVER["HTTP_REFERER"]."', '$id')");
    $file = $this->db->query_fetch_single("SELECT fullpath FROM wf_folderant_files WHERE id = $id LIMIT 1;");
      $sendmime = $this->getType($file);
      header ("HTTP/1.1 200 OK");
      header("Content-type: $sendmime");
      header("Content-Length: ".filesize($dlpath.$file));
      header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
      readfile($file); 
    ob_end_flush();
    die;
  }

   public function get_file_info($id){
      return $this->db->query_fetch("SELECT * FROM wf_folderant_files  WHERE id = $id LIMIT 1;");
   }

   public function get_tree($master = 0,$search = FALSE){
      if($search) $search_query = "AND id in (".implode(",", $search).")";
      $sub_folders = $this->db->select("SELECT id, name as label FROM wf_folderant_folders WHERE parent = $master ORDER BY name;");
      if($sub_folders){
         foreach ($sub_folders as $key => $folder){
            if(isset($this->product_codes[$folder["label"]])) $sub_folders[$key]["label"] = $this->product_codes[$folder["label"]];
            $children = $this->get_tree($folder["id"],$search);
            if($children) $sub_folders[$key]["children"] = $children;
            else unset($sub_folders[$key]);
         }
      }
      $sub_files = $this->db->select("SELECT CONCAT('file_',id) as id, name as label, filetype as type FROM wf_folderant_files WHERE parent = $master and parent != 0 $search_query ORDER BY name;");
      if(count($sub_files) || count($sub_folders)) return array_merge($sub_folders, $sub_files);
   }

   public function get_details($id){
      $file = $this->db->query_fetch("SELECT f.label, f.prod_num, f.name, f.id, f.size, f.time, t.type_de FROM wf_folderant_files f, wf_folderant_shorttags t  WHERE f.id = $id AND f.short_tag = t.short_tag LIMIT 1;");
      if(!$file) $file = $this->db->query_fetch("SELECT label, prod_num, name, id, size, time, 'Diverses' as type_de FROM wf_folderant_files WHERE id = $id LIMIT 1;");
      $html = '
      <html>
        <head>
          <!--#if FIREFOX || MOZCENTRAL-->
          <!--#include /js/pdf.js/web/viewer-snippet-firefox-extension.html-->
          <!--#endif-->
              <link rel="stylesheet" href="/js/pdf.js/web/viewer.css"/>
          <!--#if !PRODUCTION-->
              <link rel="resource" type="application/l10n" href="locale.properties"/>
          <!--#endif-->
          <!--#if !(FIREFOX || MOZCENTRAL || CHROME)-->
              <script type="text/javascript" src="/js/pdf.js/web/compatibility.js"></script>
          <!--#endif-->
          <!--#if !PRODUCTION-->
              <script type="text/javascript" src="/js/pdf.js/external/webL10n/l10n.js"></script>
          <!--#endif-->
          <!--#if !PRODUCTION-->
              <script type="text/javascript" src="/js/pdf_combined.js"></script>
              <script type="text/javascript">PDFJS.workerSrc = "/js/pdf_combined.js";</script>
          <!--#endif-->
          <!--#if GENERIC || CHROME-->
          <!--#include /js/pdf.js/web/viewer-snippet.html-->
          <!--#endif-->
          <!--#if B2G-->
          <!--#include /js/pdf.js/web/viewer-snippet-b2g.html-->
          <!--#endif-->
          <script type="text/javascript" src="/js/pdf.js/web/debugger.js"></script>
          <script type="text/javascript" src="/js/pdf.js/web/viewer.js"></script>
        </head>
        <body>
          <div class="modal-body" style="width:1000px;height:800px;">
          <!-- ---------------------------------------------------------------------------------------------------------- -->
                <div id="outerContainer">
                  <div id="sidebarContainer">
                    <div id="toolbarSidebar" class="splitToolbarButton toggled">
                      <button id="viewThumbnail" class="toolbarButton group toggled" title="Show Thumbnails" tabindex="1" data-l10n-id="thumbs">
                         <span data-l10n-id="thumbs_label">Thumbnails</span>
                      </button>
                      <button id="viewOutline" class="toolbarButton group" title="Show Document Outline" tabindex="2" data-l10n-id="outline">
                         <span data-l10n-id="outline_label">Document Outline</span>
                      </button>
                    </div>
                    <div id="sidebarContent">
                      <div id="thumbnailView">
                      </div>
                      <div id="outlineView" class="hidden">
                      </div>
                    </div>
                  </div>  <!-- sidebarContainer -->
                  <div id="mainContainer">
                    <div class="findbar hidden doorHanger" id="findbar">
                      <label for="findInput" class="toolbarLabel" data-l10n-id="find_label">Find:</label>
                      <input id="findInput" class="toolbarField" tabindex="20">
                      <div class="splitToolbarButton">
                        <button class="toolbarButton findPrevious" title="" id="findPrevious" tabindex="21" data-l10n-id="find_previous">
                          <span data-l10n-id="find_previous_label">Previous</span>
                        </button>
                        <div class="splitToolbarButtonSeparator"></div>
                        <button class="toolbarButton findNext" title="" id="findNext" tabindex="22" data-l10n-id="find_next">
                          <span data-l10n-id="find_next_label">Next</span>
                        </button>
                      </div>
                      <input type="checkbox" id="findHighlightAll" class="toolbarField">
                      <label for="findHighlightAll" class="toolbarLabel" tabindex="23" data-l10n-id="find_highlight">Highlight all</label>
                      <input type="checkbox" id="findMatchCase" class="toolbarField">
                      <label for="findMatchCase" class="toolbarLabel" tabindex="24" data-l10n-id="find_match_case_label">Match case</label>
                      <span id="findMsg" class="toolbarLabel"></span>
                    </div>
                    <div class="toolbar">
                      <div id="toolbarContainer">
                        <div id="toolbarViewer">
                          <div id="toolbarViewerLeft">
                            <button id="sidebarToggle" class="toolbarButton" title="Toggle Sidebar" tabindex="3" data-l10n-id="toggle_slider">
                              <span data-l10n-id="toggle_slider_label">Toggle Sidebar</span>
                            </button>
                            <div class="toolbarButtonSpacer"></div>
                            <button id="viewFind" class="toolbarButton group" title="Find in Document" tabindex="4" data-l10n-id="findbar">
                               <span data-l10n-id="findbar_label">Find</span>
                            </button>
                            <div class="splitToolbarButton">
                              <button class="toolbarButton pageUp" title="Previous Page" id="previous" tabindex="5" data-l10n-id="previous">
                                <span data-l10n-id="previous_label">Previous</span>
                              </button>
                              <div class="splitToolbarButtonSeparator"></div>
                              <button class="toolbarButton pageDown" title="Next Page" id="next" tabindex="6" data-l10n-id="next">
                                <span data-l10n-id="next_label">Next</span>
                              </button>
                            </div>
                            <label id="pageNumberLabel" class="toolbarLabel" for="pageNumber" data-l10n-id="page_label">Page: </label>
                            <input type="number" id="pageNumber" class="toolbarField pageNumber" value="1" size="4" min="1" tabindex="7">
                            </input>
                            <span id="numPages" class="toolbarLabel"></span>
                          </div>
                          <div id="toolbarViewerRight">
                            <input id="fileInput" class="fileInput" type="file" oncontextmenu="return false;" style="visibility: hidden; position: fixed; right: 0; top: 0" />
                            <button id="fullscreen" class="toolbarButton fullscreen" title="Switch to Presentation Mode" tabindex="11" data-l10n-id="presentation_mode">
                              <span data-l10n-id="presentation_mode_label">Presentation Mode</span>
                            </button>
                            <button id="openFile" class="toolbarButton openFile" title="Open File" tabindex="12" data-l10n-id="open_file">
                               <span data-l10n-id="open_file_label">Open</span>
                            </button>
                            <button id="print" class="toolbarButton print" title="Print" tabindex="13" data-l10n-id="print">
                              <span data-l10n-id="print_label">Print</span>
                            </button>
                            <button id="download" class="toolbarButton download" title="Download" tabindex="14" data-l10n-id="download">
                              <span data-l10n-id="download_label">Download</span>
                            </button>
                            <!-- <div class="toolbarButtonSpacer"></div> -->
                            <a href="#" id="viewBookmark" class="toolbarButton bookmark" title="Current view (copy or open in new window)" tabindex="15" data-l10n-id="bookmark"><span data-l10n-id="           bookmark_label">Current View</span></a>
                          </div>
                          <div class="outerCenter">
                            <div class="innerCenter" id="toolbarViewerMiddle">
                              <div class="splitToolbarButton">
                                <button class="toolbarButton zoomOut" title="Zoom Out" tabindex="8" data-l10n-id="zoom_out">
                                  <span data-l10n-id="zoom_out_label">Zoom Out</span>
                                </button>
                                <div class="splitToolbarButtonSeparator"></div>
                                <button class="toolbarButton zoomIn" title="Zoom In" tabindex="9" data-l10n-id="zoom_in">
                                  <span data-l10n-id="zoom_in_label">Zoom In</span>
                                 </button>
                              </div>
                              <span id="scaleSelectContainer" class="dropdownToolbarButton">
                                 <select id="scaleSelect" title="Zoom" oncontextmenu="return false;" tabindex="10" data-l10n-id="zoom">
                                  <option id="pageAutoOption" value="auto" selected="selected" data-l10n-id="page_scale_auto">Automatic Zoom</option>
                                  <option id="pageActualOption" value="page-actual" data-l10n-id="page_scale_actual">Actual Size</option>
                                  <option id="pageFitOption" value="page-fit" data-l10n-id="page_scale_fit">Fit Page</option>
                                  <option id="pageWidthOption" value="page-width" data-l10n-id="page_scale_width">Full Width</option>
                                  <option id="customScaleOption" value="custom"></option>
                                  <option value="0.5">50%</option>
                                  <option value="0.75">75%</option>
                                  <option value="1">100%</option>
                                  <option value="1.25">125%</option>
                                  <option value="1.5">150%</option>
                                  <option value="2">200%</option>
                                </select>
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <menu type="context" id="viewerContextMenu">
                      <menuitem label="First Page" id="first_page" data-l10n-id="first_page" ></menuitem>
                      <menuitem label="Last Page" id="last_page" data-l10n-id="last_page" ></menuitem>
                      <menuitem label="Rotate Counter-Clockwise" id="page_rotate_ccw" data-l10n-id="page_rotate_ccw" ></menuitem>
                      <menuitem label="Rotate Clockwise" id="page_rotate_cw" data-l10n-id="page_rotate_cw" ></menuitem>
                    </menu>
                    <div id="viewerContainer">
                      <div id="viewer" contextmenu="viewerContextMenu"></div>
                    </div>
                    <div id="loadingBox">
                      <div id="loading"></div>
                      <div id="loadingBar"><div class="progress"></div></div>
                    </div>
                    <div id="errorWrapper" hidden="true">
                      <div id="errorMessageLeft">
                        <span id="errorMessage"></span>
                        <button id="errorShowMore" onclick="" oncontextmenu="return false;" data-l10n-id="error_more_info">
                          More Information
                        </button>
                        <button id="errorShowLess" onclick="" oncontextmenu="return false;" data-l10n-id="error_less_info" hidden="true">
                          Less Information
                        </button>
                      </div>
                      <div id="errorMessageRight">
                        <button id="errorClose" oncontextmenu="return false;" data-l10n-id="error_close">
                          Close
                        </button>
                      </div>
                      <div class="clearBoth"></div>
                      <textarea id="errorMoreInfo" hidden="true" readonly="readonly"></textarea>
                    </div>
                  </div> <!-- mainContainer -->
                </div> <!-- outerContainer -->
                <div id="printContainer"></div>
          <!-- ---------------------------------------------------------------------------------------------------------- -->
          </div>
        </body>
      </html>
      ';
      return $html;

   }

   public function update_articles(){
      $this->db = new db;
      $files = glob($this->config["source_path"] . 'SAP-Trans/*.xml');
      if ($files) {
         foreach ($files as $file) {
            $arrXml = $this->objectsIntoArray(simplexml_load_string(file_get_contents($file)));
            $this->db->query("INSERT INTO `wf_ctp_articles` 
                                          (ItemCode,ItemName,FrgnName,CodeBars,ItmsGrpNam,ProductCode) 
                                          VALUES 
                                          ('".$arrXml["ItemCode"]."','".$arrXml["ItemName"]."','".$arrXml["ForeignName"]."','".$arrXml["BarCode"]."','".$arrXml["ItemsGroupName"]."','".$arrXml["U_CTP_Product"]."')  
                              ON DUPLICATE KEY UPDATE 
                                          ItemName = '".$arrXml["ItemName"]."',FrgnName = '".$arrXml["ForeignName"]."',CodeBars = '".$arrXml["BarCode"]."',ItmsGrpNam = '".$arrXml["ItemsGroupName"]."',ProductCode = '".$arrXml["U_CTP_Product"]."'
                           ;");
            unlink($file);
         }
      }
   }

   private function objectsIntoArray($arrObjData, $arrSkipIndices = array()){
      $arrData = array();
   
      // if input is object, convert into array
      if (is_object($arrObjData)) {
         $arrObjData = get_object_vars($arrObjData);
      }
   
      if (is_array($arrObjData)) {
         foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
               $value = $this->objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
               continue;
            }
            $arrData[$index] = $value;
         }
      }
      return $arrData;
   }

   private function dir_rekursiv($verzeichnis, $filetypes){ 
      set_time_limit (0);
      echo $verzeichnis.'<br/>';
      $handle =  opendir($verzeichnis); 

      while ($datei = readdir($handle)){ 
        if ($datei != "." && $datei != ".."){ 
            if (is_dir($verzeichnis.$datei)){ 
                $folders[$datei] = $this->dir_rekursiv($verzeichnis.$datei.'/', $filetypes); 
            }
            else{
                foreach ($filetypes as $item) {
                     if (strtolower($item) == substr(strtolower($datei), -strlen($item))){
                        $typeFormat = TRUE;
                        $filetype = $item;
                        $content = '';
                     }
               }
               $file = array();
               if($typeFormat == TRUE) {
                  $extension = substr(strrchr($datei, "."), 1);
                  $file['type']    = $filetype;
                  $file['file']    = $datei;
                  $file['full_path'] = $verzeichnis.$datei;
                  $file['size']    = filesize($verzeichnis.$datei) / 1024;
                  $file['time']    = filemtime($verzeichnis.$datei); 
                  $file['content']    = $content;     
                  $folders[$datei] = $file;
               }
             }
          } 
      }
    closedir($handle); 
    return $folders;
   } 


   private function get_rekursiv_dir_tree($verzeichnis){ 
      $handle =  opendir($verzeichnis); 
      while ($datei = readdir($handle)){ 
        if ($datei != "." && $datei != ".."){ 
            if (is_dir($verzeichnis.$datei)){ 
                $folders[$datei] = $this->get_rekursiv_dir_tree($verzeichnis.$datei.'/'); 
            }
        }
    } 
    closedir($handle); 
    return $folders;
   } 

   private function get_rekursiv_db_tree($master = 0){
      $sub_folders = $this->db->select("SELECT id, name FROM wf_folderant_folders WHERE parent = $master;");
      $tree = array();
      if($sub_folders){
         foreach ($sub_folders as $key => $folder) $tree[$folder["name"]] = $this->get_rekursiv_db_tree($folder["id"]);
      }
      $sub_files = $this->db->select("SELECT name, time, fullpath FROM wf_folderant_files WHERE parent = $master;");
         foreach ($sub_files as $key => $sub_file){
            $tree[$sub_file["name"]] = $sub_file;
            unset($tree[$sub_file["name"]]["name"]);
         }
      return $tree;
   }

   private function array_diff_assoc_recursive($array1, $array2){
      foreach($array1 as $key => $value){
        if(is_array($value)){
              if(!isset($array2[$key])) $difference[$key] = $value;
              elseif(!is_array($array2[$key])) $difference[$key] = $value;
              else{
                  $new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
                  if($new_diff != FALSE) $difference[$key] = $new_diff;
              }
          }
          elseif(!isset($array2[$key]) || $array2[$key] != $value) $difference[$key] = $value;
      }
      return !isset($difference) ? 0 : $difference;
   } 

   private function array_intersect_assoc_recursive(&$arr1, &$arr2) {
      if (!is_array($arr1) || !is_array($arr2)) {
         return $arr1 == $arr2; // or === for strict type
      }
      $commonkeys = array_intersect(array_keys($arr1), array_keys($arr2));
      $ret = array();
      foreach ($commonkeys as $key) {
         $ret[$key] =& $this->array_intersect_assoc_recursive($arr1[$key], $arr2[$key]);
      }
      return $ret;
   } 

   private function do_update($delete_array, $new_array, $check_array){
      $this->process_delete_update($delete_array);
      $this->process_new_update($new_array,explode(',', $this->config["file_types"]),$this->config["source_path"]);
      $this->process_check_update($check_array,explode(',', $this->config["file_types"]));
   }

   private function process_delete_update($delete_array, $parent = 0){
      if(is_array($delete_array)){
      foreach($delete_array as $name => $file){
         if(isset($file["fullpath"])) {
            $this->db->query("DELETE FROM wf_folderant_files WHERE fullpath = '".$file["fullpath"]."' LIMIT 1;");
            echo "DELETE FROM wf_folderant_files WHERE fullpath = '".$file["fullpath"]."' LIMIT 1;";
         }
         elseif(count($file)){
            $id = $this->db->query_fetch_single("SELECT id FROM wf_folderant_folders WHERE name = '".$name."' AND parent = ".$parent." LIMIT 1");
            $this->process_delete_update($delete_array[$name],$id);
            if(!$this->db->query_fetch_single("SELECT id FROM wf_folderant_folders WHERE parent = ".$id." LIMIT 1;") && !$this->db->query_fetch_single("SELECT id FROM wf_folderant_files WHERE parent = ".$id." LIMIT 1;")) $this->db->query("DELETE FROM wf_folderant_folders WHERE id = '".$id."' LIMIT 1;");
         }
      }
   }
   }

   private function process_new_update($new_array, $filetypes, $verzeichnis, $parent = 0){
      if(is_array($new_array)){
      foreach($new_array as $datei => $file){
            if (is_dir($verzeichnis.$datei)){ 
               $id = $this->db->query_fetch_single("SELECT id FROM wf_folderant_folders WHERE name = '".$datei."' AND parent = ".$parent." LIMIT 1");
               if(!$id) $id = $this->db->lastindex_query("
                                                            INSERT INTO `wf_folderant_folders` (
                                                                                                `id` ,
                                                                                                `update` ,
                                                                                                `editor` ,
                                                                                                `name` ,
                                                                                                `parent`
                                                                                               )
                                                            VALUES (
                                                            NULL , '0000-00-00 00:00:00', 'folderant', '$datei', '$parent'
                                                         );"
            );
               $folders[$datei] = $this->process_new_update($new_array[$datei], $filetypes, $verzeichnis.$datei.'/', $id); 
            }
            else{ 
               foreach ($filetypes as $item) {
                     if (strtolower($item) == substr(strtolower($datei), -strlen($item))){
                        $typeFormat = TRUE;
                        $filetype = $item;
                        $content = '';
                     }
               }
               $file = array();
               if($typeFormat == TRUE) {
                  $extension = substr(strrchr($filename, "."), 1);
                  $file['type']    = $filetype;
                  $file['file']    = $datei;
                  $file['full_path'] = $verzeichnis.$datei;
                  $file['size']    = filesize($verzeichnis.$datei) / 1024;
                  $file['time']    = filemtime($verzeichnis.$datei); 
                  $file['content']    = $content;     
                  $this->db->query("
                                       INSERT INTO `wf_folderant_files`(
                                       `id` ,
                                       `update` ,
                                       `editor` ,
                                       `parent` ,
                                       `name` ,
                                       `fullpath`,
                                       `filetype` ,
                                       `size` ,
                                       `time` ,
                                       `content`,
                                       `lang`
                                       )
                                       VALUES (
                                       NULL , '0000-00-00 00:00:00', 'folderant', '$parent', '".$file["file"]."' , '".$file["full_path"]."', '".$file["type"]."', '".$file["size"]."', '".$file["time"]."','".addslashes($file["content"])."','".$file['lang']."'
                                       );"
            );
               }
            }
         }
      }
   }

   private function process_check_update($check_array, $filetypes){
      if(is_array($check_array)){
      foreach($check_array as $datei => $file_arr){
            if (is_dir($verzeichnis.$datei) && $datei != "SAP-Trans"){ 
               $folders[$datei] = $this->process_check_update($check_array[$datei], $filetypes); 
            }
            elseif($datei != "SAP-Trans" && $file_arr["time"] > $this->db->query_fetch_single("SELECT time FROM wf_folderant_files WHERE fullpath = '".$file_arr["fullpath"]."' LIMIT 1;")){ 
               foreach ($filetypes as $item) {
                     if (strtolower($item) == substr(strtolower($datei), -strlen($item))){
                        $typeFormat = TRUE;
                        $filetype = $item;
                        $content = '';
                     }
               }
               $file = array();
               if($typeFormat == TRUE) {
                  $extension = substr(strrchr($filename, "."), 1);
                  $file['type']    = $filetype;
                  $file['file']    = $datei;
                  $file['full_path'] = $file_arr['full_path'];
                  $file['size']    = filesize($file_arr['full_path']) / 1024;
                  $file['time']    = filemtime($file_arr['full_path']); 
                  $file['content']    = $content;     
                  $this->db->query("
                                       UPDATE `wf_folderant_files` SET           
                                       `name` = '".$file["file"]."',
                                       `filetype` = '".$file["type"]."',
                                       `size` = '".$file["size"]."',
                                       `time` =  '".$file["time"]."',
                                       `content` = '".addslashes($file["content"])."',
                                       `lang` = '".$file['lang']."'
                                        WHERE fullpath = '".$file['full_path']."' LIMIT 1;"
               );
               }
            }
         }
      }
   }

   public function update_db(){
      $this->db = new db;
      $file_array = $this->get_rekursiv_dir_tree($this->config["source_path"]);
      $db_array = $this->get_rekursiv_db_tree();
      $delete_array = $this->array_diff_assoc_recursive($db_array,$file_array);
      $new_array = $this->array_diff_assoc_recursive($file_array,$db_array);
      $check_array = $this->array_intersect_assoc_recursive($db_array,$file_array);
      $update = $this->do_update($delete_array, $new_array, $check_array);
      return $update;
   }

   private function write2db_full($folder,$parent = 0){
      foreach($folder as $name => $item){
         if(!isset($item["type"])){
            $new_parent = $this->db->lastindex_query("
            INSERT INTO `wf_folderant_folders` (
                                          `id` ,
                                          `update` ,
                                          `editor` ,
                                          `name` ,
                                          `parent`
                                       )
                                 VALUES (
                                          NULL , '0000-00-00 00:00:00', 'folderant', '$name', '$parent'
                                       );"
            );
            $this->write2db_full($item,$new_parent);
         }
         else{
            $this->db->query("
            INSERT INTO `wf_folderant_files`(
                                       `id` ,
                                       `update` ,
                                       `editor` ,
                                       `parent` ,
                                       `name` ,
                                       `fullpath`,
                                       `filetype` ,
                                       `size` ,
                                       `time` ,
                                       `content`,
                                       `lang`
                                    )
                              VALUES (
                                       NULL , '0000-00-00 00:00:00', 'folderant', '$parent', '".$item["file"]."' , '".$item["full_path"]."', '".$item["type"]."', '".$item["size"]."', '".$item["time"]."','".addslashes($item["content"])."','".$item['lang']."'
                                    );"
            );
         }
      }
   }

   public function index_folders(){
      $folders = $this->dir_rekursiv($this->config["source_path"], explode(',', $this->config["file_types"]));
      $this->db = new db();
      $this->db->query("TRUNCATE wf_folderant_folders;");
      $this->db->query("TRUNCATE wf_folderant_files;");
      $this->write2db_full($folders);
      return $folders;
   }

   protected function getType($filename) {
      $filename = basename($filename);
      $filename = explode('.', $filename);
      $filename = $filename[count($filename)-1];   
      return $this->privFindType($filename);
   }

   protected function privFindType($ext) {
      $mimetypes = $this->privBuildMimeArray();
      if (isset($mimetypes[$ext])) {
         return $mimetypes[$ext];     
      } else {
         return 'application/octet-stream';
      }
         
   }

   protected function privBuildMimeArray() {
      return array(
         "ez" => "application/andrew-inset",
         "hqx" => "application/mac-binhex40",
         "cpt" => "application/mac-compactpro",
         "doc" => "application/msword",
         "bin" => "application/octet-stream",
         "dms" => "application/octet-stream",
         "lha" => "application/octet-stream",
         "lzh" => "application/octet-stream",
         "exe" => "application/octet-stream",
         "class" => "application/octet-stream",
         "so" => "application/octet-stream",
         "dll" => "application/octet-stream",
         "oda" => "application/oda",
         "pdf" => "application/pdf",
         "ai" => "application/postscript",
         "eps" => "application/postscript",
         "ps" => "application/postscript",
         "smi" => "application/smil",
         "smil" => "application/smil",
         "wbxml" => "application/vnd.wap.wbxml",
         "wmlc" => "application/vnd.wap.wmlc",
         "wmlsc" => "application/vnd.wap.wmlscriptc",
         "bcpio" => "application/x-bcpio",
         "vcd" => "application/x-cdlink",
         "pgn" => "application/x-chess-pgn",
         "cpio" => "application/x-cpio",
         "csh" => "application/x-csh",
         "dcr" => "application/x-director",
         "dir" => "application/x-director",
         "dxr" => "application/x-director",
         "dvi" => "application/x-dvi",
         "spl" => "application/x-futuresplash",
         "gtar" => "application/x-gtar",
         "hdf" => "application/x-hdf",
         "js" => "application/x-javascript",
         "skp" => "application/x-koan",
         "skd" => "application/x-koan",
         "skt" => "application/x-koan",
         "skm" => "application/x-koan",
         "latex" => "application/x-latex",
         "nc" => "application/x-netcdf",
         "cdf" => "application/x-netcdf",
         "sh" => "application/x-sh",
         "shar" => "application/x-shar",
         "swf" => "application/x-shockwave-flash",
         "sit" => "application/x-stuffit",
         "sv4cpio" => "application/x-sv4cpio",
         "sv4crc" => "application/x-sv4crc",
         "tar" => "application/x-tar",
         "tcl" => "application/x-tcl",
         "tex" => "application/x-tex",
         "texinfo" => "application/x-texinfo",
         "texi" => "application/x-texinfo",
         "t" => "application/x-troff",
         "tr" => "application/x-troff",
         "roff" => "application/x-troff",
         "man" => "application/x-troff-man",
         "me" => "application/x-troff-me",
         "ms" => "application/x-troff-ms",
         "ustar" => "application/x-ustar",
         "src" => "application/x-wais-source",
         "xhtml" => "application/xhtml+xml",
         "xht" => "application/xhtml+xml",
         "zip" => "application/zip",
         "au" => "audio/basic",
         "snd" => "audio/basic",
         "mid" => "audio/midi",
         "midi" => "audio/midi",
         "kar" => "audio/midi",
         "mpga" => "audio/mpeg",
         "mp2" => "audio/mpeg",
         "mp3" => "audio/mpeg",
         "aif" => "audio/x-aiff",
         "aiff" => "audio/x-aiff",
         "aifc" => "audio/x-aiff",
         "m3u" => "audio/x-mpegurl",
         "ram" => "audio/x-pn-realaudio",
         "rm" => "audio/x-pn-realaudio",
         "rpm" => "audio/x-pn-realaudio-plugin",
         "ra" => "audio/x-realaudio",
         "wav" => "audio/x-wav",
         "pdb" => "chemical/x-pdb",
         "xyz" => "chemical/x-xyz",
         "bmp" => "image/bmp",
         "gif" => "image/gif",
         "ief" => "image/ief",
         "jpeg" => "image/jpeg",
         "jpg" => "image/jpeg",
         "jpe" => "image/jpeg",
         "png" => "image/png",
         "tiff" => "image/tiff",
         "tif" => "image/tif",
         "djvu" => "image/vnd.djvu",
         "djv" => "image/vnd.djvu",
         "wbmp" => "image/vnd.wap.wbmp",
         "ras" => "image/x-cmu-raster",
         "pnm" => "image/x-portable-anymap",
         "pbm" => "image/x-portable-bitmap",
         "pgm" => "image/x-portable-graymap",
         "ppm" => "image/x-portable-pixmap",
         "rgb" => "image/x-rgb",
         "xbm" => "image/x-xbitmap",
         "xpm" => "image/x-xpixmap",
         "xwd" => "image/x-windowdump",
         "igs" => "model/iges",
         "iges" => "model/iges",
         "msh" => "model/mesh",
         "mesh" => "model/mesh",
         "silo" => "model/mesh",
         "wrl" => "model/vrml",
         "vrml" => "model/vrml",
         "css" => "text/css",
         "html" => "text/html",
         "htm" => "text/html",
         "asc" => "text/plain",
         "txt" => "text/plain",
         "rtx" => "text/richtext",
         "rtf" => "text/rtf",
         "sgml" => "text/sgml",
         "sgm" => "text/sgml",
         "tsv" => "text/tab-seperated-values",
         "wml" => "text/vnd.wap.wml",
         "wmls" => "text/vnd.wap.wmlscript",
         "etx" => "text/x-setext",
         "xml" => "text/xml",
         "xsl" => "text/xml",
         "mpeg" => "video/mpeg",
         "mpg" => "video/mpeg",
         "mpe" => "video/mpeg",
         "qt" => "video/quicktime",
         "mov" => "video/quicktime",
         "mxu" => "video/vnd.mpegurl",
         "avi" => "video/x-msvideo",
         "movie" => "video/x-sgi-movie",
         "ice" => "x-conference-xcooltalk"
      );
   } 

   protected function copy_recurse(&$statsCopyFolder, $source = FALSE, $dest = FALSE, $recursive = TRUE){
      if (!is_dir($dest)){ 
         mkdir($dest); 
      }
      $handle = @opendir($source);
      if(!$handle) return false;
      while ($file = @readdir ($handle)){
         if (eregi("^\.{1,2}$",$file)) continue;
         if(!$recursive && $source != $source.$file."/"){
            if(is_dir($source.$file)) continue;
         }
         if(is_dir($source.$file)) $this->copy_recurse($statsCopyFolder, $source.$file."/", $dest.$file."/", $recursive);
         else{
            copy($source.$file, $dest.$file);
            $statsCopyFolder['files']++;
            $statsCopyFolder['bytes'] += filesize($source.$file); 
         }
      }
      @closedir($handle);  
   } 

   public function make_backup(){   
      set_time_limit (0);
      $stats = "";
      $this->rmdirs($this->config["backup_path"]);
      $this->copy_recurse($stats,$this->config["source_path"],$this->config["backup_path"]);
      $this->db = new db();
      unlink($this->config["backup_path"]."../dbDump.sql");
      $fp = fopen($this->config["backup_path"]."../dbDump.sql", "wb");
      fwrite($fp, $this->db->dump(array("wf_folderant_config","wf_folderant_files","wf_folderant_folders","wf_folderant_shorttags")));  
      fclose($fp);
      echo "Backup erfolgreich in ".$this->config["backup_path"]." erstellt!";
   }

   public function ext_backup(){   
      set_time_limit (0);
      $stats = "";
      $zip = new recurseZip();
      $zip->compress($this->config["source_path"],$_SESSION["_registry"]["root"]."/admin/tmp/");
      $this->db = new db();
      $fp = fopen("./tmp/folderant.sql", "wb");
      fwrite($fp, $this->db->dump(array("wf_folderant_config","wf_folderant_files","wf_folderant_folders","wf_folderant_shorttags")));  
      fclose($fp);
      return basename($this->config["source_path"]);
   }

   public function get_backup(){   
      set_time_limit (0);
      $stats = "";
      $this->rmdirs($this->config["source_path"]);
      $this->copy_recurse($stats,$this->config["backup_path"],$this->config["source_path"]);
      $this->db = new db();
      $dump = file_get_contents($this->config["backup_path"]."../dbDump.txt");
      $dump = explode(";\r\n", $dump);
      foreach ($dump as $query){
         $this->db->query($query);
      }
      $files = $this->db->select("SELECT fullpath, time FROM wf_folderant_files;");
      foreach($files as $file){
         touch($file["fullpath"], $file["time"]);
      }
      echo "Backup erfolgreich eingespielt!";
   }

   protected function rmdirs($dir) {
      $files = glob($dir . '*');
      if ($files) {
         foreach ($files as $file) {
            if (is_dir($file)) {
               $this->rmdirs($file . '/');
            }
            else {
               if (!@unlink($file)) {
                  chmod($file, 0777);
                  unlink($file);
               }
            }
         }
      }
      if (!@rmdir($dir)) {
         chmod($dir, 0777);
         rmdir($dir);
      }
   }

   public function rename_stuff($ids = FALSE, $dir = FALSE){
      set_time_limit (0);
      if(!$ids) $ids = $this->db->select_pair("wf_ctp_articles","ItemCode","ProductCode");
      if(!$dir) $dir = "/is/htdocs/wp1024418_BYIFCIGY79/www/ftp-kmc/PRO-TEC/";
      $files = glob($dir . '*');
      if ($files) {
         foreach ($files as $file) {
            if (is_dir($file)) {
               foreach($ids as $key => $id){
                  if(strpos ($file, substr($key, 1))){
                     echo basename ($file)." - ";
                     if($id){
                     $new_name = str_replace(basename ($file), $id, $file);
                     rename($file, $new_name);
                     $file = $new_name;
                     }
                     echo $file."<br>";
                     break;
                  }
               }
               $this->rename_stuff($ids, $file . '/');
            }
            else {
               foreach($ids as $key => $id){
                  if(strpos ($file, $key)){
                     echo $file."-";
                     if($id){
                     $new_name = str_replace($key, $id, $file);
                     rename($file, $new_name);
                     $file = $new_name;
                     }
                     echo $file."<br>";
                     break;
                  }
               }
            }
         }
      }
   }
}
?>