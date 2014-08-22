<?php
/**
 * INSTITUTION DB MANAGEMENT
 */

global $wpdb;

// change this when installing a new table version
// otherwise, access_table_install will not run every time (this is a good thing)
global $inst_db_version;
$inst_db_version = '1.04';

global $inst_table_name;
$inst_table_name = $wpdb->prefix . 'institutions';

global $inst_location_table_name;
$inst_location_table_name = $wpdb->prefix . 'institution_locations';

global $inst_ip_table_name;
$inst_ip_table_name = $wpdb->prefix . 'institution_ips';

global $inst_order_table_name;
$inst_order_table_name = $wpdb->prefix . 'institution_orders';

/**
 * create the table that houses access rules
 * @return [type] [description]
 */
function inst_table_install() {
	global $wpdb;
	global $inst_db_version;
	global $inst_table_name;
	global $inst_location_table_name;
	global $inst_ip_table_name;
	global $inst_order_table_name;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$inst_sql = "CREATE TABLE $inst_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name VARCHAR(100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_location_sql = "CREATE TABLE $inst_location_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		inst_id int NOT NULL,
		description VARCHAR(256) NOT NULL,
		continent VARCHAR(20) NOT NULL,
		region VARCHAR(50) NOT NULL,
		city VARCHAR(50) NOT NULL,
		zip VARCHAR(10) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_ip_sql = "CREATE TABLE $inst_ip_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		location_id int NOT NULL,
		start int(11) UNSIGNED NOT NULL,
		end int(11) UNSIGNED NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_order_sql = "CREATE TABLE $inst_order_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		inst_id int NOT NULL,
		location_id int NOT NULL,
		date_start date NOT NULL,
		date_end date NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($inst_sql);
	dbDelta($inst_location_sql);
	dbDelta($inst_ip_sql);
	dbDelta($inst_order_sql);

	check_db_errors();

	add_option( 'inst_db_version', $inst_db_version );
}
/**
 * runs the table install if the version numbers dont match
 * @return [type] [description]
 */
function update_inst_table() {
	global $inst_db_version;
	if(get_option('inst_db_version') != $inst_db_version) {
		inst_table_install();
	}
}
add_action('init', 'update_inst_table');


?>