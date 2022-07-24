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

define( 'AUTOPIGEON_VERSION' , '1.0.0');
define( 'AUTOPIGEON_DEVELOPMENT_MODE' , true);
define( 'AUTOPIGEON_PLUGIN_DIRECTORY' , plugin_dir_path( __FILE__ ));

require_once( AUTOPIGEON_PLUGIN_DIRECTORY . "admin/admin.php");

class AutoPigeon{
    protected $admin;

    public function __construct(){
        $this->admin = new AutoPigeon_Admin();
    }
}




$autopigeon = new AutoPigeon();

?>
