<?php


class AP_Utils{

   
    static function wp_set_option($option, $value){
        if (get_option($option) == FALSE){
            add_option($option, $value);
        }
        else{
            update_option($option, $value);
        }
    }
    static function get_action(){
        if (isset($_GET["action"]) && !empty($_GET["action"])){
            return $_GET["action"];
        }
        else{
            return null;
        }
    }
    static function get_form(){
        if (isset($_GET["form"]) && !empty($_GET["form"])){
            return $_GET["form"];
        }
        else{
            return null;
        }
    }
    static function check_post_field($field){
        if (isset($_POST[$field]) && !empty($_POST[$field])){
            return true;
        }
        else{
            return false;
        }
    }
    static function check_get_field($field){
        if (isset($_GET[$field]) && !empty($_GET[$field])){
            return true;
        }
        else{
            return false;
        }
    }
    static function js_redirect($page){
        ?>
        <script>
            window.location = "<?php echo esc_html($page); ?>";
            </script>
        <?php
    }
    static function dot_long_string($string, $limit) {
        $repl = "...";
        if(strlen($string) > $limit) 
        {
            return substr($string, 0, $limit) . $repl; 
        }
        else 
        {
            return $string;
        }
    }
    static function verify_nonce($field, $name){
        if ( ! isset( $_REQUEST[$field] ) || ! wp_verify_nonce( $_POST[$field], $name)) {
            return false;
        } 
        else {
            return true;
        }
    }

    
}

?>