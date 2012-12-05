<?php 
require("../../../includes/general.inc.php");
$kmc = new folderant();
$ids = $kmc->search($_GET["text"],$_GET["fulltext"], $_GET["tags"]);
$results = $DB->select("
	SELECT q1.id, q1.label, COUNT(q2.id) as ammount
		FROM `wf_folderant_files` AS q1 
		LEFT JOIN `wf_folderant_files` AS q2 
		ON (q1.id=q2.id) WHERE
		q1.id in (".implode(",", $ids).")
		GROUP BY q1.label 
		LIMIT 10;",MYSQLI_ASSOC,FALSE,"id");
$output_array = array();
foreach($ids as $id){
	if(isset($results[$id])) $output_array[] = array($results[$id]["label"] => $results[$id]["label"].' ('.$results[$id]["ammount"].')');
}
echo json_encode($output_array);
?>