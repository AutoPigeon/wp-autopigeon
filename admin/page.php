<?php 
require_once (AUTOPIGEON_PLUGIN_DIRECTORY . "include/utils.php");

class AP_Page{
    public $title;
    public $action_button_title;
    public $action_button_action;
    public $action_pages;
    public $method;
    public $form_errors;

    public function setup(){
        $this->form_errors = array();
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->open_wrap();
        $this->route();
        $this->close_wrap();
    }
    public function add_form_error($form_field, $error){
        if (!isset($this->form_errors[$form_field])){
            $this->form_errors[$form_field] = array();
        }
        array_push($this->form_errors[$form_field], $error);
    }
    public function add_global_form_error($error){
        $this->add_form_error("__global__", $error);
    }

    public function get_global_errors_as_html(){
        $errors = $this->get_form_field_errors("__global__");
        $result = "<ul>";
        if ( $errors == [] ){
            return "";
        }
        else{
        
            foreach ($errors as $error ){
                $err = esc_html($error);

                $result .= "<li style='color:red;'>" . $err . "</li>";
            }
        }
        $result .= "</ul>";
        return $result;
    }
    public function get_form_field_errors_as_html($field){
        $errors = $this->get_form_field_errors($field);
        $result = "<ul>";
        if ( $errors == [] ){
            return "";
        }
        else{
        
            foreach ($errors as $error ){
                $err = esc_html($error);

                $result .= "<li><small style='color:red;'>" . $err . "</small></li>";
            }
        }
        $result .= "</ul>";
        return $result;
    }
    public function get_form_field_errors($field){
        if (isset($this->form_errors[$field])){
            return $this->form_errors[$field];
        }
        else{
            return array();
        }
    }


    public function route(){
        $action = AP_Utils::get_action();
        if ($action == null){
            $this->build_header();
            $this->default_page();
        }
        else{
            $this->action_page($action);
        }
    }

    public function open_wrap(){
        ?>
            <div class="wrap">
        <?php
    }

    public function close_wrap(){
        ?>
            </div>
        <?php
    }

    public function action_page($action){
        if (isset($this->action_pages[$action])){
            if ($this->action_pages[$action]["use_header"]){
                $this->build_header();
            }
            call_user_func($this->action_pages[$action]["function"]);
        }
        else{
            $this->build_header();
            $this->default_page();
        }
    }

    public function default_page(){
        
    }
    public function build_header(){
        ?>
        <h1 class="wp-heading-inline"><?php echo esc_html($this->title) ?></h1>
        <a class="page-title-action" href="<?php echo esc_html($this->action_button_action) ?>"><?php echo esc_html($this->action_button_title) ?></a>
        <hr class="wp-header-end">
        <?php
    }
}

?>