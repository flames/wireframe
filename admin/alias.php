<?php
	include("includes/general.inc.php");
	$sites = $DB->select("SELECT * FROM wf_sites");
	foreach ($sites as $site){
		$alias = str_replace("%2F","//",urlencode($site["titel"]));
		$counter = $DB->affected_query("SELECT id FROM wf_sites WHERE `alias` LIKE '$alias';");
		if($counter) $alias = $alias."_".$counter;
		$DB->query("UPDATE wf_sites SET alias = '$alias' WHERE id = ".$site["id"]." LIMIT 1");
		echo $alias."<br/>";
	}
?>