<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://sidsavara.com
 * @since      1.0.0
 *
 * @package    Booking_Agent
 * @subpackage Booking_Agent/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Booking_Agent
 * @subpackage Booking_Agent/includes
 * @author     Sid Savara and The Pull Requests <sid@sidsavara.com>
 */
class Booking_Agent_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		Booking_Agent_Deactivator::remove_booking_agent_roles_on_plugin_deactivation();
	}

	function remove_booking_agent_roles_on_plugin_deactivation() {
		if ( get_role( 'visitor_role' ) ) {
			remove_role( 'visitor_role' );
		}
		if ( get_role( 'scheduler_role' ) ) {
			remove_role( 'scheduler_role' );
		}
	}

}


