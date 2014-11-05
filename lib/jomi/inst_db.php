<?php
/**
 * INSTITUTION DB MANAGEMENT
 */

global $wpdb;

// increment this when installing a new table version
// otherwise, access_table_install will not run every time (this is a good thing)
global $inst_db_version;
$inst_db_version = '1.13';

/**
 * table names.
 * shouldn't change
 */
global $inst_table_name;
$inst_table_name = $wpdb->prefix . 'institutions';

global $inst_location_table_name;
$inst_location_table_name = $wpdb->prefix . 'institution_locations';

global $inst_ip_table_name;
$inst_ip_table_name = $wpdb->prefix . 'institution_ips';

global $inst_order_table_name;
$inst_order_table_name = $wpdb->prefix . 'institution_orders';

global $inst_contact_table_name;
$inst_contact_table_name = $wpdb->prefix . 'institution_contacts';

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
	global $inst_contact_table_name;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$inst_sql = "CREATE TABLE $inst_table_name (
		id          mediumint(9) NOT NULL AUTO_INCREMENT,
		name        VARCHAR(100) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_location_sql = "CREATE TABLE $inst_location_table_name (
		id          mediumint(9) NOT NULL AUTO_INCREMENT,
		inst_id     int NOT NULL,
		description VARCHAR(256) NOT NULL,
		continent   VARCHAR(20) NOT NULL,
		country     VARCHAR(20) NOT NULL,
		region      VARCHAR(50) NOT NULL,
		city        VARCHAR(50) NOT NULL,
		zip         VARCHAR(10) NOT NULL,
		address     VARCHAR(100) NOT NULL,
		comment     text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_ip_sql = "CREATE TABLE $inst_ip_table_name (
		id          mediumint(9) NOT NULL AUTO_INCREMENT,
		location_id int NOT NULL,
		start       int(11) UNSIGNED NOT NULL,
		end         int(11) UNSIGNED NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_order_sql = "CREATE TABLE $inst_order_table_name (
		id          mediumint(9) NOT NULL AUTO_INCREMENT,
		inst_id     int NOT NULL,
		user_id     int NOT NULL,
		location_id int NOT NULL,
		date_start  date NOT NULL,
		date_end    date NOT NULL,
		type        VARCHAR(20) NOT NULL,
		amount      FLOAT NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";
	$inst_contact_sql = "CREATE TABLE $inst_contact_table_name (
		id          mediumint(9) NOT NULL AUTO_INCREMENT,
		inst_id     int NOT NULL,
		lead_id     int NOT NULL,
		first_name  VARCHAR(20) NOT NULL,
		last_name   VARCHAR(20) NOT NULL,
		email       VARCHAR(50) NOT NULL,
		comment     text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	// load dbDelta function
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// dbDelta only runs queries to even things out (delta). does not drop + recreate tables
	dbDelta($inst_sql);
	dbDelta($inst_location_sql);
	dbDelta($inst_ip_sql);
	dbDelta($inst_order_sql);
	//dbDelta($inst_contact_sql);

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

/**
 * insert institution
 */
function insert_inst() {
	global $wpdb;
	global $inst_table_name;
	
	$push_data = array(
		'name' => (empty($_POST['name'])) ? 'Institution' : $_POST['name']
	);

	$wpdb->insert(
		$inst_table_name,
		$push_data
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_insert-inst', 'insert_inst');
add_action( 'wp_ajax_insert-inst', 'insert_inst');

/**
 * delete institution
 * @return [type] [description]
 */
function delete_inst() {
	global $wpdb;
	global $inst_table_name;

	$id = $_POST['id'];

	$wpdb->delete(
		$inst_table_name,
		array('id' => $id)
	);

	// need to delete all corresponding orders, ips, locations

	check_db_errors();
}
add_action( 'wp_ajax_nopriv_delete-inst', 'delete_inst');
add_action( 'wp_ajax_delete-inst', 'delete_inst');

/**
 * update institution
 * @return [type] [description]
 */
function update_inst() {
	global $wpdb;
	global $inst_table_name;

	$id = $_POST['id'];
	
	$push_data = array(
		'name' => (empty($_POST['name'])) ? 'Institution' : $_POST['name']
	);

	$wpdb->update(
		$inst_table_name,
		$push_data,
		array('ID' => $id),
		array('%s'),
		array('%d')
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_update-inst', 'update_inst');
add_action( 'wp_ajax_update-inst', 'update_inst');

/**
 * insert institution location
 * @return [type] [description]
 */
function insert_inst_location() {
	global $wpdb;
	global $inst_location_table_name;

	$inst_id = $_POST['id'];
	$description = $_POST['description'];

	$push_data = array(
		'inst_id' => $inst_id,
		'description' => $description,
		'continent' => '',
		'region' => '',
		'city' => '',
		'zip' => 0,
		'country' => '',
		'address' => ''
	);

	$wpdb->insert($inst_location_table_name, $push_data);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_insert-inst-location', 'insert_inst_location');
add_action( 'wp_ajax_insert-inst-location', 'insert_inst_location');

/**
 * update institution location info
 * @return [type] [description]
 */
function update_inst_location() {
	global $wpdb;
	global $inst_location_table_name;

	$id = $_POST['id'];
	$inst_id = $_POST['inst_id'];
	$description = $_POST['description'];
	$continent = $_POST['continent'];
	$region = $_POST['region'];
	$city = $_POST['city'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$address = $_POST['address'];

	$push_data = array(
		'inst_id' => $inst_id,
		'description' => $description,
		'continent' => $continent,
		'region' => $region,
		'city' => $city,
		'zip' => $zip,
		'country' => $country,
		'address' => $address
	);

	$wpdb->update(
		$inst_location_table_name, 
		$push_data,
		array('ID' => $id),
		array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
		array('%d')
	);
	check_db_errors();

}
add_action( 'wp_ajax_nopriv_update-inst-location', 'update_inst_location');
add_action( 'wp_ajax_update-inst-location', 'update_inst_location');

/**
 * delete institution location
 * @return [type] [description]
 */
function delete_inst_location() {
	global $wpdb;
	global $inst_location_table_name;

	$id = $_POST['id'];

	$wpdb->delete(
		$inst_location_table_name,
		array('ID'=>$id)
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_delete-inst-location', 'delete_inst_location');
add_action( 'wp_ajax_delete-inst-location', 'delete_inst_location');

/**
 * insert ip (mapped to location)
 * @return [type] [description]
 */
function insert_inst_ip() {
	global $wpdb;
	global $inst_ip_table_name;

	$location_id = $_POST['location_id'];
	$ip_start = $_POST['ip_start'];
	$ip_end = $_POST['ip_end'];

	//convert to storable long data type
	$ip_start = sprintf("%u", ip2long($ip_start));
	$ip_end = sprintf("%u", ip2long($ip_end));

	$push_data = array(
		'location_id' => $location_id,
		'start' => $ip_start,
		'end' => $ip_end
	);

	$wpdb->insert(
		$inst_ip_table_name,
		$push_data
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_insert-inst-ip', 'insert_inst_ip');
add_action( 'wp_ajax_insert-inst-ip', 'insert_inst_ip');

/**
 * update IP (mapped to location)
 * @return [type] [description]
 */
function update_inst_ip() {
	global $wpdb;
	global $inst_ip_table_name;

	$id = $_POST['id'];
	$location_id = $_POST['location_id'];
	$ip_start = $_POST['ip_start'];
	$ip_end = $_POST['ip_end'];

	//convert to storable long data type
	$ip_start = sprintf("%u", ip2long($ip_start));
	$ip_end = sprintf("%u",ip2long($ip_end));

	$push_data = array(
		'location_id' => $location_id,
		'start' => $ip_start,
		'end' => $ip_end
	);

	$wpdb->update(
		$inst_ip_table_name,
		$push_data,
		array('ID' => $id),
		array('%d', '%f', '%f'),
		array('%d')
	);

	check_db_errors();
}
add_action( 'wp_ajax_nopriv_update-inst-ip', 'update_inst_ip');
add_action( 'wp_ajax_update-inst-ip', 'update_inst_ip');

/**
 * delete IP (mapped to location)
 * @return [type] [description]
 */
function delete_inst_ip() {
	global $wpdb;
	global $inst_ip_table_name;

	$id = $_POST['id'];

	$wpdb->delete(
		$inst_ip_table_name,
		array('ID' => $id)
	);

	check_db_errors();
}
add_action( 'wp_ajax_nopriv_delete-inst-ip', 'delete_inst_ip');
add_action( 'wp_ajax_delete-inst-ip', 'delete_inst_ip');

/**
 * insert institution order
 * @return [type] [description]
 */
function insert_inst_order() {
	global $wpdb;
	global $inst_order_table_name;

	$inst_id = $_POST['inst_id'];
	$location_id = $_POST['location_id'];
	$user_id = $_POST['user_id'];
	$date_start = $_POST['date_start'];
	$date_end = $_POST['date_end'];
	$type = $_POST['type'];
	$amount = $_POST['amount'];

	$push_data = array(
		'inst_id' => $inst_id,
		'location_id' => $location_id,
		'user_id' => $user_id,
		'date_start' => $date_start,
		'date_end' => $date_end,
		'type' => $type,
		'amount' => $amount
 	);

 	$wpdb->insert(
 		$inst_order_table_name,
 		$push_data
 	);

 	check_db_errors();
}
add_action( 'wp_ajax_nopriv_insert-inst-order', 'insert_inst_order');
add_action( 'wp_ajax_insert-inst-order', 'insert_inst_order');

/**
 * update institution order
 * @return [type] [description]
 */
function update_inst_order() {
	global $wpdb;
	global $inst_order_table_name;

	$id = $_POST['id'];

	$inst_id = $_POST['inst_id'];
	$location_id = $_POST['location_id'];
	$user_id = $_POST['user_id'];
	$date_start = $_POST['date_start'];
	$date_end = $_POST['date_end'];
	$type = $_POST['type'];
	$amount = $_POST['amount'];

	$push_data = array(
		'inst_id' => $inst_id,
		'location_id' => $location_id,
		'user_id' => $user_id,
		'date_start' => $date_start,
		'date_end' => $date_end,
		'type' => $type,
		'amount' => $amount
 	);

 	$wpdb->update(
 		$inst_order_table_name,
 		$push_data,
 		array('ID' => $id),
 		array('%d', '%d', '%d', '%s', '%s', '%s', '%f'),
 		array('%d')
 	);

 	check_db_errors();
}	
add_action( 'wp_ajax_nopriv_update-inst-order', 'update_inst_order');
add_action( 'wp_ajax_update-inst-order', 'update_inst_order');

/**
 * delete institution order
 * @return [type] [description]
 */
function delete_inst_order() {
	global $wpdb;
	global $inst_order_table_name;

	$id = $_POST['id'];

	$wpdb->delete(
		$inst_order_table_name,
		array('ID' => $id)
	);

	check_db_errors();
}	
add_action( 'wp_ajax_nopriv_delete-inst-order', 'delete_inst_order');
add_action( 'wp_ajax_delete-inst-order', 'delete_inst_order');

?>