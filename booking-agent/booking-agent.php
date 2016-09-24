<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://sidsavara.com
 * @since             1.0.0
 * @package           Booking_Agent
 *
 * @wordpress-plugin
 * Plugin Name:       Booking Agent
 * Plugin URI:        http://sidsavara.com
 * Description:       Booking Agent to assist OCCC.
 * Version:           1.0.0
 * Author:            Sid Savara and The Pull Requests
 * Author URI:        http://sidsavara.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       booking-agent
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-booking-agent-activator.php
 */
function activate_booking_agent() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-agent-activator.php';
	Booking_Agent_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-booking-agent-deactivator.php
 */
function deactivate_booking_agent() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-agent-deactivator.php';
	Booking_Agent_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_booking_agent' );
register_deactivation_hook( __FILE__, 'deactivate_booking_agent' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-booking-agent.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_booking_agent() {

	$plugin = new Booking_Agent();
	$plugin->run();

}




run_booking_agent();


require_once plugin_dir_path( __FILE__ ) . 'includes/visitation-calendar.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/booking-agent-admin-hooks.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/booking-agent-visitor-registration.php';


require_once plugin_dir_path( __FILE__ ) . 'includes/Inmate_List_Table.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Visitor_List_Table.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Visitation_List_Table.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Visitation_One_Day_Table.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/Visitor_Schedule_Visit.php';

// require_once plugin_dir_path( __FILE__ ) . 'includes/Courier.php';


// Doesn't seem to work, leaving here for later


?>