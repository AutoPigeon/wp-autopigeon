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
 *
 * @package WP-AutoPigeon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


define( 'AUTOPIGEON_PLUGIN_DIRECTORY', plugin_dir_path( __FILE__ ) );
define( 'AUTOPIGEON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'AUTOPIGEON_VERSION', '1.0.0' );
define( 'AUTOPIGEON_DEVELOPMENT_MODE', true );
define( 'AUTOPIGEON_API_DOMAIN', 'http://192.168.0.191:8000/' );
define( 'AUTOPIGEON_DASHBOARD_DOMAIN', 'http://192.168.0.191:3000/' );

require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'admin/class-autopigeon-admin.php';

/**
 * Function for activating the plugin
 *
 * @function activate_autopigeon
 * @since 1.0.0
 */
function activate_autopigeon() {
	require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'include/class-autopigeon-activate.php';
	$activator = new AutoPigeon_Activate();
	$activator->activate();
}

/**
 * Function for deactivating the plugin
 *
 * @function deactivate_autopigeon
 * @since 1.0.0
 */
function deactivate_autopigeon() {
	require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'include/class-autopigeon-deactivate.php';
	$deactivator = new AutoPigeon_Deactivate();
	$deactivator->deactivate();
}

register_activation_hook( __FILE__, 'activate_autopigeon' );
register_deactivation_hook( __FILE__, 'deactivate_autopigeon' );

/**
 * Main class with all functions for setting up the plugin
 *
 * @class AutpPigeon
 * @since 1.0.0
 */
class AutoPigeon {
	/**
	 * Store the admin object here
	 *
	 * @var $admin AutoPigeon_Admin
	 * @since 1.0.0
	 */
	protected $admin;

	/**
	 * Setup all the ajax routes for admin
	 *
	 * @function setup_ajax_routes
	 * @since 1.0.0
	 */
	public function setup_ajax_routes() {
		add_action( 'wp_ajax_ap_integrate', array( &$this, 'ajax_endpoint_integrate' ) );
	}

	/**
	 * Ajax route endpoint for integrating
	 *
	 * @function ajax_endpoint_integrate
	 * @since 1.0.0
	 */
	public function ajax_endpoint_integrate() {

		check_admin_referer( 'ap_integrate', '_wpnonce' );

		$auth_token = $_POST['auth_token'] ?? '';

		$auth_token = sanitize_key( $auth_token );
		$site_url   = sanatize_url( get_site_url() );

		$api_response = wp_remote_post(
			AUTOPIGEON_API_DOMAIN . 'integration/new/',
			array(
				'method'  => 'POST',
				'headers' => array(
					'Authorization' => 'Token ' . $auth_token,
				),
				'body'   => array(
					'platform' => 'wordpress',
					'url'      => $site_url,
				),
			),
		);
		echo wp_json_encode( $api_response );

		wp_die();
	}

	/**
	 * Constuctor function for AutoPigeon class
	 *
	 * @function __construct
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->setup_ajax_routes();
		$this->admin = new AutoPigeon_Admin();

	}
}

$autopigeon = new AutoPigeon();
