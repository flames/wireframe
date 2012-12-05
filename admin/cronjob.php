<?php
include_once "includes/general.inc.php";
print "<pre>\n";
print "<---Starting Crons--->\n";
	if ($handle = opendir($DIR_ROOT.'/admin/crons')) {
		while (false !== ($file = readdir($handle))) {
			if (preg_match("/.*\.php/", $file, $hit)) {
                                $name = preg_replace('/\.php/', '', $hit[0]);
                                print "    <---Starting ".$name."--->\n";
                                $DB = new db();
                                include_once($DIR_ROOT."/admin/crons/".$file);
                                print "    <---".$name." finished--->\n";
			}
		}
		closedir($handle);
	}
print "<---Crons finished--->\n";
print "</pre>\n";
?>
