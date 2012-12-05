<?php
$sites = $DB->select("SELECT `id`, `titel`, `text` FROM wf_sites WHERE (`titel` SOUNDS LIKE '".$_POST["search_text"]."' OR `titel` LIKE '%".$_POST["search_text"]."%' OR `text` LIKE '%".$_POST["search_text"]."%') AND status = 1;");
$products = $DB->select("SELECT  `id`, `art_num`, `name`, `desc`, `short_desc`, `keywords` FROM `wf_prods` WHERE (`art_num` LIKE '".$_POST["search_text"]."' OR `name` SOUNDS LIKE '".$_POST["search_text"]."' OR `name` LIKE '%".$_POST["search_text"]."%' OR `desc` LIKE '%".$_POST["search_text"]."%' OR `short_desc` LIKE '%".$_POST["search_text"]."%' OR `keywords` LIKE '%".$_POST["search_text"]."%') AND `status` = 1;");
echo "Die Suche nach <b>".$_POST["search_text"]."</b> ergab <b>".(count($sites) + count($products))."</b> Treffer.";
foreach ($products as $product){
	echo '<h2><a href="'.$URL_ROOT.'Shop/'.urlencode($product["art_num"]).'/'.urlencode($product["name"]).'/">'.$product["name"].'</a></h2>'.$product["short_desc"].'<br/><br/>';
}
foreach ($sites as $site){
	if($site["parent"]){
		$parent = $DB->query_fetch("SELECT * FROM wf_sites WHERE id = ".$site["parent"]." LIMIT 1 ;"); 
		echo '<h2><a href="'.$URL_ROOT.$parent["titel"].'/'.$site["titel"].'/">'.$site["titel"].'</a></h2>'.cut_txt(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $site["text"])),200).'...<br/><br/>';
	}
	else
	echo '<h2><a href="'.$URL_ROOT.$site["titel"].'/">'.$site["titel"].'</a></h2>'.cut_txt(strip_tags(preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $site["text"])),200).'...<br/><br/>';
}
?>
