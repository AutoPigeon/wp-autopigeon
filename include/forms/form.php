<?php

class AP_Form{
    protected $method;
    protected $action;
    protected $page;
    
    public function __construct($method, $action, $page, $nonce_name){
        $this->method = $method;
        $this->action = $action;
        $this->page = $page;
        $this->nonce_name = $nonce_name;   
    }
    protected function open_form(){
        ?>
            <form action='<?php echo esc_html($this->action) ?>' method='<?php echo esc_html($this->method)?>'>
        <?php
    }
    protected function close_form(){
        ?>
        </form>
        <?php
    }
    protected function render_nonce(){
        wp_nonce_field($this->nonce_name,  "_wpnonce");
    }
    protected function build(){

    }
    public function render(){
        $this->open_form();
        $this->render_nonce();
        $this->build();
        $this->close_form();
    }
}


?>