<?php


/** ************************* LOAD THE BASE CLASS ******************************
 *
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary. In this tutorial, we are
 * going to use the WP_List_Table class directly from WordPress core.
 *
 * IMPORTANT:
 * Please note that the WP_List_Table class technically isn't an official API,
 * and it could change at some point in the distant future. Should that happen,
 * I will update this plugin with the most current techniques for your reference
 * immediately.
 *
 * If you are really worried about future compatibility, you can make a copy of
 * the WP_List_Table class (file path is shown just below) to use and distribute
 * with your plugins. If you do that, just remember to change the name of the
 * class to avoid conflicts with core.
 *
 * Since I will be keeping this tutorial up-to-date for the foreseeable future,
 * I am going to work with the copy of the class provided in WordPress core.
 */
if ( ! class_exists( 'WP_List_Table' ) )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/* Hide notices to avoid AJAX errors
 * Sometimes the Class throws a notice about 'hook_suffix' being undefined,
 * which breaks every AJAX call.
 */
error_reporting( ~E_NOTICE );


/** ************************ CREATE A PACKAGE CLASS ****************************
 *
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our theme for this list table is going to be movies.
 */
class Visitor_List_Table extends WP_List_Table {

	/**
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 */
	function __construct() {

		global $status, $page;

		//Set parent defaults
		parent::__construct(
			array(
				//singular name of the listed records
				'singular'	=> 'wp_visitor',
				//plural name of the listed records
				'plural'	=> 'wp_visitors',
				//does this table support ajax?
				'ajax'		=> true
			)
		);

	}


	/**
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 * @param array $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 */
	function column_default( $item, $column_name ) {


		switch ( $column_name ) {
		/* wp_inmate_visitors
VISIT_REL_ID
INMATE_ID
VISITOR_NAME
RELATIONSHIP
DATE_OF_BIRTH
VISITOR_PIN
APPROVAL_DATE
VISITOR_STATUS
VISITOR_MESSAGE
WP_USER_ID*/
			// case 'INMATE_ID':
			case 'RELATIONSHIP':
			case 'DATE_OF_BIRTH':
			case 'VISITOR_PIN':
			case 'APPROVAL_DATE':
			case 'VISIT_CODE':
			case 'VISITOR_STATUS':
				return $item->$column_name;
			default:
				//Show the whole array for troubleshooting purposes
				return print_r( $item, true );
		}
	}


	/**
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @param array $item A singular item (one full row's worth of data)
	 *
	 * @return string Text to be placed inside the column <td> (movie title only)
	 */
	function column_inmate_id( $item ) {


		//Return the title contents
		return sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->INMATE_ID .'</a>', 'all_inmates_list', 'view', $item->INMATE_ID);
	}

	function column_inmate_name( $item ) {


		//Return the title contents
		return sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->INMATE_NAME .'</a>', 'all_inmates_list', 'view', $item->INMATE_ID);
	}


	function column_visitor_name( $item ) {

		$visitorNameRow = sprintf( '<a href="?page=%s&action=%s&visitor_id=%s">'.$item->VISITOR_NAME .'</a>', 'all_visitors_list', 'view', $item->VISIT_REL_ID);

		$fullRow = $visitorNameRow;

		//Return the title contents
		return $fullRow;
	}

	/*
	 * version with edit and delete buttons
	 *
	 * 	function column_inmate_id( $item ) {
		// <a href="./users.php?page=add_or_update_inmate"
		//Build row actions
		$actions = array(
			'edit'		=> sprintf( '<a href="?page=%s&action=%s&movie=%s">Edit</a>', $_REQUEST['page'], 'edit', $item->INMATE_ID),
			'delete'	=> sprintf( '<a href="?page=%s&action=%s&movie=%s">Delete</a>', $_REQUEST['page'], 'delete', $item->INMATE_ID ),
		);

		//Return the title contents
		return sprintf('%1$s %2$s',
			 $item->INMATE_ID,
	 $this->row_actions( $actions )
		);
		}

*/



	/**
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a column_cb() method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::single_row_columns()
	 *
	 * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
	 */
	function get_columns() {

		/*
		 * case 'INMATE_ID':
			case 'INMATE_NAME':
			case 'POD_CELL':
			case 'INC_DATE':
			case 'VISIT_CODE':
		 */
		return $columns = array(
			'VISITOR_NAME'	=> 'Visitor',
			'INMATE_ID'		=> 'Inmate ID',
			'INMATE_NAME'		=> 'Inmate Name',
			'RELATIONSHIP'	=> 'Relationship',
			'VISITOR_PIN'	=> 'PIN',
			'DATE_OF_BIRTH'	=> 'Birthday',
			'APPROVAL_DATE'	=> 'Approval Date',
			'VISITOR_STATUS'	=> 'Visitor Status',
			'VISIT_CODE'	=> 'Inmate Visit Status'
		);

	}


	/**
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within prepare_items() and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
	 */
	function get_sortable_columns() {

		/*
		 * 'INMATE_ID'		=> 'Inmate ID',
			'INMATE_NAME'	=> 'Inmate Name',
			'POD_CELL'	=> 'Pod/Cell',
			'INC_DATE'	=> 'Incarceration Date',
			'VISIT_CODE'	=> 'Visit Code
		 */

		return $sortable_columns = array(
			'INMATE_ID'	 	=> array( 'INMATE_ID', false ),	//true means it's already sorted
			'INMATE_NAME'	=> array( 'INMATE_NAME', false ),
			'VISITOR_NAME'	=> array( 'VISITOR_NAME', false ),
			'VISITOR_STATUS'	=> array( 'VISITOR_STATUS', false )
		);
	}




	/**
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here...
	 *
	 * @global WPDB $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
	// fixed long url issue
		$_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );


		global $wpdb; //This is used only if making any database queries

		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 100;


		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();


		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array($columns, $hidden, $sortable);




		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */

		// $data = $this->example_data;

		/*
	 * 'INMATE_ID'		=> 'Inmate ID',
		'INMATE_NAME'	=> 'Inmate Name',
		'POD_CELL'	=> 'Pod/Cell',
		'INC_DATE'	=> 'Incarceration Date',
		'VISIT_CODE'	=> 'Visit Code
	 */
		// s=robert
		$searchString ='';
		if (isset($_GET['s'])) {
			$searchString = $_GET['s'];
		}
		$prefix = $wpdb->base_prefix;

		/*
		 * wp_inmate_visitors
VISIT_REL_ID
INMATE_ID
VISITOR_NAME
RELATIONSHIP
DATE_OF_BIRTH
VISITOR_PIN
APPROVAL_DATE
VISITOR_STATUS
VISITOR_MESSAGE
WP_USER_ID
		 */

		// WHERE name LIKE '%w%';
		$base_query = "SELECT " .
						" vis.VISITOR_NAME, " .
					  " i.INMATE_ID,".
				      " vis.VISIT_REL_ID,".
					  " i.INMATE_NAME,".
		              " vis.RELATIONSHIP, " .
						" vis.DATE_OF_BIRTH, " .
					   " vis.VISITOR_PIN, " .
						" vis.APPROVAL_DATE, " .
						" vis.VISITOR_STATUS, " .
		              "CONCAT(i.VISIT_CODE, '  - ', v.description ) as VISIT_CODE " .
		              "  FROM " .
						$prefix . "inmate_visitors vis left join " .
						$prefix . "inmates i" .
						" ON vis.INMATE_ID = i.INMATE_ID " .
					   "left join ". $prefix . "inmate_visitation_status v " .
		              " ON i.VISIT_CODE = v.VISITATION_STATUS_CODE " ;

		if (!empty($searchString)){
			$base_query = $base_query . " AND " .
											" ( i.INMATE_NAME like '%" . $searchString . "%' " .
		                                     " OR i.INMATE_ID like '%" . $searchString . "%' " .
											  " OR vis.VISITOR_NAME like '%" . $searchString . "%' " .
		                                    " )";
		}

		$base_query = $base_query . ' ORDER BY ';

		if (!empty( $_REQUEST['orderby'])){

			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';

			$base_query = $base_query . $_REQUEST['orderby'] . ' '. $order .', ';

		}
		$base_query = $base_query . "  vis.VISITOR_NAME,i.INMATE_NAME,vis.RELATIONSHIP  ";

		if (isset($_GET['debug'])) {
			echo $base_query;
		}


		$base_query = $base_query . " ; " ;

		$data = $wpdb->get_results($base_query);


		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b ) {

			return ;

			// just kept here for demo purposes, but we're sorting on the database

			//If no sort, default to title
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'VISITOR_NAME';
			//If no order, default to asc
			$order = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc';
			//Determine sort order
			$result = strcmp( $a->$orderby, $b->$orderby );
			//Send final sort direction to usort
			return ( 'asc' === $order ) ? $result : -$result;
		}
		// usort( $data, 'usort_reorder' );

		/***********************************************************************
		 * ---------------------------------------------------------------------
		 * vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv
		 *
		 * In a real-world situation, this is where you would place your query.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 *
		 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
		 * ---------------------------------------------------------------------
		 **********************************************************************/


		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count($data);


		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);



		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;


		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(
			array(
				//WE have to calculate the total number of items
				'total_items'	=> $total_items,
				//WE have to determine how many items to show on a page
				'per_page'	=> $per_page,
				//WE have to calculate the total number of pages
				'total_pages'	=> ceil( $total_items / $per_page ),
				// Set ordering values if needed (useful for AJAX)
				'orderby'	=> ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'title',
				'order'		=> ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'
			)
		);
	}



}





/** ************************ REGISTER THE TEST PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page. For this example, we'll add a top-level
 * menu item to the bottom of the admin menus.
 */
function visitor_add_menu_items(){
	add_submenu_page(
		'users.php', // $parent_slug
		'Visitors List Page', //$page_title
		'Visitors List', // $menu_title
		'list_users', // $capability
		'all_visitors_list', // $menu_slug
		'all_visitors_render_list_page' // $function
	);

	add_submenu_page(
		'users.php', // $parent_slug
		'Add New Visitor', //$page_title
		'Add New Visitor', // $menu_title
		'list_users', // $capability
		'add_or_update_visitor', // $menu_slug
		'add_or_update_visitor' // $function
	);

	// add_menu_page('Example Plugin List Table', 'List Table Example', 'list_users', 'tt_list_test', 'all_inmates_render_list_page');
}

add_action('admin_menu', 'visitor_add_menu_items');

function add_note_to_visitor(){
	global $wpdb;
	$prefix = $wpdb->base_prefix;
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;



	if(!empty($_GET['addNoteButton'])){

		$visit_rel_id = $_GET['visitor_id'];

		$new_note = $_GET['new_note'];

		if (!empty($new_note)){
			/*$wpdb->update('table_name', array('id'=>$id, 'title'=>$title, 'message'=>$message), array('id'=>$id))*/

			$wpdb->insert($wpdb->prefix . 'visitor_notes', array('VISIT_REL_ID' => $visit_rel_id,
				'NOTE_CREATOR' => $user_id,
				'NOTE_TEXT' => $new_note,
				'NOTE_EDITOR' => $user_id),
				array('%s', '%s', '%s', '%s') );




			?>

			<div id="welcome-panel" class="welcome-panel">
				<div class="welcome-panel-content">
					<h2>Visitor record updated.
					</h2>
					<p class="about-description">The note has been added to the visitor's record.</p>
					<br/>

				</div>
			</div>

			<?php
		} else {
			?>

			<div id="welcome-panel" class="welcome-panel">
				<div class="welcome-panel-content">
					<h2>No note was entered, so nothing was added to system.
					</h2>
					<p class="about-description">The visitor record was not updated, however is displayed for you below</p>
					<br/>

				</div>
			</div>

			<?php
		}
	}



		display_visitor_details($visit_rel_id);

		return;
}

function add_or_update_visitor(){

	global $wpdb;
	$prefix = $wpdb->base_prefix;


	if(!empty($_GET['updateVisitorButton'])){


		$visitor_id = $_GET['visitor_id'];

		/*$wpdb->update('table_name', array('id'=>$id, 'title'=>$title, 'message'=>$message), array('id'=>$id))*/

		$wpdb->update($wpdb->prefix . 'inmate_visitors', array(
			'visitor_name' => $_GET['visitor_name'],
			'visitor_pin' => $_GET['pin'],
			'visitor_message' => $_GET['visitor_message'],
			'date_of_birth' => $_GET['date_of_birth'],
			'visitor_status' => $_GET['status']),
			array('visit_rel_id'=>$visitor_id),
			array('%s','%s','%s','%s', '%s'),
			array('%s') );

		 // $wpdb->show_errors();




		?>

		<div id="welcome-panel" class="welcome-panel">
			<div class="welcome-panel-content">
				<h2>Inmate Record updated for Visitor
				</h2>
				<p class="about-description">The Visitor record has been updated</p>
				<br/>

			</div>
		</div>

		<?php

		display_visitor_details($visitor_id);

		return;

	}

	if(!empty($_GET['addVisitorButton'])){

		/*
		 *
VISIT_REL_ID
INMATE_ID
VISITOR_NAME
RELATIONSHIP
DATE_OF_BIRTH
VISITOR_PIN
APPROVAL_DATE
VISITOR_STATUS
VISITOR_MESSAGE
WP_USER_ID
		 */
				// record does not exist, lets add it
				$wpdb->insert($wpdb->prefix . 'inmate_visitors', array('inmate_id' => $_GET['inmate_id'],
				                                             'visitor_name' => $_GET['visitor_name'],
															'visitor_pin' => $_GET['pin'],
															'visitor_message' => $_GET['visitor_message'],
															'relationship' => $_GET['relationship'],
				                                             'date_of_birth' => $_GET['date_of_birth'],
				                                             'visitor_status' => $_GET['status']),
					array('%s', '%s', '%s','%s', '%s', '%s','%s' ) );

		//exit( var_dump( $wpdb->last_query ) );

		$wpdb->show_errors();
					$visitor_id = $wpdb->insert_id;
		echo ' new id is ' . $visitor_id;
		$wpdb->show_errors();


		?>
				<div id="welcome-panel" class="welcome-panel">
					<div class="welcome-panel-content">
						<h2>The new visitor was added, please see their record below
						</h2>
						<p class="about-description">This is the new record that was just added to the system</em></p>
						<br/>

					</div>
				</div>


				<?php

				display_visitor_details($visitor_id, false);

				return;

			}



		/*
	 *
Inmate ID
Inmate Name
Pod/Cell
Incarceration Date
Visit Cod
	 */
	?>
	<h3><span class="dashicons dashicons-id"></span> Add New Visitor</h3>

			<form id="add_visitor" method="get">

			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

	<table class="form-table">

		<tr>
			<th><label for="inmate_name">Visitor Name</label></th>
			<td><input type="text" name="visitor_name"
					   value="<?php echo $_GET['visitor_name'] ?>"
					   class="regular-text"/></td>
		</tr>

		<tr>
			<th><label for="pin">PIN</label></th>
			<td><input type="text" name="pin"
					   value="<?php echo $_GET['pin'] ?>"
					   class="regular-text"/></td>
		</tr>



		<tr>
			<th><label for="date_of_birth">Date of Birth</label></th>
			<td><input type="date" name="date_of_birth"
			           value="<?php echo $_GET['date_of_birth'] ?>"
			           class="regular-text"/></td>
		</tr>



		<!--<select>
  <option value="volvo">Volvo</option>

  <option value="audi" selected>Audi</option>
</select> -->


		<tr>
			<th><label for="status">Visit Approval Status</label></th>
			<td><select name="status">
					<?php


					$visit_code_query = "SELECT STATUS,".
					              " DESCRIPTION ".
					              "  FROM " .
					              $prefix . "inmate_visitor_statuses v" ;

					$visit_data = $wpdb->get_results($visit_code_query);

					foreach ( $visit_data as $key => $code ) {
						$selected = '';
						if ($code->VISITATION_STATUS_CODE == $_GET['status']){
							$selected = "selected";
						}
						echo '<option '. $selected .' value="'.$code->STATUS.'">'.$code->STATUS. ' - ' . $code->DESCRIPTION .'</option>';
					}

					?>
			           </select></td>
		</tr>
		<tr>
			<th><label for="relationship">Relationship</label></th>
			<td><input type="text" name="relationship"
					   value="<?php echo $_GET['relationship'] ?>"
					   class="regular-text"/></td>
		</tr>

		<tr>
			<th><label for="pin">Visitor Message</label></th>
			<td><textarea name="visitor_message"
					   style="width: 600px; height: 100px;"
					   class="regular-text"><?php echo $_GET['visitor_message'] ?></textarea></td>
		</tr>

		<?php
			$readonly = '';

			if(!empty($_GET['source'])){
				$readonly = 'readOnly';
			}

		?>
		<tr>
			<th><label for="inmate_id">Inmate ID</label></th>
			<td><input type="text" name="inmate_id"
					   value="<?php echo $_GET['inmate_id'] ?>"
					   <?php echo $readonly; ?>
					   class="regular-text"/></td>
		</tr>

		<?php
			if(!empty($_GET['source'])) {

				$inmate_query = "SELECT INMATE_NAME".
				                    "  FROM " .
				                    $prefix . "inmates ".
									" WHERE INMATE_ID = '" . $_GET['inmate_id'] . "';" ;

				// echo $inmate_query;
				$inmate_data = $wpdb->get_row($inmate_query);
				?>
				<tr>
					<th><label for="inmate_id">Inmate Name</label></th>
					<td><input type="text" name="inmate_name"
							   value="<?php echo $inmate_data->INMATE_NAME ?>"
							   readonly
							   class="regular-text"/></td>
				</tr>
				<?php
			}
				?>


	</table>
	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

	<p class="submit"><input type="submit" name="addVisitorButton" id="addVisitorButton" class="button button-primary" value="Add New Visitor">
		 <a href="./users.php?page=all_visitors_list" type="submit" name="createinmate" id="createinmatesub" class="button button-secondary" value="Return to Visitor List">Return to Visitor List</a>
		<?php
			if(!empty($_GET['source'])) {
				echo '<a type="submit" href="./users.php?page=all_inmates_list&action=view&inmate_id='.$_GET['inmate_id'].'" class="button button-secondary">Return to Inmate</a>';
			}
		?>
	</p>
	</form>
	<?php
}

function display_visitor_details($visitor_id, $editable = true){

	// echo $visitor_id;
	global $wpdb;
	$prefix = $wpdb->base_prefix;

	$readonly = '';
	if ($editable == false){
		$readonly = 'readOnly';
	}

	$user_data = $wpdb->get_row("SELECT WP_USER_ID FROM " .
							    $prefix . "inmate_visitors " .
							   " WHERE VISIT_REL_ID = " . $visitor_id . ";");


	$wp_user_id = $user_data-> WP_USER_ID;

	// echo $wp_user_id;

	$data = get_visitor_and_inmate_data_by_visitor_id($visitor_id);

	?>
	<h3><span class="dashicons dashicons-welcome-widgets-menus"></span> Visitor Details</h3>

	<?php
		if (empty($data)){
			echo 'Sorry, no record exists for Visitor ID: ' + $visitor_id;
			return;
		}
	?>
	<form id="inmate-list-filter" method="get">


		<input type="hidden" name="visitor_id" value="<?php echo $visitor_id ?>" />

		<table class="form-table">

			<tr>
				<th><label for="inmate_name">Visitor Name</label></th>
				<td><input type="text" name="visitor_name"
						   value="<?php echo $data->VISITOR_NAME ?>"
						   class="regular-text"/></td>
			</tr>

			<tr>
				<th><label for="pin">PIN</label></th>
				<td><input type="text" name="pin"
						   value="<?php echo $data->VISITOR_PIN ?>"
						   class="regular-text"/></td>
			</tr>
			<tr>
				<th><label for="date_of_birth">Date of Birth</label></th>
				<td><input type="date" name="date_of_birth"
						   value="<?php echo $data->DATE_OF_BIRTH ?>"
						   class="regular-text"/></td>
			</tr>



			<!--<select>
      <option value="volvo">Volvo</option>

      <option value="audi" selected>Audi</option>
    </select> -->


			<tr>
				<th><label for="status">Visit Approval Status</label></th>
				<td><select name="status">
						<?php


						$visit_code_query = "SELECT STATUS,".
							" DESCRIPTION ".
							"  FROM " .
							$prefix . "inmate_visitor_statuses v" ;

						$status_data = $wpdb->get_results($visit_code_query);

						foreach ( $status_data as $key => $code ) {
							$selected = '';
							if ($code->VISITATION_STATUS_CODE == $status_data->VISITOR_STATUS){
								$selected = "selected";
							}
							echo '<option '. $selected .' value="'.$code->STATUS.'">'.$code->STATUS. ' - ' . $code->DESCRIPTION .'</option>';
						}

						?>
					</select></td>
			</tr>

			<tr>
				<th><label for="visitor_message">Visitor Message</label></th>
				<td><textarea name="visitor_message"
						   style="width: 600px; height: 100px;"
						   class="regular-text"><?php echo $data->VISITOR_MESSAGE ?></textarea></td>
			</tr>

	<!--		<tr>
				<th><label for="relationship">Relationship</label></th>
				<td><input type="text" name="relationship"
						   value="<?php echo $data->RELATIONSHIP ?>"
						   class="regular-text"/></td>
			</tr>
			<tr>
				<th><label for="inmate_id">Inmate ID</label></th>
				<td><input type="text" name="inmate_id"
						   value="<?php echo $data->INMATE_ID ?>"
						   class="regular-text"/></td>
			</tr>

			<tr>
				<th><label for="inmate_id">Inmate Name</label></th>
				<td><input type="text" name="inmate_name"
						   value="<?php echo $data->INMATE_NAME ?>"
						   class="regular-text"/></td>
			</tr>

			-->



		</table>


	<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />


	<p class="submit">
		<?php if ($editable) {
		?>

		<input type="submit" name="updateVisitorButton" id="updateVisitorButton" class="button button-primary" value="Update Visitor Info"> </input>  |
		<?php } ?>
		 <a href="./users.php?page=all_visitors_list" type="submit" name="createinmate" id="createinmatesub" class="button button-secondary" value="Return to List"> Go to Visitor List</a>
	</p>



	<?php

	generate_inmates_table($visitor_id, $wp_user_id  );

	generate_visitor_notes_table_and_form($visitor_id, $wp_user_id );

	?>
	</form>
	<?php



}

/**
 * @param $visitor_id
 * @param $prefix
 * @param $wpdb
 * @return mixed
 */
function get_visitor_and_inmate_data_by_visitor_id($visitor_id)
{

	global $wpdb;
	$prefix = $wpdb->base_prefix;

	$base_query = "SELECT " .
		" vis.VISITOR_NAME, " .
		" vis.VISITOR_PIN, " .
		" i.INMATE_ID," .
		" vis.VISIT_REL_ID," .
		" i.INMATE_NAME," .
		" vis.RELATIONSHIP, " .
		" vis.DATE_OF_BIRTH, " .
		" vis.VISITOR_PIN, " .
		" vis.APPROVAL_DATE, " .
		" vis.VISITOR_STATUS,
		 vis.VISITOR_MESSAGE," .
		"CONCAT(i.VISIT_CODE, '  - ', v.description ) as VISIT_CODE " .
		"  FROM " .
		$prefix . "inmate_visitors vis left join " .
		$prefix . "inmates i " .
		" on vis.INMATE_ID = i.INMATE_ID left join " .
		$prefix . "inmate_visitation_status v " .
		" on i.VISIT_CODE = v.VISITATION_STATUS_CODE " .
		" WHERE vis.VISIT_REL_ID = " . $visitor_id . ";";


	// echo $base_query;
	$data = $wpdb->get_row($base_query);
	return $data;
}


function generate_inmates_table($visitor_id, $wp_user_id  )
{
	global $wpdb;
	$prefix = $wpdb->base_prefix;

	$where_clause = ' AND v.VISIT_REL_ID = ' . $visitor_id;
	if (!empty($wp_user_id)) {
		$where_clause = ' AND v.wp_user_id = ' . $wp_user_id;
	}


	$visitors_query = "SELECT v.RELATIONSHIP, " .
		" v.APPROVAL_DATE, " .
		" v.VISITOR_STATUS, " .
		" v.VISITOR_MESSAGE, " .
		"i.INMATE_ID," .
		" i.INMATE_NAME," .
		" i.POD_CELL, " .
		" i.INC_DATE, " .
		"CONCAT(i.VISIT_CODE, '  - ', c.description ) as VISIT_CODE " .
		"  FROM " .
		$prefix . "inmate_visitors v , " .
		$prefix . "inmates i," .
		$prefix . "inmate_visitation_status c" .
		" WHERE i.VISIT_CODE = c.VISITATION_STATUS_CODE " .
		" AND i.INMATE_ID = v.INMATE_ID " .
		$where_clause .
		" ORDER BY i.INMATE_NAME ;";


	// echo $visitors_query;
	$inmates = $wpdb->get_results($visitors_query);

	// echo $visitors_query;
	/*
	 * foreach( $wpdb->get_results("SELECT * FROM your_table_name WHERE id LIKE' . $id . ';") as $key => $row) {
// each column in your row will be accessible like this
$my_column = $row->column_name;}
	 */

	?>
	<h3><span class="dashicons dashicons-groups"></span> Inmates</h3>
	<input type="submit"
	   name="addNewInmate" id="addNewInmate" class="button button-primary" value="Quick Add New Inmate"></input>
	<br/><br/>

	<?php

	if (count($inmates) > 0) {

		?>


		<table class="widefat striped wp-list-table widefat fixed ">
			<thead>
			<tr>
				<th>Name</th>
				<th>Inmate ID</th>
				<th>Relationship</th>
				<th>Incarceration Date</th>
				<th>Inmate Visit Code</th>
				<th>POD/Cell</th>
				<th>Visitor Status</th>
				<th>Approval Date</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th>Name</th>
				<th>Inmate ID</th>
				<th>Relationship</th>
				<th>Incarceration Date</th>
				<th>Inmate Visit Code</th>
				<th>POD/Cell</th>
				<th>Visitor Status</th>
				<th>Approval Date</th>
			</tr>
			</tfoot>


			<tbody>
			<?php
			foreach ($inmates as $key => $inmate) {

				?>

				<tr>
					<td><a href="?page=all_inmates_list&action=view&inmate_id=<?php echo $inmate->INMATE_ID?>"><?php echo $inmate->INMATE_NAME; ?></a></td>
					<td><a href="?page=all_inmates_list&action=view&inmate_id=<?php echo $inmate->INMATE_ID?>"><?php echo $inmate->INMATE_ID; ?></a></td>
					<td><?php echo $inmate->RELATIONSHIP; ?></td>
					<td><?php echo $inmate->INC_DATE; ?></td>
					<td><?php echo $inmate->VISIT_CODE; ?></td>
					<td><?php echo $inmate->POD_CELL; ?></td>
					<td><?php echo $inmate->VISITOR_STATUS; ?></td>
					<td><?php echo $inmate->APPROVAL_DATE; ?></td>

				</tr>

				<?php

			}

			?>

			</tbody>
		</table>
		<br/>
		<a href="./users.php?page=add_or_update_visitor&inmate_id=<?php echo $inmate_id ?>" type="submit"
		   name="addNewVisitor" id="addNewVisitor" class="button button-secondary" value="Add New Visitor"> Add New
			Visitor</a>


		<?php
	} else {
		?>
		<h4><em>Currently not approved to visit any inmates</em></h4>
		<?php
	}

}

/**
 * @param $inmate_id
 * @param $prefix
 * @param $wpdb
 */
function generate_visitor_notes_table_and_form( $visitor_id, $wp_user_id ) {
	global $wpdb;
	$prefix = $wpdb->base_prefix;

	$where_clause = ' AND v.VISIT_REL_ID = ' . $visitor_id;
	if (!empty($wp_user_id)) {
		$where_clause = ' AND v.wp_user_id = ' . $wp_user_id;
	}

	$notes_query = "SELECT n.NOTE_ID," .
	               " u1.user_login as creator," .
	               " u2.user_login as editor, " .
	               " n.NOTE_TEXT, " .
	               " n.NOTE_CREATED, " .
	               " n.NOTE_EDITED " .
	               "  FROM " .
	               $prefix . "VISITOR_notes n," .
					$prefix . "inmate_visitors v, " .
					$prefix . "users u1, " .
	               $prefix . "users u2 " .
	               " WHERE u1.ID = n.NOTE_CREATOR " .
	               " AND u2.ID = n.NOTE_EDITOR " .
	               " AND n.VISIT_REL_ID = v.VISIT_REL_ID " .
				   $where_clause .
	               " order by n.NOTE_EDITED desc;";
	$notes       = $wpdb->get_results( $notes_query );

	// echo $notes_query;
	/*
	 * foreach( $wpdb->get_results("SELECT * FROM your_table_name WHERE id LIKE' . $id . ';") as $key => $row) {
// each column in your row will be accessible like this
$my_column = $row->column_name;}
	 */

	?>
	<h3><span class="dashicons dashicons-admin-page"></span> Visitor Notes</h3>



	<?php

	if ( count( $notes ) > 0 ) {


		?>
		<table class="widefat striped wp-list-table widefat fixed ">
			<thead>
			<tr>
				<th>Created</th>
				<th style="width:50%">Note</th>
				<th>Created By</th>
				<th>Edited By</th>
				<th>Edited</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th>Created</th>
				<th>Note</th>
				<th>Created By</th>
				<th>Edited By</th>
				<th>Edited</th>
			</tr>
			</tfoot>


			<tbody>
			<?php
			foreach ( $notes as $key => $note ) {


				?>


				<tr>
					<td><?php echo $note->NOTE_CREATED; ?></td>
					<td><?php echo  $note->NOTE_TEXT; ?></td>
					<td><?php echo $note->creator ; ?></td>
					<td><?php  echo $note->editor; ?></td>
					<td><?php echo $note->NOTE_EDITED; ?></td>


				</tr>

				<?php

			}

			?>

		</tbody>
		</table>

		<?php

	} else {
		?>
		<h4><em>No notes saved for Visitor</em></h4>
		<?php
	}

	?>

	<h2><span class="dashicons dashicons-welcome-write-blog"></span> Add Note</h2>
	<table class="form-table">
		<tr>
			<td><textarea name="new_note"
			              value=""
			              style="width: 600px; height: 100px;"
			              class="regular-text"></textarea></td>
		</tr>
	</table>

	<input type="submit" name="addNoteButton" id="addNoteButton" class="button button-primary" value="Add a Note"> </input>

	<?php
}


/** *************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function all_visitors_render_list_page(){


	if(!empty($_GET['addNoteButton'])){
		add_note_to_visitor();
		return;
	}
	if(!empty($_GET['updateVisitorButton'])){
		add_or_update_visitor();
		return;
	}

	if (!empty($_GET['visitor_id'])){
		display_visitor_details($_GET['visitor_id']);
		return;
	}

	//Create an instance of our package class...
	$testListTable = new Visitor_List_Table();
	//Fetch, prepare, sort, and filter our data...
	$testListTable->prepare_items();

	?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h3><span class="dashicons dashicons-editor-ul"></span> Visitors List <a href="./users.php?page=add_or_update_visitor" class="page-title-action">Add New</a> |
			<a href="#" class="page-title-action">Dowload as Excel/CSV</a> </h3>

		<?php
			if (!empty($_GET['s'])){
				?>
		<h3>Showing search results for "<?php echo $_GET['s']; ?>"</h3>

			<?php
			}
		?>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="inmate-list-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="user-search-input">Search Inmates:</label>
				<input type="search" id="user-search-input" name="s" value="<?php echo $_GET['s']; ?>">
				<input type="submit" id="search-submit" class="button" value="Search Inmates"></p>
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

			<!-- Now we can render the completed list table -->
			<?php $testListTable->display() ?>
		</form>

	</div>
	<?php
}

