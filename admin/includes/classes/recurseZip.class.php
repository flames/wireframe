<?php
/*
Copyright (c) 2011 http://ramui.com. All right reserved.
This product is protected by copyright and distributed under licenses restricting copying, distribution. Permission is granted to the public to download and use this script provided that this Notice and any statement of authorship are reproduced in every page on all copies of the script.
*/
class recurseZip
{
private function recurse_zip($src,&$zip,$path) {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_zip($src . '/' . $file,$zip,$path);
                }
                else {
                    $zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
                }
            }
        }
        closedir($dir);
}

public function decompress($src,$dst=''){
 while ($zip_entry = zip_read($src)) {
    $fp = fopen($dst.zip_entry_name($zip_entry), "w");
    if (zip_entry_open($zip, $zip_entry, "r")) {
      $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
      fwrite($fp,"$buf");
      zip_entry_close($zip_entry);
      fclose($fp);
    }
  }
  zip_close($zip);
}

public function compress($src,$dst='')
{
		if(substr($src,-1)==='/'){$src=substr($src,0,-1);}
		if(substr($dst,-1)==='/'){$dst=substr($dst,0,-1);}
        $path=strlen(dirname($src).'/');
        $filename=substr($src,strrpos($src,'/')+1).'.zip';
		$dst=empty($dst)? $filename : $dst.'/'.$filename;
		@unlink($dst);
        $zip = new ZipArchive;
        $res = $zip->open($dst, ZipArchive::CREATE);
        if($res !== TRUE){
                echo 'Error: Unable to create zip file';
                exit;}
        if(is_file($src)){$zip->addFile($src,substr($src,$path));}
        else{
                if(!is_dir($src)){
                     $zip->close();
                     @unlink($dst);
                     echo 'Error: File not found';
                     exit;}
        $this->recurse_zip($src,$zip,$path);}
        $zip->close();
        return $dst;
}
}
?>