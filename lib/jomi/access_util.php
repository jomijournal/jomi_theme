<?php

/**
 * HELPER FUNCTIONS
 */

global $wpdb;
global $access_db_version;
global $access_table_name;

// default values.
// edit this array to modify default behavior
$default = array(
	'result_type' => 'DEFAULT',
	'result_time_start' => -1,
	'result_time_end' => -1,
	'result_time_elapsed' => -1,
	'result_msg' => '<p>DEFAULT</p>',
	'check_type' => 'none',
	'check_value' => 'none',
	'priority' => 0,
	'selector_type' => 'none',
	'selector_value' => 'none'
);

/**
 * match $_POST inputs against defaults and returns
 * @return [array] processed post data
 */
function process_access_post_data() {

	$result_type =          (!isset($_POST['result_type'])         || empty($_POST['result_type']))         ? $default['result_type']         : $_POST['result_type'];
	$result_time_start =    (!isset($_POST['result_time_start'])   || empty($_POST['result_time_start']))   ? $default['result_time_start']   : $_POST['result_time_start'];
	$result_time_end =      (!isset($_POST['result_time_end'])     || empty($_POST['result_time_end']))     ? $default['result_time_end']     : $_POST['result_time_end'];
	$result_time_elapsed =  (!isset($_POST['result_time_elapsed']) || empty($_POST['result_time_elapsed'])) ? $default['result_time_elapsed'] : $_POST['result_time_elapsed'];
	if(empty($_POST['result_msg'])) {
		switch ($result_type) {
			case 'DENY':
			case 'deny':
				$result_msg = "<strong>DENIED</strong>";
				break;
			case 'sign_up':
				$result_msg = "<strong>SIGN UP</strong>";
				break;
			case 'none':
			case '':
			case $default['result_type']:
			default:
				$result_msg = $default['result_msg'];
				break;
		}
	} else {
		$result_msg = $_POST['result_msg'];
	}
	$check_type =     (!isset($_POST['check_type'])     || empty($_POST['check_type']))     ? $default['check_type']     : $_POST['check_type'];
	$check_value =    (!isset($_POST['check_value'])    || empty($_POST['check_value']))    ? $default['check_value']    : $_POST['check_value'];
	$priority =       (!isset($_POST['priority'])       || empty($_POST['priority']))       ? $default['priority']       : $_POST['priority'];
	$selector_type =  (!isset($_POST['selector_type'])  || empty($_POST['selector_type']))  ? $default['selector_type']  : $_POST['selector_type'];
	$selector_value = (!isset($_POST['selector_value']) || empty($_POST['selector_value'])) ? $default['selector_value'] : $_POST['selector_value'];

	$out = array(
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
	);
	return $out;
}

/**
 * check if the _POST id var isset or is empty
 * @return [type] [description]
 */
function check_post_id() {
	// no id passed in
	if(!isset($_POST['id']) || empty($_POST['id'])) {
		return false;
	} else {
		$id = $_POST['id'];
		return $id;
	}
}

/**
 * [check_db_errors description]
 * @return [type] [description]
 */
function check_db_errors() {
	// print errors if any show up
	//if(!empty($wpdb->print_error())) {
	//	return $wpdb->print_error();
	//}
}

?>