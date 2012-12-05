<?php
$dates = $DB->select("SELECT * FROM `wf_calendar_dates` WHERE `status` = 1 AND `to` >= '".date("Y-m-d")."' ORDER BY `from` ASC LIMIT 3;");
foreach ($dates as $date){
    $featured .= '
        <div class="featured_div">
            <b>'.$date["headline"].'<br/>
            '.date("d.m",strtotime($date["from"])).' - '.date("d.m.Y",strtotime($date["to"])).'
            </b><br/>
            '.$date["short_text"].'
        </div>
        ';
}
$featured .= '
<div style="clear:both"></div>';
echo $featured;
?>