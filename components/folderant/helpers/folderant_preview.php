<?php
	ob_start();
    require("../../../includes/general.inc.php");
    $kmc = new folderant();
    $file = $kmc->get_file_info($_GET["id"]);
    if(!is_file("../uploads/folderant_thumbs/".$file["id"].".jpg")){
    	$image_tmp = new ImageEditor();
    	if($image_tmp->loadImageFile($file["fullpath"])){
                        $thumb = new ImageEditor();
                        $thumb->createCanvas(100, 100);
                        $thumb->fillin($image_tmp);     
						ob_clean();
                        $thumb->displayImage(ImageEditor::JPG); 
                        $thumb->writeImageFile( "../uploads/folderant_thumbs/".$file["id"].".jpg",ImageEditor::JPG);
						ob_end_flush();
						die;
    	}

    	elseif($file["filetype"] == "pdf"){
    	$im = new imagick($file["fullpath"].'[0]');
		$im->setImageFormat( "jpg" );
		$im->scaleImage ( 80 , 0);
		$im->writeImage ( "../../../uploads/folderant_thumbs/".$file["id"].".jpg");
		ob_clean();
		header( "Content-Type: image/jpeg" );
		echo $im;
		ob_end_flush();
		die;
    	}

    	//elseif($file["filetype"] == "tif"){
    	//	$image = exif_thumbnail($file["fullpath"], $width, $height, $type);
		//
		//	ob_clean();
    	//	header('Content-type: ' .image_type_to_mime_type($type));
    	//	echo $image;
		//	ob_end_flush();
    	//  die;

    	//}
    	//elseif ($file["filetype"] =="cdr")
    	//{ 
    		//$zip = new recurseZip();
    		//$target = "../uploads/folderant_thumbs/".$file["id"]."_tmp.bmp";
    		//$zip->decompress(fopen($file["fullpath"],w),"../uploads/folderant_thumbs/tmp");
        	//ob_clean();
            //$image_tmp = new ImageEditor();
            //$image_tmp->loadImageFile($target);
            //$thumb = new ImageEditor();
            //$thumb->createCanvas(100, 100);
            //$thumb->fillin($image_tmp);   
            //$thumb->displayImage(ImageEditor::JPG); 
            //$thumb->writeImageFile( "../uploads/folderant_thumbs/".$file["id"].".jpg",ImageEditor::JPG);
            //unset($target);
			//ob_end_flush();
			//die;
    	//} 
    //}
    else  	{
        if(in_array($file["filetype"], array("cdr","eps","tif","psd","ai"))) $image = @imagecreatefromgif($DIR_ROOT."/js/treeview/filetypes/pic.gif");          
        else $image = @imagecreatefromgif($DIR_ROOT."/js/treeview/filetypes/".$file["filetype"].".gif");
        ob_clean();
        header('Content-type: image/gif');
        imagegif($image);
        ob_end_flush();
        die;
    }
}
    $image = new ImageEditor();
    $image->loadImageFile("../../../uploads/folderant_thumbs/".$file["id"].".jpg");
    ob_clean();
    $image->displayImage(ImageEditor::JPG); 
    ob_end_flush();
?>