<?php

class AP_Security{
    static public function clean_input($input){
        $i = trim($input);
        return $i;
    }
    
    static public function clean_input_for_output(){
        $i = htmlspecialchars($input);
        return $i;
    }
}
