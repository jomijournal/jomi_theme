<?php

global $access_db_version;
$access_db_version = '1.01';

function access_table_install() {
	global $wpdb;
	global $access_db_version;

	$table_name = $wpdb->prefix . 'article_access';
	
	/*
	 * We'll set the default character set and collation for this table.
	 * If we don't do this, some characters could end up being converted 
	 * to just ?'s when saved in our table.
	 */
	$charset_collate = '';

	if ( ! empty( $wpdb->charset ) ) {
	  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}

	if ( ! empty( $wpdb->collate ) ) {
	  $charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$sql = "CREATE TABLE $table_name (
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
 * 'result_type' => [string] 
 * 		- ('DENY', 'NONE', etc...). 
 * 		- the type of the result. used to standardize msg outputs
 * 		- default: 'DEFAULT'
 * 'result_time_start' => [int] 
 * 		- the time, in seconds, of when to allow users to start watching
 * 		- default: -1. allow users to start anywhere
 * 'result_time_end' => [int] 
 * 		- the time, in seconds, of when stop users from watching
 * 		- default: -1. allow user to watch until end
 * 'result_time_elapsed' => [int]
 * 		- the time, in seconds, of how much of the video the user is allowed to watch
 * 		- default: -1. allow user to watch any amount of video
 * 'result_msg' => [string]
 * 		- the message, in HTML, to display when blocked.
 * 		- default: ''. use the msg determined by the result_type
 * 'check_type' => [string]
 * 		- the type of check to perform. ex, 'region', 'ip', 'user', 'country'
 * 		- default: ''. perform no check
 * 'check_value' => [string]
 * 		- the values to check against, as a CSV. ex, 'US,AS,AF' or '193.345.34.32,325.34.23.43,23.4.43.43'
 * 		- default: ''. perform no check
 * 'priority' => [int]
 * 		- the order of when this rule is enforced. lower number means it will be enforced earlier. ex, -10 will be enforced earlier than 5
 * 		- default: '0'. medium priority.
 * 'selector_type' => [string]
 * 		- the type of selector to apply. selectors determine what articles this rule will apply to.
 * 		- default: 'NONE'. apply to nothing
 * 'selector_value' => [string]
 * 		- 'the value to check against, as a CSV.'
 * 		- default: ''. apply to nothing
 * 
 * @return [type]       [description]
 */
function insert_rule($args) {

	global $wpdb;

	// default values.
	// edit this array to modify default behavior
	$default = array(
		'result_type' => 'DEFAULT',
		'result_time_start' => -1,
		'result_time_end' => -1,
		'result_time_elapsed' => -1,
		'result_msg' => '<p>DEFAULT</p>',
		'check_type' => '',
		'check_value' => '',
		'priority' => 0,
		'selector_type' => '',
		'selector_value' => ''
	);
	$result_type = (empty($args['result_type'])) ? $default['result_type'] : $args['result_type'];
	$result_time_start = (empty($args['result_time_start'])) ? $default['result_time_start'] : $args['result_time_start'];
	$result_time_end = (empty($args['result_time_end'])) ? $default['result_time_end'] : $args['result_time_end'];
	$result_time_elapsed = (empty($args['result_time_elapsed'])) ? $default['result_time_elapsed'] : $args['result_time_elapsed'];
	if(empty($args['result_msg'])) {
		switch ($result_type) {
			case $default['result_type']:
			default:
				$result_msg = $default['result_msg'];
		}
	} else {
		$result_msg = $args['result_msg'];
	}
	$check_type = (empty($args['check_type'])) ? $default['check_type'] : $args['check_type'];
	$check_value = (empty($args['check_value'])) ? $default['check_value'] : $args['check_value'];
	$priority = (empty($args['priority'])) ? $default['priority'] : $args['priority'];
	$selector_type = (empty($args['selector_type'])) ? $default['selector_type'] : $args['selector_type'];
	$selector_value = (empty($args['selector_value'])) ? $default['selector_value'] : $args['selector_value'];

	$table_name = $wpdb->prefix . 'article_access';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'result_type' => $result_type,
			'result_time_start' => $result_time_start,
			'result_time_end' => $result_time_end,
			'result_time_elapsed' => $result_time_elapsed,
			'result_msg' => $result_msg,
			'check_type' => $check_type,
			'check_value' => $check_value,
			'priority' => $priority,
			'selector_type' => $selector_type,
			'selector_value' => $selector_value
		) 
	);

	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}
}

// DEBUG ONLY: insert an empty rule
//insert_rule(array());

?>