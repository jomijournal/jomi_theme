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
 * get useful article meta to help comb through article access rules
 * @param  [int] $id article id
 * @return [array] (category, id, status, author)
 */
function extract_selector_meta($id) {

	// TODO: match up against defaults?
	
	$publication_id = get_field('publication_id');

	$categories = get_the_category($id);
	$cat_ids = array();
	$cat_slugs = array();
	$cat_names = array();
	foreach($categories as $category) {
		//$category = ($category == '') ? '' : $category;
		array_push($cat_ids, $category->cat_ID);
		array_push($cat_slugs, $category->slug);
		array_push($cat_names, $category->name);
	}
	$status = (get_post_status($id) == false) ? '' : get_post_status($id);
	$coauthors = get_coauthors($id);
	$coauth_out = array();
	foreach($coauthors as $coauthor) {
		array_push($coauth_out, $coauthor->ID);
	}

	$selector_meta = array(
		'id' => $id,
		'pub_id' => $publication_id,
		'cat_ids' => $cat_ids,
		'cat_slugs' => $cat_slugs,
		'cat_names' => $cat_names,
		'status' => $status,
		'author' => $coauth_out,
	);
	return $selector_meta;
}
/**
 * use the user IP to get institution meta
 * can probably cache the result of this in the future
 * not useful yet
 * @return [int] institution ID (corresponds with row ID in the DB)
 */
function extract_institution_meta() {
	$ip = $_SERVER['REMOTE_ADDR'];

	// TODO: query institution table and get the institution rules
	
	$out = array(
		// institution ID
		'id' => 0
	);
	return $out;
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

  // init conditional
  $where_conditional = "(selector_type, selector_value) IN (";

  // all
  $where_conditional .= "('all', ''),";
  // categories
  $cat_ids = $selector_meta['cat_ids'];
  //$cat_slugs = $selector_meta['cat_slugs'];
  //$cat_names = $selector_meta['cat_names'];
  foreach($cat_ids as $index => $cat_id) {
  	$where_conditional .= "('category', $cat_id),";
  	//$cat_slug = $cat_slugs[$index];
  	//$where_conditional .= "('category', $cat_slug),";
  	//$cat_name = $cat_names[$index];
  	//$where_conditional .= "('category', $cat_name),";
  }
  // article id
  $id = $selector_meta['id'];
  $where_conditional .= "('article_id', $id),";
  // publication id
  $pub_id = $selector_meta['pub_id'];
  $where_conditional .= "('pub_id', $pub_id),";
  // status
  $status = $selector_meta['status'];
  $where_conditional .= "('post_status', '$status'),";
  // authors
  $authors = $selector_meta['author'];
  foreach($authors as $author) {
  	$where_conditional .= "('author', $author),";
  }
  // TODO: add institution meta cond. here:

  // cap it off
  $where_conditional .= "('-1','-1'))";

  $rules_query = "SELECT * 
                  FROM $access_table_name 
                  WHERE $where_conditional 
                  ORDER BY priority DESC";

  $rules = $wpdb->get_results($rules_query);

  return $rules;
}

function load_user_info() {
	global $wpdb;
	global $access_debug;
	global $reader;
	
	// grab user data
	$current_user = wp_get_current_user();
    if ( ($current_user instanceof WP_User) ) {
    	$user = array(
    		'login' => $current_user->user_login,
    		'email' => $current_user->user_email,
    		'display_name' => $current_user->display_name,
    		'id' => $current_user->ID
    	);
    } else {
    	$user = array(
    		'login' => 'none',
    		'email' => 'none',
    		'display_name' => 'none',
    		'id' => 'none'
    	);
    }

    if(is_user_logged_in()){
    	$logged_in = true;
    } else {
    	$logged_in = false;
    }
     
    // grab debug users if available
    // will also set logged in to true, so it won't trip the idiotproofing in the logic that follows
    if(!empty($_GET['testlogin'])) {
    	$user['login'] = $_GET['testlogin'];
    	$logged_in = true;
    }
    if(!empty($_GET['testemail'])) {
    	$user['email'] = $_GET['testemail'];
    	$logged_in = true;
    }
    if(!empty($_GET['testdisplayname'])) {
    	$user['display_name'] = $_GET['testdisplayname'];
    	$logged_in = true;
    }
    if(!empty($_GET['testuserid'])) {
    	$user['id'] = $_GET['testuserid'];
    	$logged_in = true;
    }


    /*****
	* INSTITUTION SNATCHING
    *****/

    // grab and filter user ip
	$ip = $_SERVER['REMOTE_ADDR'];
	$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	// use debug ips if available
	// TODO: this NEEDS to be more secure
	if(!empty(get_option('access_debug_ip'))) $ip = get_option('access_debug_ip');
	if(!empty($_GET['testip'])) $ip = $_GET['testip'];

	// turn ip into a computer-readable form
	$ip_long = sprintf("%u", ip2long($ip));
	if($access_debug) echo 'Current IP:' . $ip_long . "\n";

	//begin institution querying

	global $inst_ip_table_name;
	global $inst_location_table_name;
	global $inst_order_table_name;
	global $inst_table_name;

	// get matching ips
	$ip_query = "SELECT * FROM $inst_ip_table_name 
	WHERE $ip_long BETWEEN start AND end";
	$inst_ips = $wpdb->get_results($ip_query);
	// get first result
	$inst_ip = $inst_ips[0];

	if($access_debug) {
		echo "Institution IP data:\n";
		print_r($inst_ip);
	}

	// load up matching institutions
	$inst_locations = array();
	if(empty($inst_ips)) {
		//$is_subscribed = false;
	} else { 
		//$is_subscribed = true;
		//foreach($inst_ips as $inst_ip) {
			$location_id = $inst_ip->location_id;
			$location_query = 
			"SELECT * FROM $inst_location_table_name
			WHERE id=$location_id";
			$inst_locations = array_merge($inst_locations, $wpdb->get_results($location_query));
			$inst_location = $inst_locations[0];
		//}
		if($access_debug) {
			echo "Institution Location Data:\n";
			print_r($inst_location);
		}
	}

	// load up matching orders
	$inst_orders = array();
	if(empty($inst_locations)) {

	} else {
		//foreach($inst_locations as $inst_location) {
			$location_id = $inst_location->id;
			$order_query = 
			"SELECT * FROM $inst_order_table_name
			WHERE location_id=$location_id";
			$inst_orders = array_merge($inst_orders, $wpdb->get_results($order_query));
			$inst_order = $inst_orders[0];
		//}
		if($access_debug) {
			echo "Institution Order History:\n";
			print_r($inst_order);
		}
	}
	
	// use order to see whether institution is subscribed or not
	if(empty($inst_orders)) {

		$is_subscribed = false;
	} else {

		$cur_time = time();
		$is_subscribed = false;
		//foreach($inst_orders as $inst_order) {
			// check if order falls within today's date
			$fromtime = strtotime($inst_order->date_start);
			$endtime = strtotime($inst_order->date_end);
			if ($cur_time >= $fromtime && $cur_time <= $endtime) {

			    $is_subscribed = true;
			    //break;
			} 
		//}
	}

	// grab parent institution
	if(!empty($inst_ip) && !empty($inst_location) && !empty($inst_order)) {
		$inst_id = $inst_location->inst_id;
		$inst_query = "SELECT * FROM $inst_table_name WHERE id=$inst_id";
		$insts = $wpdb->get_results($inst_query);
		$inst = $insts[0];

		if($access_debug) {
			echo "Institution Name:\n";
			print_r($inst);
		}

		global $user_inst;
		$user_inst = array(
			'inst' => $inst,
			'ip' => $inst_ip,
			'location' => $inst_location,
			'order' => $inst_order,
			'is_subscribed' => $is_subscribed
		);
	}

	/****
	* USER ORDER SNATCHIN
	****/
	global $wpdb;
	global $inst_order_table_name;

	$user_id = $user['id'];
	$order_query = 
	"SELECT * FROM $inst_order_table_name
	WHERE user_id=$user_id";
	$user_orders = $wpdb->get_results($order_query);

	if($access_debug) {
		echo "User Order History:\n";
		print_r($user_orders);
	}

	$cur_time = time();
	foreach($user_orders as $user_order) {
		// check if order falls within today's date
		$fromtime = strtotime($user_order->date_start);
		$endtime = strtotime($user_order->date_end);
		if ($cur_time >= $fromtime && $cur_time <= $endtime) {

		    $is_subscribed = true;
		    break;
		} 
	}


	/****
	* GEOIP SNATCHIN
	****/
	try {
	    $record = $reader->city($ip);

	    $country = array (
	    	'iso' => $record->country->isoCode,
	    	'name' => $record->country->name
	    );

		$region = array (
			'iso' => $record->mostSpecificSubdivision->isoCode,
			'name' => $record->mostSpecificSubdivision->name
		);

		$continent = array (
			'iso' => $record->continent->code,
			'name' => $record->continent->name
		);	

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
	    //return new WP_Error( 'ip_not_found', "I've fallen and can't get up" );
	}

	// apply debug if present
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

	// load up user info
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

	if(empty($rules)) {
		return;
	}
	if(empty($user_info)) {
		return;
	}

	$blocks = array();

	foreach($rules as $rule) {
		// check for invalid/empty result first and return if so
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
			switch($check_type) {
				case 'is_ip':
					$ip_check = $user_info['ip'];
					$ips = explode(',', $check_values[$index]);

					foreach($ips as $ip) {
						if($ip_check == $ip) {
							if($access_debug) echo "ip matched\n";
							$check_count++;
						}
					}
					break;

				case 'is_institution':
					$institution_check = $user_info['institution'];
					$institutions = explode(',', $check_values[$index]);

					foreach($institutions as $institution) {
						// TODO institution check
					}
					break;

				case 'is_country':
					$country_check = $user_info['country'];
					$countries = explode(",", $check_values[$index]);

					foreach($countries as $country) {
						if($country_check['iso'] == $country or $country_check['name'] == $country) {
							if($access_debug) echo "country matched\n";
							$check_count++;
						}
					}
					break;
				case 'is_region':
					$region = $user_info['region'];
					$region_checks = explode(',', $check_values[$index]);

					foreach($region_checks as $region_check) {
						if($region['iso'] == $region_check || $region['name'] == $region_check) {
							if($access_debug) echo "region matched!\n";
							$check_count++;
						}
					}

					break;
				case 'is_continent':
					$continent = $user_info['continent'];
					$continent_checks = explode(',', $check_values[$index]);

					foreach($continent_checks as $continent_check) {
						if($continent['iso'] == $continent_check || $continent['name'] == $continent_check) {
							if($access_debug) echo "continent matched!\n";
							$check_count++;
						} 
					}

					break;
				case 'is_user':

					$user_check = $user_info['user'];

					$users = explode(",", $check_values[$index]);
					foreach($users as $user) {
						if(($user_check['login'] == $user or
						   $user_check['email'] == $user or
						   $user_check['display_name'] == $user or
						   $user_check['id'] == $user) && $user_info['logged_in']) {
							if($access_debug) echo "user matched\n";
							$check_count++;
						}
					}
					break;
				case 'is_logged_in':

					$logged_in_check = $user_info['logged_in'];
					$logged_ins = explode(',', $check_values[$index]);
					foreach($logged_ins as $logged_in) {
						if(($logged_in == 'T' && $logged_in_check) || ($logged_in == 'F' && !$logged_in_check)) {
							if($access_debug) echo "loggedin matched!\n";
							$check_count++;
						}
					}

					break;
				case 'is_subscribed':
					$user_subscribed = $user_info['subscribed'];
					$check_subscribed = $check_values[$index];
					if(($check_subscribed == 'T' && $user_subscribed) || ($check_subscribed == 'F' && !$user_subscribed)) {
						if($access_debug) echo "subscribed matched!\n";
						$check_count++;
					}

				break;
				default:
					echo "invalid check type";
					break;

			//END SWITCH
			}
		//END FOREACH
		}
		if($access_debug) echo 'checks passed: ' . $check_count . '/' . $checks . "\n";

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

/**
 * super function that calls everything
 * @return [type] [description]
 */
function check_access() {
  global $wpdb;
  global $access_table_name;
  global $access_blocks;

  global $access_debug;
  // if not admin, then don't display at all
  global $is_user_admin;
  if(!$is_user_admin) $access_debug = false;

  $selector_meta = extract_selector_meta(get_the_ID());
  if($access_debug) echo '<pre>';
  $institution_meta = extract_institution_meta();

  $all_rules_query = "SELECT * 
                      FROM $access_table_name";
  $all_rules = $wpdb->get_results($all_rules_query);

  $rules = collect_rules($selector_meta, $institution_meta);
  if($access_debug) {
  	echo "Rules that apply to this article:\n";
  	print_r($rules);
  }

  $user_info = load_user_info();
  if($access_debug) {
  	echo "User Meta:\n";
  	print_r($user_info);
  }

  $access_blocks = array();
  $access_blocks = get_blocks($rules, $user_info);
  if($access_debug) {
  	echo "Applied Blocks:\n";
  	print_r($access_blocks);
  }

  // FOR DEBUGGING ONLY. STOPS ALL BLOCKS FROM LOADING
  //$blocks = array();

  if($access_debug) echo '</pre>';
}

?>