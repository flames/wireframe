<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of time
 *
 * @author Abadon
 */
class time {
    private $lang;
    function __construct($lang = false){
        if($lang) $this->lang = $lang;
        else $this->lang = $_SESSION["_registry"]["lang"]["time"];
    }    
    function getFormat_calendar(){
        return $this->lang["date_format_calendar"];
    } 
    
    function convertTime($time = FALSE, $to = FALSE){
        if(!$to) $to = $this->lang["time_format"];
        $time = strtotime($time);
        return date($to, $time);
    } 
    
    function convertDate($time = FALSE, $to = FALSE){
        if(!$to) $to = $this->lang["date_format"];
        $time = strtotime($time);
        return date($to, $time);
    }
    
    function convertDateTime($time = FALSE, $to = FALSE){
        if(!$to) $to = $this->lang["datetime_format"];
        $time = strtotime($time);
        return date($to, $time);
    }
}

?>
