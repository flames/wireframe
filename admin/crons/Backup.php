<?php
$backup_config = $REGISTRY["ftp_config"]["backup"];
try
{
    $ftp = FTP::getInstance();
    $ftp->connect($backup_config, false, true );
}
catch (FTPException $error)
{
    echo $error->getMessage();
}
$ftp->changeDir($backup_config["root"]);
$newBackupDir = str_replace(array("http:","/"), "", $URL_ROOT).'_'.date("Y_m_d__H_i_s",time())."/";
$ftp->makeDir($newBackupDir);
$files = array_flip($ftp->getSimpleFileList());
unset($files["kmc-db.sql"],$files["kmc-files.zip"]);
$files = array_values(array_flip($files));
$to = count($files) - 14;
for ($i=0; $i < $to; $i++) { 
   $ftp->removeDir($files[$i],1);
}

$ftp->changeDir($backup_config["root"].$newBackupDir);
if (!$backup_config["zip"]){
    print "        ***Starting unzipped File-Backup in ".$backup_config["root"].$newBackupDir."***\n";
    $ftp->copyFolder($DIR_ROOT."/","");
    $ftp->changeDir($backup_config["root"].$newBackupDir);
}
else{
    print "        ***Starting zipped File-Backup in ".$backup_config["root"].$newBackupDir."***\n";
    $zip = new recurseZip();
    $zip->compress($DIR_ROOT,$_SESSION["_registry"]["root"]."/admin/tmp/");
    $ftp->upload($_SESSION["_registry"]["root"]."/admin/tmp/".basename($DIR_ROOT).".zip",  "files.zip", 'auto', 0 );
    unlink($_SESSION["_registry"]["root"]."/admin/tmp/".basename($DIR_ROOT).".zip");
}
print "        ***File-Backup Complete***\n";
print "        ***Starting Database-Backup***\n";
$handler = fOpen($_SESSION["_registry"]["root"]."/admin/tmp/dbDump.tmp" , "a+");
fWrite($handler,$DB->dump());
fclose($handler);
$ftp->upload($DIR_ROOT."/admin/tmp/dbDump.tmp",  "dbDump.sql", 'auto', 0 );
unlink($DIR_ROOT."/admin/tmp/dbDump.tmp");
print "        ***Database-Backup Complete***\n";
?>
