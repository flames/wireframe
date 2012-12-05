<?php
$sites = $DB->select("SELECT * FROM wf_sites WHERE parent = 0;");
echo '<ul id="sitemap">';
foreach ($sites as $site){
	$childs = $DB->select("SELECT * FROM wf_sites WHERE parent = ".$site["id"].";");
	echo '<li><h2><a href="'.$URL_ROOT.$site["titel"].'">'.str_replace("%2F","//",urlencode($site["titel"])).'</a></h2>';
	if($childs){
		echo "<ul>";
		$parent = $DB->query_fetch("SELECT * FROM wf_sites WHERE id = ".$site["parent"]." LIMIT 1 ;");
		foreach ($childs as $child){
			echo '<li><h2><a href="'.$URL_ROOT.str_replace("%2F","//",urlencode($site["titel"])).'/'.str_replace("%2F","//",urlencode($child["titel"])).'">'.$child["titel"].'</a></h2></li>';
		}
		echo "</ul>";
	}
	echo "</li>";
}
echo "</ul>";
?>
