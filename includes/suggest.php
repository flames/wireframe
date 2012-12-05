<?php
require("general.inc.php");
function check_phon($string1,$string2){
    if(soundex($string1) == soundex($string2)) return true;
    return false;
}
$hits =array();
$query = '';
if(array_key_exists('value',$_GET)){
	$query = $_GET['value'];
	$callback = $_GET['callback'];
} else if(array_key_exists('value',$_POST)){
	$query = $_POST['value'];
	$callback = $_POST['callback'];
}
$sites = $DB->select("SELECT `titel`, `text` FROM wf_sites WHERE (`titel` SOUNDS LIKE '".$query."' OR `titel` LIKE '%".$query."%' OR `text` LIKE '%".$query."%') AND status = 1;");
$products = $DB->select("SELECT  `art_num`, `name`, `desc`, `short_desc`, `keywords` FROM `wf_prods` WHERE (`art_num` LIKE '".$query."' OR `name` SOUNDS LIKE '".$query."' OR `name` LIKE '%".$query."%' OR `desc` LIKE '%".$query."%' OR `short_desc` LIKE '%".$query."%' OR `keywords` LIKE '%".$query."%') AND `status` = 1;");
foreach ($products as $db_hit) {
	print_r($db_hit);
	if((check_phon($db_hit["name"],$query) || stripos($db_hit["name"], $query)) ) $hits[] = $db_hit["name"];
	if((stripos($db_hit["art_num"], $query)) ) $hits[] = $db_hit["art_num"];
	if((stripos($db_hit["desc"], $query))) $hits[] = $db_hit["desc"];
	if((stripos($db_hit["short_desc"], $query)) ) $hits[] = $db_hit["short_desc"];
	if($db_hit["keywords"]) $keywords = preg_split("/,/", $db_hit["keywords"]);
	foreach($keywords as $keyword){
		if((stripos($keyword, $query)) ) $hits[] = $keyword;
	}
}
foreach ($sites as $db_hit) {
	if((check_phon($db_hit["titel"],$query) || stripos($db_hit["titel"], $query))) $hits[] = $db_hit["titel"];	
	if((stripos($db_hit["text"], $query)) ) $hits[] = $db_hit["text"];
}
$retVal['suggestions'] = $suggestions;
header('Content-type: application/json');
echo $callback.'('.json_encode($hits).')';
?>