<?php
/*
 * jQuery File Upload Plugin PHP Class 5.11.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

class Upload extends UploadHandler
{
    protected $options;

    function __construct($options=null) {
        parent::__construct($options);
        $this->db       =  $_SESSION["_registry"]["db"];
    }

    public function get() {
        $files = $this->db->select_single("SELECT `file` FROM wf_media WHERE `table`='".$_REQUEST["table"]."' AND `item_id` ='".$_REQUEST["id"]."' AND `type` ='".$_REQUEST["type"]."';");
        foreach($files as $file){
            $file_name = isset($file) ?
                basename(stripslashes($file)) : null;
                $info[] = $this->get_file_object($file_name);
        }

        header('Content-type: application/json');
        echo json_encode($info);
    }

    public function post() {
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete();
        }
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
        $info = array();
        if ($upload && is_array($upload['tmp_name'])) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
                $info[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    isset($_SERVER['HTTP_X_FILE_NAME']) ?
                        $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'][$index],
                    isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                        $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'][$index],
                    isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                        $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                    $upload['error'][$index],
                    $index
                );
            }
        } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            $info[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                isset($_SERVER['HTTP_X_FILE_NAME']) ?
                    $_SERVER['HTTP_X_FILE_NAME'] : (isset($upload['name']) ?
                        $upload['name'] : null),
                isset($_SERVER['HTTP_X_FILE_SIZE']) ?
                    $_SERVER['HTTP_X_FILE_SIZE'] : (isset($upload['size']) ?
                        $upload['size'] : null),
                isset($_SERVER['HTTP_X_FILE_TYPE']) ?
                    $_SERVER['HTTP_X_FILE_TYPE'] : (isset($upload['type']) ?
                        $upload['type'] : null),
                isset($upload['error']) ? $upload['error'] : null
            );
        }
        header('Vary: Accept');
        $json = json_encode($info);
        $redirect = isset($_REQUEST['redirect']) ?
            stripslashes($_REQUEST['redirect']) : null;
        if ($redirect) {
            header('Location: '.sprintf($redirect, rawurlencode($json)));
            return;
        }
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
       if(!isset($info[0]->error))
       $this->db->query("  INSERT INTO  `wf_media` (`id` ,`table` ,`item_id` ,`type` ,`file`)
                           VALUES                  (NULL ,'".$_REQUEST["up_table"]."','".$_REQUEST["up_item_id"]."','".$_REQUEST["up_type"]."','".$info[0]->name."');
                        ");
        echo $json;
    }

    public function copy_file($uploaded_file, $name, $size, $type, $index) {
        $file = new stdClass();
        $file->name = $this->trim_file_name($name, $type, $index);
        $file->size = intval($size);
        $file->type = $type;
            $file_path = $this->options['upload_dir'].$file->name;
            if ($uploaded_file && is_file($uploaded_file)) {
                copy($uploaded_file, $file_path);
            } 

            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                if ($this->options['orient_image']) {
                    $this->orient_image($file_path);
                }
                $file->url = $this->options['upload_url'].rawurlencode($file->name);
                foreach($this->options['image_versions'] as $version => $options) {
                    if ($this->create_scaled_image($file->name, $options)) {
                        if ($this->options['upload_dir'] !== $options['upload_dir']) {
                            $file->{$version.'_url'} = $options['upload_url']
                                .rawurlencode($file->name);
                        } else {
                            clearstatcache();
                            $file_size = filesize($file_path);
                        }
                    }
                }
            }
            $file->size = $file_size;
        return $file;
    }

    public function delete() {
        $file_name = isset($_REQUEST['file']) ?
            basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $this->options['upload_dir'].$file_name;
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach($this->options['image_versions'] as $version => $options) {
                $file = $options['upload_dir'].$file_name;
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        $this->db->query("  DELETE FROM  `wf_media` WHERE file = '$file_name' LIMIT 1;");
        header('Content-type: application/json');
        echo json_encode($success);
    }
    public function direct_upload($index,$conf){
        if($_FILES["save"]['tmp_name'][$index]){
            $file = $this->handle_file_upload($_FILES["save"]['tmp_name'][$index], $_FILES["save"]['name'][$index], $_FILES["save"]['size'][$index], $_FILES["save"]['type'][$index],"","");
            $this->db->query("  INSERT INTO  `wf_media` (`id` ,`table` ,`item_id` ,`type` ,`file`)
                           VALUES                  (NULL ,'".$conf["table"]."','".$conf["item_id"]."','".$index."','".$file->name."');
            ");
        }
    }

    public function create_scaled_image($file_name, $options) {   
        $file_path = $this->options['upload_dir'].$file_name;
        $destination_file = $options['upload_dir'].$file_name;
        $new_file_path = $options['upload_dir'].$file_name;
        $square_size = $options['max_width'];
        list($img_width, $img_height) = @getimagesize($file_path);

        $original_width = $img_width;    
        $original_height = $img_height;
        
        if($original_width > $original_height){
            $new_height = $square_size;
            $new_width = $new_height*($original_width/$original_height);
        }
        if($original_height > $original_width){
            $new_width = $square_size;
            $new_height = $new_width*($original_height/$original_width);
        }
        if($original_height == $original_width){
            $new_width = $square_size;
            $new_height = $square_size;
        }
        
        $new_width = round($new_width);
        $new_height = round($new_height);

        $smaller_image = imagecreatetruecolor($new_width, $new_height);
        $square_image = imagecreatetruecolor($square_size, $square_size);

        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $original_image = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($smaller_image, 0, 0, 0));
                @imagecolortransparent($new_img, @imagecolorallocate($square_image, 0, 0, 0));
                $original_image = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($smaller_image, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($smaller_image, false);
                @imagesavealpha($smaller_image, true);
                @imagecolortransparent($square_image, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($square_image, false);
                @imagesavealpha($square_image, true);
                $original_image = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        
        imagecopyresampled($smaller_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        
        if($new_width>$new_height){
            $difference = $new_width-$new_height;
            $half_difference =  round($difference/2);
            imagecopyresampled($square_image, $smaller_image, 0-$half_difference+1, 0, 0, 0, $square_size+$difference, $square_size, $new_width, $new_height);
        }
        if($new_height>$new_width){
            $difference = $new_height-$new_width;
            $half_difference =  round($difference/2);
            imagecopyresampled($square_image, $smaller_image, 0, 0-$half_difference+1, 0, 0, $square_size, $square_size+$difference, $new_width, $new_height);
        }
        if($new_height == $new_width){
            imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $square_size, $square_size, $new_width, $new_height);
        }
        

        // if no destination file was given then display a png      
        if(!$destination_file){
            imagepng($square_image,NULL,9);
        }
        
        // save the smaller image FILE if destination file given
        if(substr_count(strtolower($destination_file), ".jpg")){
            $success = imagejpeg($square_image,$destination_file,100);
        }
        if(substr_count(strtolower($destination_file), ".gif")){
            $success = imagegif($square_image,$destination_file);
        }
        if(substr_count(strtolower($destination_file), ".png")){
            $success = imagepng($square_image,$destination_file,9);
        }

        imagedestroy($original_image);
        imagedestroy($smaller_image);
        imagedestroy($square_image);
        return $success;
    }
}
?>