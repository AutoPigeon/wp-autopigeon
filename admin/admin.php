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
            require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "admin/pages/dashboard.php");
        }
        else{
            $this->admin_page_integrate();
        }
    }
    
    public function admin_page_settings(){
        if ($this->is_integrated()){
            require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "admin/pages/settings.php");
        }
        else{
            $this->admin_page_integrate();
        }
    }
    public function admin_page_events(){
        if ($this->is_integrated()){
            require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "admin/pages/events.php");
            $page = new AP_Events_Page();
        }
        else{
            $this->admin_page_integrate();
        }
    }

    public function is_integrated(){
        $integration_key = get_option('ap_auth_token');
        if ($integration_key == ''){
            return false;
        }
        return true;
    }
    public function register_menu(){
        add_menu_page(
            'Dashboard',
            "AutoPigeon",
            'exist',
            'ap_dashboard',
            [&$this, 'admin_page_dashboard']
        );
        add_submenu_page(
            null,
            "AutoPigeon Integrate",
            "AutoPigeon Integrate",
            "manage_options",
            "ap_integrate",
            [&$this, "admin_page_integrate"]
        );
        if ($this->is_integrated()){
            add_submenu_page(
                "ap_dashboard",
                "Events",
                "Events",
                "exist",
                "ap_events",
                [&$this, 'admin_page_events']
            );
            add_submenu_page(
                "ap_dashboard",
                "Settings",
                "Settings",
                "exist",
                "ap_settings",
                [&$this, 'admin_page_settings']
            );
        }
        
    }

}


?>