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

function mac_get_current_user(){
    return $_SESSION["_registry"]["user"]["name"];
}

function FilterText($txt) {
  $txt = str_replace ("Ä", "&Auml;", $txt);
  $txt = str_replace ("ä", "&auml;", $txt);
  $txt = str_replace ("Ö", "&Ouml;", $txt);
  $txt = str_replace ("ö", "&ouml;", $txt);
  $txt = str_replace ("Ü", "&Uuml;", $txt);
  $txt = str_replace ("ü", "&uuml;", $txt);
  $txt = str_replace ("ß", "&szlig;", $txt);
  return($txt);
}

function CleanText($txt) {
  $txt = str_replace ("Ä", "Ae", $txt);
  $txt = str_replace ("ä", "ae", $txt);
  $txt = str_replace ("Ö", "Oe", $txt);
  $txt = str_replace ("ö", "oe", $txt);
  $txt = str_replace ("Ü", "Ue", $txt);
  $txt = str_replace ("ü", "ue", $txt);
  $txt = str_replace ("ß", "ss", $txt);
  return($txt);
}

function gen_fields($field_array){
        global $DB;
        $countrys = $DB->select("SELECT * FROM `wf_country` WHERE active = 1",MYSQLI_ASSOC, FALSE, "id");
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

function token($length){
    // Festlegung der verfügbaren Buchstaben, Zahlen und Sonderzeichen
    $specialChars = array();
    $chars = array_merge(range('a','z'), range('A','Z'), range(0,9), $specialChars);
    // Einzelne Buchstaben entfernen
    unset($chars[array_search('i',$chars)]);
    unset($chars[array_search('l',$chars)]);
    unset($chars[array_search('o',$chars)]);
    unset($chars[array_search('I',$chars)]);
    unset($chars[array_search('O',$chars)]);
    unset($chars[array_search('Q',$chars)]);
    $chars = array_values($chars);
    // Array mischen
    shuffle($chars);
    // Array beschneiden
    $pwd = array_slice($chars,0,$length);
    // Rückgabewert als String
    return implode('',$pwd);
}

?>
