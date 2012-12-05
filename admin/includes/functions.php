<?php
function cut_txt($string,$laenge){
    $origin=strlen($string);
    $stri_arr=explode(" ",$string);
    $anzzahl=count($stri_arr);
    $gekuerzt=0;
    $string="";
    while($gekuerzt<$anzzahl){ 
        $string_alt=$string;
        $string=$string." ".$stri_arr[$gekuerzt];
        $gekuerzt++;
        if(strlen($string)>$laenge){ 
            $gekuerzt=$anzzahl; 
            $string=$string_alt;
        } 
    } 
    return $string; 
}
 
function send_mail($from = FALSE,$to = FALSE, $msg = FALSE, $subject = FALSE, $html= TRUE){
    if($from && $to && $msg && $subject){
        if ($html){
            $header  = 'MIME-Version: 1.0' . "\r\n";
            $header .= 'Content-type: text/html; charset=UTF-8' . "\r\n"; 
        }
            $header .= 'From: '.$from. "\r\n";          
            $header .= 'Reply-To: '.$from. "\r\n";
            $header .= 'X-Mailer: PHP/' . phpversion();
            return mail ($to,$subject,$msg,$header);
    }
    return "fehler";
}

function get_remote_file($url)
{
    if (ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    }
    elseif (function_exists('curl_init')) {
        $c = curl_init($url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_HEADER, 0);
        $file = curl_exec($c);
        curl_close($c);
        return $file;
    }
    else {
        die('Error');
    }
} 
function unicode($ascii){
$unicode = "";
for($x=0;$x<strlen($ascii);$x++){
$unicode .= "&#".ord(substr($ascii, $x, 1)).";";
}
return $unicode;
}
function mail_obfuscater($string){
    $pattern1= '([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@{1,2}([ ]+|)([ a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)';
    $pattern2='\b[A-Z0-9._%+-]+@{1,2}[A-Z0-9.-]+\.[A-Z]{2,4}\b';
    $pattern="/$pattern1|$pattern2/i";
    if(preg_match_all ($pattern , $string , $matches )){
        foreach ($matches[0] as $match){
            $string = str_replace($match, '<a href="'.unicode("mailto:".$match).'">'.unicode($match).'</a>', $string);
        }
    }
    return $string;
}

function clean_css($string){
          $search = array(
'/color=\"([\S]*)\" size=\"([\S]*)\"/e',
'/size=\"([\S]*)\" color=\"([\S]*)\"/e',
'/color=\"([\S]*)\"/e',
'/size=\"([\S]*)\"/e'
);

$replace = array(
"'style=\"color:$1; font-size:'.( 5 * $1 ).'px;\"'",
"'style=\"color:$2; font-size:'.( 5 * $1 ).'px;\"'",
"'style=\"color:$1\"'",
"'style=\"font-size:'.( 5 * $1 ).'px;\"'"
);

$string = preg_replace($search,$replace, $string);
return $string;
}

function content($string){
    return clean_css(mail_obfuscater($string));
}

function has_subs($table, $cat_id = 0){
    global $DB;
    if($DB->affected_query("SELECT id FROM $table WHERE parent = $cat_id LIMIT 1;")) return true;
}

function generate_tree($table, $field, $id = false, $parent_path = '',$filter = '', $parent_id = 0, $deep = 0){
    global $DB, $URL_ROOT, $content;
    $categorys = $DB->select("SELECT * FROM $table WHERE parent = $parent_id $filter ORDER BY `order`;");
    if(!$id) $id = $parent_id;
    $html .='
    <ul class="cat_nav cat_nav_'.$deep.'" id="cat_nav_'.$id.'">';
    foreach($categorys as $category){
        $html .= '
        <li';
        if(in_array($category[$field], $content)) $html .=' class="active" ';
        $html .='>
            <a href="'.$URL_ROOT.$parent_path.str_replace("%2F","//",urlencode($category[$field])).'/">&gt; '.$category[$field].'</a>';
        if(has_subs($table, $category["id"])) $html .= generate_tree($table, $field, false, $parent_path.str_replace("%2F","//",urlencode($category[$field])).'/','', $category["id"], $deep + 1 );
        $html .= '
        </li>';
    }
    $html .= '
    </ul>';
    return $html;
}


function generate_dropdown_tree($table, $field, $id = false, $parent_path = '',$filter = '', $parent_id = 0, $deep = 0){
    global $DB, $URL_ROOT, $content;
    $categorys = $DB->select("SELECT * FROM $table WHERE parent = $parent_id $filter ORDER BY `order`;");
    if(!$deep) {
        $ul_class = 'nav';
    }
    else {
        $ul_class = 'dropdown-menu';
    }
    if($deep > 1) {
        $ul_class .= ' sub-menu';
    }
    if(!$id) $id = $parent_id;
    $html .='
    <ul class="'.$ul_class.'" id="cat_nav_'.$id.'">';
    foreach($categorys as $category){
        unset($a_attr,$a_href,$li_class);
        if(has_subs($table, $category["id"])){
            $a_attr = 'class="dropdown-toggle" href="'.$URL_ROOT.$parent_path.str_replace("%2F","//",urlencode($category[$field])).'/" toggle="#dropdown_menu_'.$category["id"].'"';
            $li_class = 'dropdown';
            if(!$parent_id) $caret = '<span class="caret"></span>';
            else $caret = '<span class="right-caret"></span>';
        }
        else {
            $a_href = 'href="'.$URL_ROOT.$parent_path.str_replace("%2F","//",urlencode($category[$field])).'/"';
            $caret = '';
        }
        $html .= '
        <li id="dropdown_menu_'.$category["id"].'" class="'.$li_class;
        if(in_array($category[$field], $content)) $html .=' active ';
        $html .='">
            <a '.$a_attr.$a_href.'>'.$category["titel"].$caret.'</a>';
        if(isset($a_attr)) $html .= generate_dropdown_tree($table, $field, false, $parent_path.str_replace("%2F","//",urlencode($category[$field])).'/','', $category["id"], $deep + 1 );
        $html .= '
        </li>';
    }
    $html .= '
    </ul>';
    return $html;
}

/**
 * A function for retrieving the Kölner Phonetik value of a string
 *
 * As described at http://de.wikipedia.org/wiki/Kölner_Phonetik
 * Based on Hans Joachim Postel: Die Kölner Phonetik.
 * Ein Verfahren zur Identifizierung von Personennamen auf der
 * Grundlage der Gestaltanalyse.
 * in: IBM-Nachrichten, 19. Jahrgang, 1969, S. 925-931
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package phonetics
 * @version 1.0
 * @link http://www.einfachmarke.de
 * @license GPL 3.0 <http://www.gnu.org/licenses/>
 * @copyright  2008 by einfachmarke.de
 * @author Nicolas Zimmer <nicolas dot zimmer at einfachmarke.de>
 */

function cologne_phon($word){
   
  /**
  * @param  string  $word string to be analyzed
  * @return string  $value represents the Kölner Phonetik value
  * @access public
  */
 
    //prepare for processing
    $word=strtolower($word);
    $substitution=array(
            "ä"=>"a",
            "ö"=>"o",
            "ü"=>"u",
            "ß"=>"ss",
            "ph"=>"f"
            );

    foreach ($substitution as $letter=>$substitution) {
        $word=str_replace($letter,$substitution,$word);
    }
    unset($substitution);
    $len=strlen($word);
   
    //Rule for exeptions
    $exceptionsLeading=array(
    4=>array("ca","ch","ck","cl","co","cq","cu","cx"),
    8=>array("dc","ds","dz","tc","ts","tz")
    );
   
    $exceptionsFollowing=array("sc","zc","cx","kx","qx");
   
    //Table for coding
    $codingTable=array(
    0=>array("a","e","i","j","o","u","y"),
    1=>array("b","p"),
    2=>array("d","t"),
    3=>array("f","v","w"),
    4=>array("c","g","k","q"),
    48=>array("x"),
    5=>array("l"),
    6=>array("m","n"),
    7=>array("r"),
    8=>array("c","s","z"),
    );
   
    for ($i=0;$i<$len;$i++){
        $value[$i]="";
       
        //Exceptions
        if ($i==0 AND $word[$i].$word[$i+1]=="cr") $value[$i]=4;
       
        foreach ($exceptionsLeading as $code=>$letters) {
            if (in_array($word[$i].$word[$i+1],$letters)){

                    $value[$i]=$code;

}                }
       
        if ($i!=0 AND (in_array($word[$i-1].$word[$i],$exceptionsFollowing))) {

            $value[$i]=8;       

}               
       
        //Normal encoding
        if ($value[$i]==""){
                foreach ($codingTable as $code=>$letters) {
                    if (in_array($word[$i],$letters))$value[$i]=$code;
                }
            }
        }
    unset($exceptionsFollowing);
    unset($codingTable);
    unset($word);
    //delete double values
    $len=count($value);
   
    for ($i=1;$i<$len;$i++){
        if ($value[$i]==$value[$i-1]) $value[$i]="";
    }
    unset($i);
    //delete vocals
    for ($i=1;$i>$len;$i++){//omitting first characer code and h
        if ($value[$i]==0) $value[$i]="";
    }
   
   
    $value=array_filter($value);
    $value=implode("",$value);
   
    return $value;
   
}
function check_phon($string1,$string2){
    if(soundex($string1) == soundex($string2)) return true;
    return false;
}

function gen_fields($field_array){
        global $DB;
        $countrys = $DB->select("SELECT * FROM `wf_countrys` WHERE status = 1",MYSQLI_ASSOC, FALSE, "id");
        if(isset($field_array["Land"]) && !$field_array["Land"][0]) $field_array["Land"][0] = "a7c40f6321c6f6109.43859248";
        $html = '<table class="form_table">';
        foreach ($field_array as $name => $field){
            $html .= '
            <tr><td>'.$name;
            if ($field[1]) $html .= ' <sup>*</sup>';
            $html .= ':</td>';
            if($name == "Land"){
                $html .= '<td><select style="width:140px;" id="'.$field[2].'" name="'.$field[2].'" value="'.$field[0].'"';
            if($field[1]) $html .= ' class="'.$field[1].'"';
            $html .='> 
            ';
            foreach ($countrys as $id => $country){
                $html .= '<option value="'.$id.'"';
                if ($field[0] == $id) $html .= ' selected="selected"';
                $html .= '>'.$country["name_de"].'</option>
                ';
            }
            $html .='</td>';}
            else{
                $html .= '<td><input id="'.$field[2].'" type="';
            if($field[2] == "password" || $field[2] == "password2") $html .= "password";
            else $html .="text";
            $html .= '" name="'.$field[2].'" value="'.$field[0].'"';
            if($field[1]) $html .= ' class="'.$field[1].'"';
            $html .=' /> </td>';}
            $html .='</tr>';
        }
        $html .='</table>';
        return $html;
}
    function user_details($username){
        global $DB;
            $user = $DB->query_fetch("SELECT * FROM permissions_entity WHERE name LIKE '$username' LIMIT 1");
            $user['nodes']= $DB->select_pair ('permissions','permission','value',FALSE,FALSE, "name = '$username' AND value != '' AND permission != 'password'" );
            $user['inheritance'] = $DB->query_fetch("SELECT * FROM permissions_inheritance WHERE child = '$username' LIMIT 1");
            $user['group'] = $DB->query_fetch("SELECT * FROM permissions_entity WHERE name = '".$user["inheritance"]["parent"]."' LIMIT 1");
            $user['group']['nodes'] = $DB->select_pair ('permissions','permission','value',FALSE,FALSE, "name = '".$user['group']['name']."' AND value != ''" );
            return $user;
    }
if (!function_exists('recurse'))
{
    function recurse($array, $array1)
    {
      foreach ($array1 as $key => $value)
      {
        // create new key in $array, if it is empty or not an array
        if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key])))
        {
          $array[$key] = array();
        }
 
        // overwrite the value in the base array
        if (is_array($value))
        {
          $value = recurse($array[$key], $value);
        }
        $array[$key] = $value;
      }
      return $array;
    }
}


if (!function_exists('array_replace_recursive'))
{
  function array_replace_recursive($array, $array1)
  {
    // handle the arguments, merge one by one
    $args = func_get_args();
    $array = $args[0];
    if (!is_array($array))
    {
      return $array;
    }
    for ($i = 1; $i < count($args); $i++)
    {
      if (is_array($args[$i]))
      {
        $array = recurse($array, $args[$i]);
      }
    }
    return $array;
  }
} 
?>