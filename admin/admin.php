<?php

class AutoPigeon_Admin{
    public function __construct(){
        add_action( 'admin_enqueue_scripts', [&$this, 'enqueue_styles'] );
        add_action('admin_menu', [$this, 'register_menu'], 0);
    }

    public function enqueue_styles(){
        wp_enqueue_style('bootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css');
    }

    public function admin_page_integrate(){
        require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "admin/pages/integrate.php");
    }
    public function admin_page_dashboard(){
        if ($this->is_integrated()){
            echo "yo";
        }
        else{
            $this->admin_page_integrate();
        }
    }

    public function is_integrated(){
        $integration_key = get_option('autopigeon_integration_key');
        if ($integration_key == ''){
            return false;
        }
        return true;
    }
    public function register_menu(){
        add_menu_page(
            'dashboard',
            "AutoPigeon",
            'exist',
            'autopigeon',
            [&$this, 'admin_page_dashboard']
        );
    }

}


?>