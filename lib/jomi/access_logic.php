<?php

global $wpdb;
global $access_db_version;
global $access_table_name;

// load debug option
global $access_debug;
$access_debug = get_option('access_debug');
$access_debug = ($access_debug === "true") ? true : false;

/**
 * ACCESS LOGIC
 */


/**
 * super function that calls everything
 * this is the entry point for the access system. called in by article.php
 * @return [type] [description]
 */
function check_access() {
  global $wpdb;
  global $access_table_name;
  global $access_blocks;

  // debug flag
  // will print a bunch of useful info at the top of article pages
  global $access_debug;
  // if not admin, then don't display at all
  global $is_user_admin;
  if(!$is_user_admin) $access_debug = false;

  // if debug flag is set, then display i guess
  if(!empty($_GET['showdebug'])) {
  	if($_GET['showdebug'] == 'true') $access_debug = true;
  	else $access_debug = false;
  }

  // load metadata on the current article being viewed
  $selector_meta = extract_selector_meta(get_the_ID());

  if($access_debug) echo '<pre>';

  // extract user IP and matching institution (if applicable)
  $institution_meta = extract_institution_meta();

  // get rules from the access database table
  $rules = collect_rules($selector_meta, $institution_meta);

  if($access_debug) {
  	echo "Rules to be checked:\n";
  	print_r($rules);
  }

  // load user info, if the user is logged in
  $user_info = load_user_info();

  if($access_debug) {
  	echo "User Meta:\n";
  	print_r($user_info);
  }

  // load blocks to be applied
  $access_blocks = array();
  // filter those blocks with the per-user and institutional info
  $access_blocks = get_blocks($rules, $user_info);

  if($access_debug) {
  	echo "Applied Blocks:\n";
  	print_r($access_blocks);
  }

  // FOR DEBUGGING ONLY. STOPS ALL BLOCKS FROM LOADING
  //$blocks = array();

  if($access_debug) echo '</pre>';
}

/**
 * get useful article meta to help comb through article access rules
 * this is executed during "the loop", so getting any other meta not included here should be straightforward
 * @param  [int] $id article id
 * @return [array] (category, id, status, author)
 */
function extract_selector_meta($id) {

	// TODO: match up against defaults?
	
	// get publication id from the ACF field
	$publication_id = get_field('publication_id');

	// get all applicable categories
	$categories = get_the_category($id);
	$cat_ids = array();
	$cat_slugs = array();
	$cat_names = array();
	foreach($categories as $category) {
		// extract ids, slugs, and names
		array_push($cat_ids, $category->cat_ID);
		array_push($cat_slugs, $category->slug);
		array_push($cat_names, $category->name);
	}

	// if post status doesn't exist, set to draft
	$status = (get_post_status($id) == false) ? 'draft' : get_post_status($id);

	// get all applicable coauthors
	$coauthors = get_coauthors($id);
	$coauth_out = array();
	foreach($coauthors as $coauthor) {
		// extract coauthor names
		array_push($coauth_out, $coauthor->ID);
	}

	// package all extracted meta
	$selector_meta = array(
		'id' => $id,
		'pub_id' => $publication_id,
		'cat_ids' => $cat_ids,
		'cat_slugs' => $cat_slugs,
		'cat_names' => $cat_names,
		'status' => $status,
		'author' => $coauth_out,
	);
	// send it out
	return $selector_meta;
}
/**
 * use the user IP to get institution meta
 * can probably cache the result of this in the future
 * @return [int] institution ID (corresponds with row ID in the DB)
 */
function extract_institution_meta() {

	global $access_debug;

	// grab and filter user ip
	$ip = $_SERVER['REMOTE_ADDR'];
	$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	// use debug ips if available
	if(!empty(get_option('access_debug_ip'))) $ip = get_option('access_debug_ip');
	if(!empty($_GET['testip'])) $ip = $_GET['testip'];

	// turn ip into a computer-readable form
	$ip_long = sprintf("%u", ip2long($ip));
	if($access_debug) echo 'Current IP:' . $ip_long . "\n";

	// set global ip flags
	global $user_ip;
	$user_ip = $ip;
	global $user_ip_long;
	$user_ip_long = $ip_long;

	//check current ip with cached session ip. if matching, then get institution data from cache and return
	if(empty($_SESSION['ip']) || empty($_SESSION['ip_long'])) {

	} elseif($ip == $_SESSION['ip'] || $ip_long == $_SESSION['ip_long']) {
		if(!empty($_SESSION['inst']) 
			&& !empty($_SESSION['inst_ip']) 
			&& !empty($_SESSION['location']) 
			&& !empty($_SESSION['order']) 
			&& !empty($_SESSION['is_subscribed'])) {

			global $user_inst;
			$user_inst = array(
				  'inst'          => $_SESSION['inst']
				, 'ip'            => $_SESSION['inst_ip']
				, 'location'      => $_SESSION['location']
				, 'order'         => $_SESSION['order']
				, 'is_subscribed' => $_SESSION['is_subscribed']
			);

			if($access_debug) {
				echo "Loading institutions from cache...\n";
				print_r($user_inst);
			}

			return;
		}
	} else {
		reset_session();
	}


	//load institution database globals
	global $wpdb;
	global $inst_ip_table_name;
	global $inst_location_table_name;
	global $inst_order_table_name;
	global $inst_table_name;

	//** LOAD MATCHING IPS

	// query database for user's IP
	$ip_query = "SELECT * FROM $inst_ip_table_name 
	WHERE $ip_long BETWEEN start AND end";
	$inst_ips = $wpdb->get_results($ip_query);

	// only use the first result
	$inst_ip = $inst_ips[0];

	if($access_debug) {
		echo "Institution IP data:\n";
		print_r($inst_ip);
	}

	//** LOAD INSTITUTION LOCATIONS

	$inst_locations = array();
	if(empty($inst_ips)) {
		// no ips matched
	} else { 

		// load location id from matched ip entry
		$location_id = $inst_ip->location_id;

		// query DB for location
		$location_query = 
		"SELECT * FROM $inst_location_table_name
		WHERE id=$location_id";
		$inst_locations = $wpdb->get_results($location_query);

		//only use first location result. the query should only produce one entry anyways
		$inst_location = $inst_locations[0];

		if($access_debug) {
			echo "Institution Location Data:\n";
			print_r($inst_location);
		}
	}

	//** LOAD MATCHING ORDER ENTRIES
	
	$inst_orders = array();
	if(empty($inst_locations)) {

	} else {
		// load location id
		$location_id = $inst_location->id;

		// query DB for matching orders
		$order_query = 
		"SELECT * FROM $inst_order_table_name
		WHERE location_id=$location_id";
		$inst_orders = $wpdb->get_results($order_query);

		if($access_debug) {
			echo "Institution Order History:\n";
			print_r($inst_orders);
		}
	}
	
	//** CHECK VALIDITY OF ORDER
	
	// not subscribed by default. will flip when conditions are met
	$is_subscribed = false;

	if(empty($inst_orders)) {
		
	} else {
		// grab today's date and time
		$cur_time = time();

		// loop thru each order. loop will break once a valid order is found
		foreach($inst_orders as $inst_order) {

			// check if order falls within today's date
			$fromtime = strtotime($inst_order->date_start);
			$endtime = strtotime($inst_order->date_end);
			if ($cur_time >= $fromtime && $cur_time <= $endtime) {
				// order is valid, break loop
				$is_subscribed = true;
				break;
			} 
		}
	}

	//** GRAB INSTITUTION OBJECT (structure that groups locations)
	
	if(!empty($inst_ip) && !empty($inst_location) && !empty($inst_order)) {

		// grab institution id from location
		$inst_id = $inst_location->inst_id;

		// query DB for institution
		$inst_query = "SELECT * FROM $inst_table_name WHERE id=$inst_id";
		$insts = $wpdb->get_results($inst_query);

		//only need one result
		$inst = $insts[0];

		if($access_debug) {
			echo "Institution Name:\n";
			print_r($inst);
		}

		// package data into a global
		global $user_inst;
		$user_inst = array(
			'inst' => $inst,
			'ip' => $inst_ip,
			'location' => $inst_location,
			'order' => $inst_order,
			'is_subscribed' => $is_subscribed
		);

		//if($access_debug) {
		//	echo "packaged institution data:\n";
		//	print_r($user_inst);
		//}

		// set php session data, if changed
		if(empty($_SESSION['inst']))          $_SESSION['inst']          = $user_inst['inst'];
		if(empty($_SESSION['inst_ip']))       $_SESSION['inst_ip']       = $user_inst['ip'];
		if(empty($_SESSION['location']))      $_SESSION['location']      = $user_inst['location'];
		if(empty($_SESSION['order']))         $_SESSION['order']         = $user_inst['order'];
		if(empty($_SESSION['is_subscribed'])) $_SESSION['is_subscribed'] = $user_inst['is_subscribed'];

		if(empty($_SESSION['ip']))      $_SESSION['ip']      = $ip;
		if(empty($_SESSION['ip_long'])) $_SESSION['ip_long'] = $ip_long;

	}
}


/**
 * collect, sort, and concatenate the rules applying to this article
 * @param  [array] $selector_meta    selector meta object grabbed from extract_selector_meta
 * @param  [array] $institution_meta institution meta object grabbed from extract_institution_meta
 * @return [type]                   [description]
 */
function collect_rules($selector_meta, $institution_meta) {

	global $wpdb;
	global $access_table_name;

	//** Build the SQL query that will search for rules in the database, that apply to the current article's creds

	// init conditional
	$where_conditional = "(selector_type, selector_value) IN (";

	// match automatically if "all" is set
	$where_conditional .= "('all', ''),";

	// match category IDs
	$cat_ids = $selector_meta['cat_ids'];
	foreach($cat_ids as $index => $cat_id) {
		$where_conditional .= "('category', $cat_id),";
	}

	// match wordpress post ID
	$id = $selector_meta['id'];
	$where_conditional .= "('article_id', $id),";

	// match jomi-set publication ID
	$pub_id = $selector_meta['pub_id'];
	$where_conditional .= "('pub_id', $pub_id),";

	// match article post status
	$status = $selector_meta['status'];
	$where_conditional .= "('post_status', '$status'),";

	// match author IDs
	$authors = $selector_meta['author'];
	foreach($authors as $author) {
		$where_conditional .= "('author', $author),";
	}

	// cap it off
	$where_conditional .= "('-1','-1'))";

	// build query
	$rules_query ="SELECT * 
						FROM $access_table_name 
						WHERE $where_conditional 
						ORDER BY priority DESC";

	// collect matches and return
	$rules = $wpdb->get_results($rules_query);

	return $rules;
}

/**
 * load user info based on their Wordpress login info
 * @return [type] [description]
 */
function load_user_info() {
	global $wpdb;
	global $access_debug;
	global $reader;
	
	// grab user object from wordpress
	$current_user = wp_get_current_user();

	// grab debug users if available. these are set individually via GET
	// will also set logged in to true, so it won't trip the idiotproofing in the logic that follows
	if(!empty($_GET['testlogin'])) {
		$current_user = get_user_by('login', $_GET['testlogin']);
	}
	if(!empty($_GET['testemail'])) {
		$current_user = get_user_by('email', $_GET['testemail']);
	}
	if(!empty($_GET['testuserid'])) {
		$current_user = get_user_by('id', $_GET['testuserid']);
	}


	if ( ($current_user instanceof WP_User) ) {
		// package critical info
		$user = array(
			'login' => $current_user->user_login,
			'email' => $current_user->user_email,
			'display_name' => $current_user->display_name,
			'id' => $current_user->ID
		);
	} else {
		// package defaults
		$user = array(
			'login' => 'none',
			'email' => 'none',
			'display_name' => 'none',
			'id' => 'none'
		);
	}

	// set logged_in flag
	if(is_user_logged_in()){
		$logged_in = true;
	} else {
		$logged_in = false;
	}

	if($logged_in) {
		$stripe_subscribed = stripe_verify_user_subscribed();

		if($stripe_subscribed) $is_subscribed = true;

		if($access_debug) {
			echo "User Stripe Subscribed:\n";
			echo $stripe_subscribed . "\n";
		}
	}


	/****
	* GEOIP SNATCHIN
	****/

	// load ip grabbed from extract_institution_meta;
	global $user_ip;
	$ip = $user_ip;

	// if global doesn't exist
	if(empty($ip)) {
		// grab and filter user ip
		$ip = $_SERVER['REMOTE_ADDR'];
		$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

		// use debug ips if available
		if(!empty(get_option('access_debug_ip'))) $ip = get_option('access_debug_ip');
		if(!empty($_GET['testip'])) $ip = $_GET['testip'];
	}

	// attempt to query GeoLite IP database
	try {
		// query the database reader
		$record = $reader->city($ip);

		// package country data
		$country = array (
			'iso' => $record->country->isoCode,
			'name' => $record->country->name
		);

		// package region (state) data
		$region = array (
		'iso' => $record->mostSpecificSubdivision->isoCode,
		'name' => $record->mostSpecificSubdivision->name
		);

		// package continent data
		$continent = array (
		'iso' => $record->continent->code,
		'name' => $record->continent->name
		);	

		// package city data
		$city = $record->city->name;

	} catch (Exception $e) {
		// if can't find, default to Boston, MA, US
		$country = array(
			'iso' => 'US',
			'name' => 'United States'
		);
		$region = array(
			'iso' => 'MA',
			'name' => 'Massachusetts'
		);
		$continent = array(
			'iso' => 'NA',
			'name' => 'North America'
		);
		$city = 'Boston';

	}

	// apply various debugs, if they exist
	$country['name'] = (empty($_GET['testcountry'])) ? $country['name'] : $_GET['testcountry'];
	$region['name'] = (empty($_GET['testregion'])) ? $region['name'] : $_GET['testregion'];
	$continent['name'] = (empty($_GET['testcontinent'])) ? $continent['name'] : $_GET['testcontinent'];
	$logged_in  = (empty($_GET['testloggedin']))   ? $logged_in  : $_GET['testloggedin'];
	// correct for strings passed in by get
	if($logged_in === "true" || $logged_in === "1") {
		$logged_in = true;
	} elseif ($logged_in === "false" || $logged_in === "0") {
		$logged_in = false;
	}


	$is_subscribed = (empty($_GET['testsubscribed'])) ? $is_subscribed : $_GET['testsubscribed'];
	// correct for strings passed in by get
	if($is_subscribed == "true" || $is_subscribed == "1") {
		$is_subscribed = true;
	} elseif ($is_subscribed == "false" || $is_subscribed == "0") {
		$is_subscribed = false;
	}

	// package user info
	$user_info = array(
		'logged_in' => $logged_in,
		'subscribed' => $is_subscribed,
		'user' => $user,
		'institution' => $institution,
		'ip' => $ip,
		'country' => $country,
		'region' => $region,
		'continent' => $continent,
		'city' => $city
	);
	return $user_info;
}


/**
 * use rules to check access to article
 * @param  [type] $rules [description]
 * @param  [type] $user_info array of user/session data to check against
 * @return [array] $blocks a list of block objects to apply
 */
function get_blocks($rules, $user_info) {

	global $access_debug;

	global $user_inst;

	// dont even try if no rules, or user data is present
	if(empty($rules)) {
		return;
	}
	if(empty($user_info)) {
		return;
	}

	$blocks = array();

	foreach($rules as $rule) {
		// check for invalid/empty result first and skip over if so
		switch($rule->result_type) {
			case '':
			case 'None':
			case 'NONE':
			case 'Default':
			case 'DEFAULT':
				//return;
				continue;
				break;
		}

		//check for negative priority. this skips checking the rule altogether
		if($rule->priority < 0) continue;

		// TODO: check for invalid time results
		
		// SPLIT UP CHECK TYPES
		$check_types = explode(',', $rule->check_type);
		$check_values = explode('|', $rule->check_value);

		// track how many checks have been met
		// block will only register if all checks have been met
		$checks = count($check_types);
		$check_count = 0;

		foreach($check_types as $index => $check_type) {

			if($access_debug) {
				echo "checking " . $check_type . " against " . $check_values[$index] . "\r\n";
			}

			switch($check_type) {
				// IP check
				case 'is_ip':
					$ip_check = $user_info['ip'];
					$ips = explode(',', $check_values[$index]);

					foreach($ips as $ip) {
						if($ip_check == $ip) {
							if($access_debug) {
								echo "ip matched\n";
								echo $ip . " == " . $ip_check . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "ips dont match\n";
								echo $ip . " != " . $ip_check . "\r\n\r\n";
							}
						}
					}
					break;

				// institution check
				case 'is_institution':
					$institution_check = $user_info['institution'];
					$institutions = explode(',', $check_values[$index]);

					foreach($institutions as $institution) {
						// TODO institution check
					}
					break;

				// country check. checks ISO and name (name is case sensitive)
				case 'is_country':
					$country_check = $user_info['country'];
					$countries = explode(",", $check_values[$index]);

					foreach($countries as $country) {
						if($country_check['iso'] == $country or $country_check['name'] == $country) {
							if($access_debug) {
								echo "country matched\n";
								echo $country . ' == ' . $country_check . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "country not matched\n";
								echo $country . ' != ' . $country_check . "\r\n\r\n";
							}
						}
					}
					break;

				// region (state) check. checks ISO and name (name is case sensitive)
				case 'is_region':
					$region = $user_info['region'];
					$region_checks = explode(',', $check_values[$index]);

					foreach($region_checks as $region_check) {
						if($region['iso'] == $region_check || $region['name'] == $region_check) {
							if($access_debug) {
								echo "region matched!\n";
								echo $region . ' == ' . $region_check . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "region not matched!\n";
								echo $region . ' != ' . $region_check . "\r\n\r\n";
							}
						}
					}

					break;

				// continent check. checks ISO and name (name is case sensitive)
				case 'is_continent':
					$continent = $user_info['continent'];
					$continent_checks = explode(',', $check_values[$index]);

					foreach($continent_checks as $continent_check) {
						if($continent['iso'] == $continent_check || $continent['name'] == $continent_check) {
							if($access_debug) {
								echo "continent matched!\n";
								echo $continent . ' == ' . $continent_check . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "continent not matched!\n";
								echo $continent . ' != ' . $continent_check . "\r\n\r\n";
							}
						}
					}

					break;

				// checks user. matches against user login (username), email, display name, and ID. if any of these match, user is considered matched.
				case 'is_user':

					$user_check = $user_info['user'];

					$users = explode(",", $check_values[$index]);
					foreach($users as $user) {
						if(($user_check['login'] == $user or
						   $user_check['email'] == $user or
						   $user_check['display_name'] == $user or
						   $user_check['id'] == $user) && $user_info['logged_in']) {
							if($access_debug) {
								echo "user matched\n";
								echo $user_check . ' == ' . $user . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "user not matched\n";
								echo $user_check . ' != ' . $user . "\r\n\r\n";
							}
						}
					}
					break;

				// checks if user is logged in or not.
				case 'is_logged_in':

					$logged_in_check = $user_info['logged_in'];
					$logged_ins = explode(',', $check_values[$index]);
					foreach($logged_ins as $logged_in) {
						if(($logged_in == 'T' && $logged_in_check) || ($logged_in == 'F' && !$logged_in_check)) {
							if($access_debug) {
								echo "loggedin matched!\n";
								echo $logged_in_check . ' == ' . $logged_in . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "loggedin not matched!\n";
								echo $logged_in_check . ' != ' . $logged_in . "\r\n\r\n";
							}
						}
					}

					break;

				// checks if the user has a per-user subscription, or is part of a subscribing institution
				case 'is_subscribed':

					$user_subscribed = $user_info['subscribed'];
					$inst_subscribed = $user_inst['is_subscribed'];
					$check_subscribed = $check_values[$index];

					if($user_subscribed != null) {
						if(($check_subscribed == 'T' && $user_subscribed) || ($check_subscribed == 'F' && !$user_subscribed)) {
							if($access_debug) {
								echo "user subscribed matched!\n";
								echo $check_subscribed . ' == ' . $user_subscribed . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "user subscribed not matched!\n";
								echo $check_subscribed . ' != ' . $user_subscribed . "\r\n\r\n";
							}
						}
					} elseif($inst_subscribed != null) {
						if(($check_subscribed == 'T' && $inst_subscribed) || ($check_subscribed == 'F' && !$inst_subscribed)) {
							if($access_debug) {
								echo "inst subscribed matched!\n";
								echo $check_subscribed . ' == ' . $inst_subscribed . "\r\n\r\n";
							}
							$check_count++;
						} else {
							if($access_debug) {
								echo "inst subscribed not matched!\n";
								echo $check_subscribed . ' != ' . $inst_subscribed . "\r\n\r\n";
							}
						}
					} else {
						if($check_subscribed == 'F') {
							if($access_debug) {
								echo "not subscribed matched!\r\n\r\n";
							}
							$check_count++;
						}
					}

					break;

				// ADD ADDITIONAL CHECKS HERE

				default:
					echo "invalid check type";
					break;

			//END SWITCH
			}
		//END FOREACH
		}

		if($access_debug) echo 'checks passed: ' . $check_count . '/' . $checks . "\r\n\r\n";

		// if all checks have been met
		// load blocks into blocks array
		if($check_count == $checks) {
			array_push($blocks, array(
				'msg' => $rule->result_msg,
				'time_start' => $rule->result_time_start,
				'time_elapsed' => $rule->result_time_elapsed,
				'closable' => $rule->result_closable
			));
		}
	}
	
	//remove dupes
	$no_more_duplicates = false;
	while(!$no_more_duplicates) {
		foreach($blocks as $block) {
			foreach($blocks as $index_check => $block_check) {
				// dont check yerself
				if($block_check == $block) continue;
				if($block_check['time_start'] == $block['time_start'] &&
				   $block_check['time_elapsed'] == $block['time_elapsed']) {

					array_splice($blocks, $index_check, 1);
					break 2;
				}
			}
		}
		$no_more_duplicates = true;
	}
	return $blocks;
}


?>