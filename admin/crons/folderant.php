<?php
$backup_config = $REGISTRY["ftp_config"]["backup"];
$folderant = new folderant();
if(date ("w",time()) == 5){
print "        ***Starting Folderant-Backup in ".$backup_config["root"].$newBackupDir."***\n";
$path = $folderant->ext_backup();
$folderant->make_backup();
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
$ftp->upload("./tmp/".$path.".zip",  "kmc-files.zip", 'auto', 0 );
unlink("./tmp/".$path.".zip");
print "        ***Folderant-Backup Complete***\n";
print "        ***Starting Folderant Database-Backup***\n";
$ftp->upload("./tmp/folderant.sql",  "kmc-db.sql", 'auto', 0 );
unlink("./tmp/folderant.sql");
print "        ***Folderant Database-Backup Complete***\n";
}
print "        ***Starting Folderant Update***\n";
$folderant->update_db();
print "        ***Folderant Update Complete***\n";

print "        ***Starting Folderant Article Update***\n";
$folderant->update_articles();
print "        ***Folderant Article Update Complete***\n";
?>