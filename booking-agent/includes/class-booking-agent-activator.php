<?php

/**
 * Fired during plugin activation
 *
 * @link       http://sidsavara.com
 * @since      1.0.0
 *
 * @package    Booking_Agent
 * @subpackage Booking_Agent/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Booking_Agent
 * @subpackage Booking_Agent/includes
 * @author     Sid Savara and The Pull Requests <sid@sidsavara.com>
 */


/** Step 1. */




class Booking_Agent_Activator {

	static function add_booking_agent_roles_on_plugin_activation() {
		add_role(   'visitor_role',
			'Visitor',
			array(  'read' => true,
			        'level_0' => true )
		);

		add_role(   'scheduler_role',
			'Scheduler',
			array( 'read' => true,
			       'list_users' => true,
			       'add_users' => true,
			       'create_users' => true,
			       'delete_users' => true,
			       'promote_users' => true,
			       'edit_users	' => true,
			       'list_users' => true

			)
		);

		/*
		 * 'edit_pages' => true,
			       'edit_others_pages' => true,
			       'edit_published_pages' => true,
			       'publish_pages' => true,
			       'delete_pages' => true,
			       'delete_others_pages' => true,
			       'delete_published_pages' => true,
		 */



			// gets the author role
		Booking_Agent_Activator::add_staff_capabilities_to_role( 'scheduler_role' );
		Booking_Agent_Activator::add_staff_capabilities_to_role( 'administrator' );

		Booking_Agent_Activator::add_visitor_capabilities_to_role('visitor_role');


	}


	static function add_visitor_capabilities_to_role($role_name){
		// gets the author role
		$scheduler_role = get_role( $role_name );

		// This only works, because it accesses the class instance.
		// would allow the author to edit others' posts for current theme only
		$scheduler_role->add_cap( 'visitor_schedule_visit_capability' );

	}

	static function add_staff_capabilities_to_role($role_name){
		// gets the author role
		$scheduler_role = get_role( $role_name );

		// This only works, because it accesses the class instance.
		// would allow the author to edit others' posts for current theme only
		$scheduler_role->add_cap( 'manage_visitation_calendar_capability' );

	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/** Step 2 (from text above). */


		Booking_Agent_Activator::add_booking_agent_roles_on_plugin_activation();

	}

}
