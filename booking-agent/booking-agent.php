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

function booking_agent_menu() {
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
	add_options_page( 'Booking Agent Settings', 'Booking Agent', 'manage_options', 'booking-agent-settings', 'booking_agent_options' );
}

/** Step 3. */
function booking_agent_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>Booking agent sttings TBD.</p>';
	echo '</div>';
}


function add_meta_data_for_visitors ( $user ) {
	if ( is_visitor_role_only( $user ) ) {

	?>
	<h3>Visitor Profile</h3>

	<table class="form-table">
		<tr>
			<th><label for="birthday">Date of Birth</label></th>
			<td><input type="text" name="birthday"
			           value="<?php echo esc_attr( get_the_author_meta( 'birthday', $user->ID ) ); ?>"
			           class="regular-text"/></td>
		</tr>
		<tr>
			<th><label for="pin">PIN</label></th>
			<td><input type="text" name="pin"
			           value="<?php echo esc_attr( get_the_author_meta( 'pin', $user->ID ) ); ?>"
			           class="regular-text"/></td>
		</tr>
	</table>
	<?php
	}
}



function booking_agent_remove_menu_pages() {


	if ( is_booking_agent_role_only() )
	 {
		 remove_menu_page( 'index.php' );                  //Dashboard
		 remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' ); // admin color scheme

	 }
}

function booking_agent_add_to_admin_bar(){
	global $wp_admin_bar;

	$user = wp_get_current_user();
	if (is_visitor_role_only($user) ||is_booking_agent_role_only() ){

		$args = array(
			'id' => false, // defaults to a sanitized title value.
			'title' => 'New Visit Request',
			'href' => 'admin.php?page=visitation-calendar',
			'parent' => 'new-content', // false for a root menu, pass the ID value for a submenu of that menu.
			'group' => false,
			'meta' => false // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '', tabindex => '' );
		);

		$wp_admin_bar->add_node($args);


	}

}


function remove_admin_bar_links() {
	global $wp_admin_bar;
	$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
	$wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
	$wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
	$wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
	$wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
	$wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
	//$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
	$wp_admin_bar->remove_menu('view-site');        // Remove the view site link
	$wp_admin_bar->remove_menu('updates');          // Remove the updates link
	$wp_admin_bar->remove_menu('comments');         // Remove the comments link
	//$wp_admin_bar->remove_menu('new-content');      // Remove the content link
	$wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
	//$wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );
add_action( 'wp_before_admin_bar_render', 'booking_agent_add_to_admin_bar' );


function is_visitor_role_only($user){
	if ( in_array( 'author', (array) $user->roles ) ||
	     in_array( 'administrator', (array) $user->roles ) ||
	     in_array( 'editor', (array) $user->roles ) ||
	     in_array( 'subscriber', (array) $user->roles ) ||
	     in_array( 'contributor', (array) $user->roles ) ||
		in_array( 'scheduler_role', (array) $user->roles )
	){
		return false;
	} else {
		return true;
	}
}

function is_booking_agent_role_only(){
	$user = wp_get_current_user();
	if ( in_array( 'author', (array) $user->roles ) ||
	     in_array( 'administrator', (array) $user->roles ) ||
	     in_array( 'editor', (array) $user->roles ) ||
	     in_array( 'subscriber', (array) $user->roles ) ||
	     in_array( 'contributor', (array) $user->roles )
	){
		return false;
	} else {
		return true;
	}
}


	function remove_unneeded_profile_fields_with_css()
{
	if(is_booking_agent_role_only()){
		echo '<style>.user-url-wrap{ display: none ! important; }</style>';
		echo '<style>.user-description-wrap{ display: none ! important; }</style>';
		echo '<style>.user-profile-picture{ display: none ! important; }</style>';
		echo '<style>.user-sessions-wrap{ display: none ! important; }</style>';
		echo '<style>.user-display-name-wrap{ display: none ! important; }</style>';
		echo '<style>.user-nickname-wrap{ display: none ! important; }</style>';
		echo '<style>#your-profile h2{ display: none ! important; }</style>';

		echo '<style>.user-comment-shortcuts-wrap{ display: none ! important; }</style>';
		echo '<style>.user-rich-editing-wrap{ display: none ! important; }</style>';
		echo '<style>.user-admin-color-wrap{ display: none ! important; }</style>';
		echo '<style>.show-admin-bar{ display: none ! important; }</style>';

	}


}

add_action( 'admin_menu', 'booking_agent_menu' );
add_action( 'admin_init', 'booking_agent_remove_menu_pages' );


add_action( 'show_user_profile', 'add_meta_data_for_visitors' );
add_action( 'edit_user_profile', 'add_meta_data_for_visitors' );
add_action( "user_new_form", "add_meta_data_for_visitors" );




add_action( 'admin_head-user-edit.php', 'remove_unneeded_profile_fields_with_css' );
add_action( 'admin_head-profile.php',   'remove_unneeded_profile_fields_with_css' );
add_action( 'admin_head-user-new.php',   'remove_unneeded_profile_fields_with_css' );



function add_booking_agent_options_page()
{


	// Add an item to the menu.
	add_menu_page(
		__( 'Visitation Calendar', 'booking-agent' ),
		__( 'Visitation Calendar', 'booking-agent' ),
		'manage_visitation_calendar_capability',
		'visitation-calendar',
		'booking_agent_visitation_calendar_function',
		'dashicons-calendar-alt',
		0
	);


	// Add an item to the menu.
	add_menu_page(
		__( 'Schedule a Visit', 'booking-agent' ),
		__( 'Schedule a Visit', 'booking-agent' ),
		'visitor_schedule_visit_capability',
		'schedule-visit',
		'booking_agent_visitor_schedule_visit_function',
		'dashicons-migrate',
		1
	);



}

function booking_agent_visitation_calendar_function()
{
	// check user capabilities
	if (!current_user_can('manage_visitation_calendar_capability')) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?= esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "wporg_options"
			settings_fields('wporg_options');
			// output setting sections and their fields
			// (sections are registered for "wporg", each field is registered to a specific section)
			do_settings_sections('wporg');
			// output save settings button
			submit_button('Save Settings');
			?>
		</form>
	</div>
	<?php
}



add_action('admin_menu', 'add_booking_agent_options_page');



require_once plugin_dir_path( __FILE__ ) . 'includes/Inmate_List_Table.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/Visitor_List_Table.php';


// Doesn't seem to work, leaving here for later

//enqueues our external font awesome stylesheet
function enqueue_our_required_stylesheets(){
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
}
add_action('admin_init','enqueue_our_required_stylesheets');

function remove_dashboard_meta() {
	remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
}
add_action( 'admin_init', 'remove_dashboard_meta' );

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function example_add_dashboard_widgets() {

	wp_add_dashboard_widget(
		'schedule_announcements',         // Widget slug.
		'Visit Schedule Announcments',         // Title.
		'schedule_announcements_function' // Display function.
	);


	wp_add_dashboard_widget(
		'visitor_forms',         // Widget slug.
		'Visitor Forms',         // Title.
		'visitor_forms_function' // Display function.
	);
}
add_action( 'wp_dashboard_setup', 'example_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function schedule_announcements_function() {

	// Display whatever it is you want to show.
	echo "9/19/16 - Visits are Scheduled Today";
}

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function visitor_forms_function() {

	// Display whatever it is you want to show.
	echo "TBD: Access Forms Here";
}


?>