<?php
$time = new time($LANG["time"]);
if($_GET["rss"]){
	ob_clean();
	if($_GET["limit"]) $limit = "LIMIT ".$_GET["limit"];
	$dates = $DB->select("SELECT d.id, d.update, d.editor, d.old_link , d.headline as name, d.short_text, d.text, d.from, d.to, c.old_link as feed_link, c.name as feed, c.desc FROM wf_calendar_dates as d LEFT JOIN wf_calendar as c ON (c.id = d.feed_id) WHERE `to` >= NOW() AND d.status = 1 AND d.feed_id = ".$page["view_options"]." ORDER BY `from` $limit;");
	$output = '<?xml version="1.0" encoding="utf-8"?>
<!-- generator="Wireframe CMS" -->
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>'.$dates[0]["feed"].'</title>
		<description><![CDATA['.$dates[0]["desc"].']]></description>
		<link>'.$URL_ROOT.'Messen/</link>
		<lastBuildDate>'.date("D, d M Y H:i:s +0000").'</lastBuildDate>
		<generator>Wireframe CMS</generator>
		<language>'.$LANG["language"]["locale"].'</language>
	';
	foreach($dates as $date){
		$output .= '
		<item>
			<title>'.$date["name"].'</title>
			<link>'.$URL_ROOT.'Messen/'.$date["id"].'/'.urlencode(str_replace('/', '//', $date["name"])).'/</link>
			<guid>'.$URL_ROOT.'Messen/'.$date["id"].'/'.urlencode(str_replace('/', '//', $date["name"])).'/</guid>
			<description><![CDATA[<b>'.$time->convertDate($date["from"]).' - '.$time->convertDate($date["to"]).'</b><br/>'.$date["short_text"].']]></description>
			<author>'.$date["editor"].'</author>
			<category>'.$date["desc"].'</category>
			<pubDate>'.date("D, d M Y H:i:s +0000",strtotime($date["update"])).'</pubDate>
		</item>';
	}
	$output .= '
		</channel>
</rss>';
echo $output;
exit();
}
else{
	$last = end($content);
	$prev = prev($content);
	if(!is_numeric($prev)){
		$feed = $DB->query_fetch("SELECT * FROM wf_newsfeeds WHERE id = ".$page["view_options"]." LIMIT 1");
		$output .= $feed["text"];
		$dates = $DB->select("SELECT * FROM `wf_news` WHERE feed_id = ".$page["view_options"]." AND  status = 1 order by `update` desc");
		foreach ($dates as $date){
			$output .= '<div style="cursor:pointer; width:100%;" onclick="location=\''.$date["id"].'/'.urlencode(str_replace('/', '//', $date["headline"])).'/\'"><b>'.$date["headline"].' ('.$time->convertDate($date["update"]).')</b>
			'.$date["short_text"].'</div>';
			$i++;
			$first = FALSE;
		}
	}
	else{
		$date = $DB->query_fetch("SELECT * FROM wf_calendar_dates WHERE id = ".$prev."  AND status = 1 LIMIT 1");
		$output = '<h1>'.$date["headline"].' ('.$time->convertDate($date["from"]).' - '.$time->convertDate($date["to"]).')</h1>
		'.$date["text"];
	}
	echo $output;
}
?>