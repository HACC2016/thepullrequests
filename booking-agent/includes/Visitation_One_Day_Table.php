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
class Visitation_One_Day_Table extends WP_List_Table {

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
				'singular'	=> 'wp_visitation',
				//plural name of the listed records
				'plural'	=> 'wp_visitations',
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
		/*   `VISITATION_STATUS_CODE` varchar(5) NOT NULL,
  `VISITATION_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `VISIT_DATE` date NOT NULL,
  `INMATE_ID` varchar(20) NOT NULL,
  `VISITOR_1_ID` int(11) NOT NULL,
  `VISITOR_2_ID` int(11) NOT NULL,
  `VISIT_HOUR_START` int(11) NOT NULL,
  `VISIT_MINUTE_START` int(11) NOT NULL,
  `VISIT_HOUR_END` int(11) NOT NULL,
  `VISIT_MINUTE_END` int(11) NOT NULL,
  `SLOT_NUMBER` int(11) NOT NULL,
  `GENDER_ALLOWED` varchar(50) NOT NULL*/
			// case 'INMATE_ID':
			case 'VISITATION_TIME':
				return $item->VISITATION_TIME;
			default:
				//Show the whole array for troubleshooting purposes

				return Visitation_One_Day_Table::visitation_column($item, $column_name);

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

	function visitation_column( $item, $column_name ) {

		$visitation_id_column = $column_name . '_visitation_id';

		$visitation_status_code_column = $column_name . '_visitation_status_code';

		$inmate_name_column = $column_name . '_inmate_name';
		$inmate_id_column = $column_name . '_inmate_id';


		$visitor_1_name_column = $column_name . '_visitor_1_name';
		$visitor_1_id_column = $column_name . '_visitor_1_id';
		$visitor_1_relationship_column = $column_name . '_visitor_1_relationship';

		$visitor_2_name_column = $column_name . '_visitor_2_name';
		$visitor_2_id_column = $column_name . '_visitor_2_id';
		$visitor_2_relationship_column = $column_name . '_visitor_2_relationship';



		if ($item->$visitation_status_code_column == 'Available'){
			return '---';
		}

		$contents = '';
		$contents .= sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->$inmate_id_column .'</a>', 'all_inmates_list', 'view', $item->$inmate_id_column);

		$contents .= '<br/>';

		$contents .= sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->$inmate_name_column .'</a>', 'all_inmates_list', 'view', $item->$inmate_id_column);
		$contents .= '<hr/>';


		$contents .= sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->$visitor_1_name_column
			. '(' . $item->$visitor_1_relationship_column . ')'
			.'</a>', 'all_visitors_list', 'view', $item->$visitor_1_id_column);

		if (!empty($item->$visitor_2_name_column)) {
			$contents .= '<br/>';

			$contents .= sprintf('<a href="?page=%s&action=%s&inmate_id=%s">' . $item->$visitor_2_name_column
				. '(' . $item->$visitor_2_relationship_column . ')'
				. '</a>', 'all_visitors_list', 'view', $item->$visitor_2_id_column);
		}
		return $contents;

		//return $item->$visitation_id_column;

		//Return the title contents
		// return sprintf( '<a href="?page=%s&action=%s&visitation_id=%s">'.$item->$visitation_id_column .'</a>', 'all_visitations_list', 'view', $item->VISITATION_ID);

		// return sprintf( '<a href="?page=%s&action=%s&inmate_id=%s">'.$item->INMATE_ID .'</a>', 'all_inmates_list', 'view', $item->INMATE_ID);
	}








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

		$visit_date = date('Y-m-d');

		if(!empty($_GET['visit_date'])) {
			$visit_date = $_GET['visit_date'];
		}

		global  $wpdb;
		$prefix = $wpdb->base_prefix;

		$the_query = "select max(slot_number) as MAX_SLOT from ".
			$prefix . "inmate_visitations".
			" where visit_date = '".$visit_date ."';" ;


		$data = $wpdb->get_row($the_query);



		$max_slot = $data->MAX_SLOT;



		/*
		 * /*   `VISITATION_STATUS_CODE` varchar(5) NOT NULL,
  `VISITATION_ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `VISIT_DATE` date NOT NULL,
  `INMATE_ID` varchar(20) NOT NULL,
  `VISITOR_1_ID` int(11) NOT NULL,
  `VISITOR_2_ID` int(11) NOT NULL,
  `VISIT_HOUR_START` int(11) NOT NULL,
  `VISIT_MINUTE_START` int(11) NOT NULL,
  `VISIT_HOUR_END` int(11) NOT NULL,
  `VISIT_MINUTE_END` int(11) NOT NULL,
  `SLOT_NUMBER` int(11) NOT NULL,
  `GENDER_ALLOWED` varchar(50) NOT NULL
		// case 'INMATE_ID':
	case 'VISITATION_STATUS_CODE':
			case 'VISIT_DATE':
			case 'VISIT_HOUR_START':
			case 'VISIT_MINUTE_START':
			case 'VISIT_HOUR_END':
			case 'VISIT_MINUTE_END':
			case 'VISIT_TIME':
			case 'SLOT_NUMBER':
			case 'GENDER_ALLOWED':
		 */



		$columns = array(
			'VISITATION_TIME'	=> 'Time'
		);

		if (empty($max_slot)){
			$max_slot = 1;
		}

		for ($slot_index = 1; $slot_index <= $max_slot; $slot_index++){

			$key_name = 'slot_' . $slot_index;
			$key_value = 'Slot ' .  $slot_index;;
			$columns[$key_name] = $key_value;
		}



		return $columns;

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

		return array();
		/* return $sortable_columns = array(
			'INMATE_ID'	 	=> array( 'INMATE_ID', false ),	//true means it's already sorted
			'INMATE_NAME'	=> array( 'INMATE_NAME', false ),
			'VISIT_DATE'	=> array( 'VISIT_DATE', false ),
			'VISIT_TIME'	=> array( 'VISIT_TIME', false ),
			'SLOT_NUMBER'	=> array( 'SLOT_NUMBER', false ),
			'VISITOR_1_NAME'	=> array( 'VISITOR_1_NAME', false ),
			'VISITOR_2_NAME'	=> array( 'VISITOR_2_NAME', false )
		);*/
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
		$prefix = $wpdb->base_prefix;

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

		$visit_date = date('Y-m-d');

		if(!empty($_GET['visit_date'])) {
			$visit_date = $_GET['visit_date'];
		}

		$base_query = generate_schedule_query($visit_date);

	/*
		if (!empty($searchString)){
			$base_query = $base_query . " AND " .
											" ( i.INMATE_NAME like '%" . $searchString . "%' " .
		                                     " OR i.INMATE_ID like '%" . $searchString . "%' " .
											  " OR vis1.VISITOR_NAME like '%" . $searchString . "%' " .
												" OR vis2.VISITOR_NAME like '%" . $searchString . "%' " .
		                                    " )";
		}

		$base_query = $base_query . ' ORDER BY ';

		if (!empty( $_REQUEST['orderby'])){

			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';

			$base_query = $base_query . $_REQUEST['orderby'] . ' '. $order .', ';

		}
			$base_query = $base_query . "  visitation.VISIT_DATE, visitation.VISIT_HOUR_START, visitation.SLOT_NUMBER";

		// echo $base_query;

		$base_query = $base_query . " ; " ;*/

		if (isset( $_GET['debug'])) {

			echo $base_query;
		}

		$data = $wpdb->get_results($base_query);



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
				'orderby'	=> ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : '',
				'order'		=> ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc'
			)
		);
	}



}


function generate_schedule_query($visit_date){


	global  $wpdb;
	$prefix = $wpdb->base_prefix;
	$max_query = "select max(slot_number) MAX_SLOT from ".
		$prefix . "inmate_visitations".
		" where visit_date = '".$visit_date ."';";

	$data = $wpdb->get_row($max_query);

	$max_slot = $data->MAX_SLOT;

	if (empty($max_slot)){
		$max_slot = 1;
	}

	$select_statement = '';

	$from_clause ='';

	$join_clause = '';

	$where_and_clause = '';

	for ($slot_index = 1; $slot_index <= $max_slot; $slot_index++){
		if ($slot_index == 1){
			$select_statement .= " SELECT concat(visit1.visit_hour_start, ':', lpad(visit1.visit_minute_start,2,0)) ".
			                 "  as VISITATION_TIME ,";
			$from_clause .= " FROM  wp_inmate_visitations visit1  ";
			$where_and_clause .= 'WHERE visit'.$slot_index.".visit_date = '".$visit_date."'" .
			                  '  AND VISIT'.$slot_index.'.SLOT_NUMBER='.$slot_index.' ';
		} else {
			$select_statement .= " , ";
			$from_clause .= " JOIN wp_inmate_visitations visit".$slot_index." " .
          					  " on visit".$slot_index.".visit_date = visit1.visit_date " .
							"  and visit".$slot_index.".VISIT_HOUR_START = visit1.visit_hour_start " .
							"  and visit".$slot_index.".visit_minute_start = visit1.visit_minute_start ";
			$where_and_clause .= " AND VISIT".$slot_index.'.SLOT_NUMBER='.$slot_index .' ';
		}

		$select_statement .= ' visit'.$slot_index.'.visitation_id as slot_'.$slot_index.'_visitation_id, ';
		$select_statement .= ' visit'.$slot_index.'.visitation_status_code as slot_'.$slot_index.'_visitation_status_code, ';

		$select_statement .= ' i'.$slot_index.'.inmate_id as slot_'.$slot_index.'_inmate_id, ';
		$select_statement .= ' i'.$slot_index.'.inmate_name as slot_'.$slot_index.'_inmate_name, ';
		$select_statement .=  'v'.$slot_index.'1.visitor_name as slot_'.$slot_index.'_visitor_1_name, ';
		$select_statement .= ' v'.$slot_index.'1.visit_rel_id as slot_'.$slot_index.'_visitor_1_id, ';
		$select_statement .= ' v'.$slot_index.'1.relationship as slot_'.$slot_index.'_visitor_1_relationship, ';
		$select_statement .= ' v'.$slot_index.'2.visitor_name as slot_'.$slot_index.'_visitor_2_name, ';
		$select_statement .= ' v'.$slot_index.'2.visit_rel_id as slot_'.$slot_index.'_visitor_2_id, ';
		$select_statement .= ' v'.$slot_index.'2.relationship as slot_'.$slot_index.'_visitor_2_relationship ';

		// $from_clause .=   $prefix . 'inmate_visitations visit'.$slot_index;

		$join_clause .= '  left join ' . $prefix . 'inmates i'.$slot_index
			        .' on i'.$slot_index.'.inmate_id = visit'.$slot_index.'.inmate_id  ';
		$join_clause .= ' left join ' . $prefix . 'inmate_visitors v'.$slot_index.'1 '.
			             'on visit'.$slot_index.'.visitor_1_id = v'.$slot_index.'1.visit_rel_id  ';
	   	$join_clause .= '   left join ' . $prefix . 'inmate_visitors v'.$slot_index.'2 '.
			             ' on visit'.$slot_index.'.visitor_2_id = v'.$slot_index.'2.visit_rel_id ';


	}






	$full_query =  	$select_statement . $from_clause .  $join_clause  . $where_and_clause . ';';

	if (isset( $_GET['debug'])) {

		echo $full_query;
	}

	return $full_query;
}




/** *************************** RENDER TEST PAGE ********************************
 *******************************************************************************
 * This function renders the admin page and the example list table. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function visitations_one_day_list_page(){



	//Create an instance of our package class...
	$visitations_one_day_table = new Visitation_One_Day_Table();
	//Fetch, prepare, sort, and filter our data...
	$visitations_one_day_table->prepare_items();

	$visit_date = date('Y-m-d');

	if(!empty($_GET['visit_date'])) {
		$visit_date = $_GET['visit_date'];
	}

	$today_visit_time = strtotime($visit_date);
	$next_date_as_time = $today_visit_time + (3600 * 24); //add seconds of one day

	$previous_date_as_time = $today_visit_time - (3600 * 24); //add seconds of one day
	$next_date = date("Y-m-d", $next_date_as_time);
	$previous_date = date("Y-m-d", $previous_date_as_time);

	$previous_date_link = './admin.php?page=visitation-calendar&visit_date=' . $previous_date;
	$next_date_link = './admin.php?page=visitation-calendar&visit_date=' . $next_date;

	?>
	<div class="wrap">

		<div id="icon-users" class="icon32"><br/></div>
		<h3><span class="dashicons dashicons-editor-ul"></span> Visitation Single Day <a href="./users.php?page=add_or_update_visitor" class="page-title-action">Add New</a> |
			<a href="#" class="page-title-action">Dowload as Excel/CSV</a> </h3>

		<h4>Displaying Data for <?=$visit_date?></h4><br/>
		<p><a href="<?=$previous_date_link?>">Previous Day (<?= $previous_date ?>)</a> | <a href="<?=$next_date_link?>">Next Day (<?= $next_date ?>)</a> </p>


		<?php
			if (!empty($_GET['s'])){
				?>
		<h3>Showing search results for "<?php echo $_GET['s']; ?>"</h3>

			<?php
			}

			$searchString = '';
			if (isset($_GET['s'])){
				$searchString = $_GET['s'];
			}
		?>
		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="inmate-list-filter" method="get">
			<p class="search-box">
				<label class="screen-reader-text" for="user-search-input">Search Visitations:</label>
				<input type="search" id="user-search-input" name="s" value="<?php echo $searchString; ?>">
				<input type="submit" id="search-submit" class="button" value="Search Visitations"></p>
			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

			<!-- Now we can render the completed list table -->
			<?php $visitations_one_day_table->display() ?>
		</form>

	</div>
	<?php
}

