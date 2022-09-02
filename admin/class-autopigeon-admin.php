<?php
/**
 * AutoPigeon admin dashboard and functions handler
 *
 * @since 1.0.0
 * @package WP-AutoPigeon
 * @subpackage AutoPigeon Admin
 */

/**
 * Class for handling the dashboard of the plugin
 *
 * @class AutoPigeon_Admin
 * @since 1.0.0
 */
class AutoPigeon_Admin {
	/**
	 * Construct function for AutoPigeon_Admin
	 *
	 * @function __construct
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( &$this, 'register_menu' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'rest_api_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts_special' ) );
	}
	/**
	 * This function will enqueue scripts and styles, but it has some logic in it. It is also for using react
	 *
	 * @function enqueue_scripts_special
	 * @param string $suffix This is the suffix of the current admin page.
	 * @since 1.0.0
	 */
	public function enqueue_scripts_special( $suffix ) {
		$asset_file_page = AUTOPIGEON_PLUGIN_DIRECTORY . 'build/index.asset.php';
		if ( file_exists( $asset_file_page ) && 'admin_page_ap_integrate' === $suffix ) {
			$assets = require_once $asset_file_page;
			wp_enqueue_script(
				'wp-autopigeon-integration-screen-script',
				AUTOPIGEON_PLUGIN_URL . 'build/index.js',
				$assets['dependencies'],
				$assets['version'],
				true
			);
			wp_enqueue_style( 'font-awesome', AUTOPIGEON_PLUGIN_URL . 'assets/fontawesome/css/fontawesome.css', array(), AUTOPIGEON_VERSION );
			wp_enqueue_style( 'font-awesome-solid', AUTOPIGEON_PLUGIN_URL . 'assets/fontawesome/css/solid.css', array(), AUTOPIGEON_VERSION );
			wp_enqueue_style( 'font-awesome-regular', AUTOPIGEON_PLUGIN_URL . 'assets/fontawesome/css/regular.css', array(), AUTOPIGEON_VERSION );
			wp_enqueue_style( 'wp-autopigeon-login', AUTOPIGEON_PLUGIN_URL . 'assets/css/login.css', array(), AUTOPIGEON_VERSION );

			foreach ( $assets['dependencies'] as $style ) {
				wp_enqueue_style( $style );
			}
		}
	}
	/**
	 * Register all admin settings
	 */
	public function register_settings() {
		register_setting(
			'wp-autopigeon',
			'wp-autopeon-integration-token',
			array(
				'type'              => 'string',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		register_setting(
			'wp-autopigeon',
			'wp-autopeon-auth-token',
			array(
				'type'              => 'string',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		register_setting(
			'wp-autopigeon',
			'wp-autpigeon-testing',
			array(
				'type'          => 'object',
				'default'       => array(
					'auth-token'           => null,
					'integration-token'    => null,
				),
				'show_in_rest' => array(
					'schema' => array(
						'type'  => 'object',
						'properties' => array(
							'auth-token' => array(
								'type' => 'string',
							),
							'integration-token' => array(
								'type' => 'integer',
							),
						),
					),
				),
			),
		);
	}
	/**
	 * Enqueue all styles for admin pages
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'ap_main_styles', plugin_dir_url( __FILE__ ) . 'css/main.css', array(), AUTOPIGEON_VERSION );
	}
	/**
	 * Enqueue all scripts for admin pages
	 *
	 * @function enqueue_scripts
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {}
	/**
	 * Admin page integrate
	 *
	 * @function admin_page_integrate
	 * @since 1.0.0
	 */
	public function admin_page_integrate() {
		require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'admin/pages/integrate.php';
	}
	/**
	 * The dashboard page for autopigeon
	 *
	 * @function admin_page_dashboard
	 * @since 1.0.0
	 */
	public function admin_page_dashboard() {
		if ( $this->is_integrated() ) {
			require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'admin/pages/dashboard.php';
		} else {
			$this->admin_page_integrate();
		}
	}
	/**
	 * Admin settings page
	 *
	 * @function admin_page_settings
	 * @since 1.0.0
	 */
	public function admin_page_settings() {
		require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'admin/pages/settings.php';
	}
	/**
	 * Admin events page
	 *
	 * @function admin_page_events
	 * @since 1.0.0
	 */
	public function admin_page_events() {
		if ( $this->is_integrated() ) {
			require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'admin/pages/events.php';
			$page = new AP_Events_Page();
		} else {
			$this->admin_page_integrate();
		}
	}
	/**
	 * Check if the current user is integrated
	 *
	 * @function is_integrated
	 * @since 1.0.0
	 */
	public function is_integrated() {
		$integration_key = get_option( 'ap_auth_token' );
		if ( '' === $integration_key ) {
			return false;
		} else {
			return true;
		}
	}
	/**
	 * Register all pages and setup admin menu
	 *
	 * @function register_menu
	 * @since 1.0.0
	 */
	public function register_menu() {
		add_menu_page(
			'Dashboard',
			'AutoPigeon',
			'exist',
			'ap_dashboard',
			array( &$this, 'admin_page_dashboard' )
		);
		add_submenu_page(
			null,
			'AutoPigeon Integrate',
			'AutoPigeon Integrate',
			'manage_options',
			'ap_integrate',
			array( &$this, 'admin_page_integrate' )
		);
		if ( $this->is_integrated() ) {
			add_submenu_page(
				'ap_dashboard',
				'Events',
				'Events',
				'exist',
				'ap_events',
				array( &$this, 'admin_page_events' )
			);
			add_submenu_page(
				'ap_dashboard',
				'Settings',
				'Settings',
				'exist',
				'ap_settings',
				array( &$this, 'admin_page_settings' ),
			);
		}
	}
}
