<?php
/**
 * This file is for handling activation of the plugin
 *
 * @package WP-AutoPigeon
 * @subpackage AutoPigeon Activate
 * @since 1.0.0
 */

/**
 * Class to handle activating the plugin
 *
 * @class AutoPigeon_Activate
 * @since 1.0.0
 */
class AutoPigeon_Activate {
	/**
	 * Activates the plugin
	 *
	 * @function activate
	 * @since 1.0.0
	 */
	public function activate() {
		$this->create_event_tables();

	}
	/**
	 * Setup the database for events
	 *
	 * @function create_event_tables
	 * @since 1.0.0
	 */
	private function create_event_tables() {
		require_once AUTOPIGEON_PLUGIN_DIRECTORY . 'include/event.php';
		AP_Events::instance()->db_create_tables();
	}
}
