<?php
/**
 * DB MANAGEMENT
 */

global $wpdb;

// change this when installing a new table version
// otherwise, access_table_install will not run every time (this is a good thing)
global $access_db_version;
$access_db_version = '1.05';

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
		result_time_start VARCHAR(6) NOT NULL,
		result_time_elapsed VARCHAR(6) NOT NULL,
		result_msg text NOT NULL,
		result_closable tinyint(1) NOT NULL,
		check_type VARCHAR(256) NOT NULL,
		check_value text NOT NULL,
		priority int(3) NOT NULL,
		selector_type VARCHAR(256) NOT NULL,
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
add_action( 'wp_ajax_nopriv_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_insert-rule', 'insert_rule' );

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
add_action( 'wp_ajax_nopriv_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_delete-rule', 'delete_rule' );

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
		array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
		array('%d')
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_update-rule', 'update_rule' );
add_action( 'wp_ajax_update-rule', 'update_rule' );

?>