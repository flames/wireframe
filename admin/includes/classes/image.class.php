<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of image
 *
 * @author Abadon
 */
class image {
    private $base_root;
    
    function __construct(){
        $this->base_root =  $_SESSION["_registry"]["system_config"]["site"]["base_url"];
    }
    
    public function get_image($image,$style=FALSE,$id=''){
        $image = unserialize($image);
        $html = '<img src="'.$this->base_root.'uploads/'.$image[0].'" alt="'.$image[2].'" style="'.$style.'" id="'.$id.'" />';
        return $html;
    }    
    
    public function get_thumb($image,$style=FALSE,$id=FALSE){
        $image = unserialize($image);
        $html ='
        <a href="'.$this->base_root.'uploads/'.$image[0].'" rel="shadowbox" title="'.$image[2].'">
            <img src="'.$this->base_root.'uploads/'.$image[1].'" alt="'.$image[2].'" style="'.$style.'" id="'.$id.'" />
        </a>';
        return $html;
    }   
    
    public function get_gallery_thumb($image,$gallery,$style=FALSE){
        $image = unserialize($image);
        $html ='
        <a class="'.$gallery.'-gallery" href="'.$this->base_root.'uploads/'.$image[0].'" title="'.$image[2].'">
            <img src="'.$this->base_root.'uploads/'.$image[1].'" alt="'.$image[2].'" style="'.$style.'" />
        </a>';
        return $html;
    } 
    
    public function get_clearthumb($image,$style=FALSE){
        $image = unserialize($image);
        $html ='<img src="'.$this->base_root.'uploads/'.$image[1].'" alt="'.$image[2].'" style="'.$style.'" />';
        return $html;
    }
        
    public function get_imageURL($image){
        $image = unserialize($image);
        $html = $this->base_root.'uploads/'.$image[0];
        return $html;
    }    
    
    public function get_thumbURL($image){
        $image = unserialize($image);
        $html = $this->base_root.'uploads/'.$image[1];
        return $html;
    } 
    
    public function get_alt($image){
        $image = unserialize($image);
        $html = $image[2];
        return $html;
    }
    
    public function is_set($image){
        $image = unserialize($image);
        if ($image[0] != "")return TRUE;
        else return FALSE;
    }    
    public function get_ext($image){
        $image = unserialize($image);
                if(preg_match('/(jpg|jpeg)$/i', $image[0])){
                            $ext = ".jpg";
                }
                elseif(preg_match('/(png)$/i', $image[0])){
                            $ext = ".png";
                }
                elseif(preg_match('/(gif)$/i', $image[0])){
                            $ext = ".gif";
                }
        return $ext;
    }
    
}

?>
