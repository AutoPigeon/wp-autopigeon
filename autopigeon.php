<?php
/**
 * Plugin Name:     WP AutoPigeon
 * Plugin URI:      https://github.com/AutoPigeon/wp-autopigeon
 * Description:     AutoPigeon Integration Plugin For Wordpress
 * Version:         1.0.0
 * Author:          AutoPigeon Team
 * License:         GPL-2.0+
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:     AutoPigeon
 * Domain Path:     /languages
 */

require_once dirname(__FILE__) . "/config.php";
define( 'AUTOPIGEON_PLUGIN_DIRECTORY' , plugin_dir_path( __FILE__ ));

require_once( AUTOPIGEON_PLUGIN_DIRECTORY . "admin/admin.php");

function activate_autopigeon(){
    require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/activate.php");
    $activator = new AutoPigeon_Activate();
    $activator->activate();
}
function deactivate_autopigeon(){
    require_once(AUTOPIGEON_PLUGIN_DIRECTORY . "include/deactivate.php");
    $deactivator = new AutoPigeon_Deactivate();
    $deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_autopigeon' );
register_deactivation_hook( __FILE__, 'deactivate_autopigeon' );



function callback($buffer){
    return $buffer;
}

function add_ob_start(){
    ob_start("callback");
}

function flush_ob_end(){
    ob_end_flush();
}

add_action('init', 'add_ob_start');
add_action('wp_footer', 'flush_ob_end');


class AutoPigeon{
    protected $admin;

    public function __construct(){
        $this->admin = new AutoPigeon_Admin();
    }
}




$autopigeon = new AutoPigeon();

?>
