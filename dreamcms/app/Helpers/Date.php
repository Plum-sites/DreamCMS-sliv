<?php

if(!function_exists('in_datarange')){
    function in_datarange($start, $end, $time = false){
        if ($time == false) $time = time();

        if(strtotime($start) < $time && strtotime($end) > $time) return true;
        return false;
    }
}