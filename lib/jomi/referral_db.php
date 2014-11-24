<?php
/**
 * REFERRAL DB
 */
?>

<?php

global $wpdb;

global $referral_db_version;
$referral_db_version = '1.00';

global $referral_table_name;
$referral_table_name = $wpdb->prefix . "user_referrals";

/**
 * create the table that houses user referrals
 * @return [type] [description]
 */
function referral_table_install() {
	global $wpdb;
	global $referral_db_version;
	global $referral_table_name;

	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}
	$sql = "CREATE TABLE $referral_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT
		, user_id int NOT NULL
		, refer_code varchar(10) NOT NULL
		, referred_by int NOT NULL
		, num_referrals int NOT NULL
		, UNIQUE KEY id (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'referral_db_version', $referral_db_version );
}
/**
 * runs the table install if the version numbers dont match
 * @return [type] [description]
 */
function update_referral_table() {
	global $referral_db_version;
	if(get_option('referral_db_version') != $referral_db_version) {
		referral_table_install();
	}
}
add_action('init', 'update_referral_table');

/**
 * insert a referral
 * @param  array $args [description]
 * @return [type]       [description]
 */
function insert_referral() {

	global $wpdb;
	global $referral_table_name;

	$push_data = process_referral_post_data();
	
	$wpdb->insert( 
		$referral_table_name, 
		$push_data
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_insert-referral', 'insert_referral' );
add_action( 'wp_ajax_insert-referral', 'insert_referral' );

/**
 * [delete_referral description]
 * @return [type] [description]
 */
function delete_referral() {
	global $wpdb;
	global $referral_table_name;

	$id = check_post_id();

	$wpdb->delete(
		$referral_table_name,
		array('id' => $id)
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_delete-referral', 'delete_referral' );
add_action( 'wp_ajax_delete-referral', 'delete_referral' );

/**
 * update an article referral referral in the db
 * @return [type] [description]
 */
function update_referral() {
	global $wpdb;
	global $referral_table_name;

	$id = check_post_id();
	
	$push_data = process_referral_post_data();

	$wpdb->update(
		$referral_table_name
		, $push_data
		, array('ID' => $id)
	);
	check_db_errors();
}
add_action( 'wp_ajax_nopriv_update-referral', 'update_referral' );
add_action( 'wp_ajax_update-referral', 'update_referral' );

/**
 * collect and package post data sent from ajax
 * @return [type] [description]
 */
function process_referral_post_data() {

	$user_id = $_POST['user_id'];
	$refer_code = $_POST['refer_code'];
	$referred_by = $_POST['referred_by'];
	$num_referrals = $_POST['num_referrals'];

	$out = array(
		'user_id' => $user_id
		, 'refer_code' => $refer_code
		, 'referred_by' => $referred_by
		, 'num_referrals' => $num_referrals
	);

	return $out; 
}


?>