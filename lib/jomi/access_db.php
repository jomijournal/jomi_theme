<?php
/**
 * DB MANAGEMENT
 */

global $wpdb;

// change this when installing a new table version
// otherwise, access_table_install will not run every time (this is a good thing)
global $access_db_version;
$access_db_version = '1.01';

global $access_table_name;
$access_table_name = $wpdb->prefix . 'article_access';

/**
 * create the table that houses access rules
 * @return [type] [description]
 */
function access_table_install() {
	global $wpdb;
	global $access_db_version;
	global $access_table_name;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$sql = "CREATE TABLE $access_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		result_type VARCHAR(20) NOT NULL,
		result_time_start int(5) NOT NULL,
		result_time_end int(5) NOT NULL,
		result_time_elapsed int(5) NOT NULL,
		result_msg text NOT NULL,
		check_type VARCHAR(20) NOT NULL,
		check_value text NOT NULL,
		priority int(3) NOT NULL,
		selector_type VARCHAR(20) NOT NULL,
		selector_value text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'access_db_version', $access_db_version );
}
/**
 * runs the table install if the version numbers dont match
 * @return [type] [description]
 */
function update_access_table() {
	global $access_db_version;
	if(get_option('access_db_version') != $access_db_version) {
		access_table_install();
	}
}
add_action('init', 'update_access_table');


/**
 * insert a rule
 * @param  array $args [description]
 * @return [type]       [description]
 */
function insert_rule() {

	global $wpdb;
	global $access_table_name;

	$push_data = process_access_post_data();
	
	$wpdb->insert( 
		$access_table_name, 
		$push_data
	);
	check_db_errors();
}

/**
 * [delete_rule description]
 * @return [type] [description]
 */
function delete_rule() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();

	$wpdb->delete(
		$access_table_name,
		array('id' => $id)
	);
	check_db_errors();
}

/**
 * update an article access rule in the db
 * @return [type] [description]
 */
function update_rule() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();
	
	$push_data = process_access_post_data();

	$wpdb->update(
		$access_table_name,
		$push_data,
		array('ID' => $id),
		array('%s', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s'),
		array('%d')
	);
	check_db_errors();
}

function add_selector() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$selector_type = $rule->selector_type;
	$selector_value = $rule->selector_value;
	//append empty selector
	$selector_type .= ",none";
	$selector_value .= ",none";

	$wpdb->update(
		$access_table_name,
		array(
			'selector_type'=>$selector_type, 
			'selector_value'=>$selector_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	check_db_errors();
}
function add_check() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$check_type = $rule->check_type;
	$check_value = $rule->check_value;
	//append empty selector
	$check_type .= ",none";
	$check_value .= ",none";

	$wpdb->update(
		$access_table_name,
		array(
			'check_type'=>$check_type, 
			'check_value'=>$check_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	check_db_errors();
}
function remove_selector() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();
	$rule_id = $_POST['rule_id'];

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$selector_type = explode(',', $rule->selector_type);
	$selector_value = explode(',', $rule->selector_value);

	if(!isset($_POST['rule_id']) || empty($_POST['rule_id'])) {
		array_pop($selector_type);
		array_pop($selector_value);
	} else {
		array_splice($selector_type, $rule_id - 1, 1);
		array_splice($selector_value, $rule_id - 1, 1);
	}

	$selector_type = implode(',', $selector_type);
	$selector_value = implode(',', $selector_value);

	$wpdb->update(
		$access_table_name,
		array(
			'selector_type'=>$selector_type, 
			'selector_value'=>$selector_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	check_db_errors();
}
function remove_check() {
	global $wpdb;
	global $access_table_name;

	$id = check_post_id();
	$rule_id = $_POST['rule_id'];

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$check_type = explode(',', $rule->check_type);
	$check_value = explode(',', $rule->check_value);

	if(!isset($_POST['rule_id']) || empty($_POST['rule_id'])) {
		array_pop($check_type);
		array_pop($check_value);
	} else {
		array_splice($check_type, $rule_id - 1, 1);
		array_splice($check_value, $rule_id - 1, 1);
	}

	$check_type = implode(',', $check_type);
	$check_value = implode(',', $check_value);

	$wpdb->update(
		$access_table_name,
		array(
			'check_type'=>$check_type, 
			'check_value'=>$check_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	check_db_errors();
}

// DEBUG ONLY: insert an empty rule
//insert_rule(array());

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// register ajax stuff
add_action( 'wp_ajax_nopriv_list-rules', 'list_rules' );
add_action( 'wp_ajax_list-rules', 'list_rules' );
add_action( 'wp_ajax_nopriv_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_nopriv_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_nopriv_update-rule', 'update_rule' );
add_action( 'wp_ajax_update-rule', 'update_rule' );
add_action( 'wp_ajax_nopriv_add-selector', 'add_selector' );
add_action( 'wp_ajax_add-selector', 'add_selector' );
add_action( 'wp_ajax_nopriv_add-check', 'add_check' );
add_action( 'wp_ajax_add-check', 'add_check' );
add_action( 'wp_ajax_nopriv_remove-selector', 'remove_selector' );
add_action( 'wp_ajax_remove-selector', 'remove_selector' );
add_action( 'wp_ajax_nopriv_remove-check', 'remove_check' );
add_action( 'wp_ajax_remove-check', 'remove_check' );

?>