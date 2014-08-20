<?php

global $wpdb;
global $access_db_version;
global $access_table_name;

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
  
  //print_r($selector_meta);

  // init conditional
  $where_conditional = "(selector_type, selector_value) IN (";
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

  //echo $rules_query;
  $rules = $wpdb->get_results($rules_query);

  //check_db_errors();

  return $rules;
}

function load_user_info() {
	global $reader;
	
	$current_user = wp_get_current_user();
    
    if ( ($current_user instanceof WP_User) ) {
    	$logged_in = true;
    	$user = array(
    		'login' => $current_user->user_login,
    		'email' => $current_user->user_email,
    		'display_name' => $current_user->display_name,
    		'id' => $current_user->ID
    	);
    	//return;
    } else {
    	$logged_in = false;
    	$user = array(
    		'login' => 'none',
    		'email' => 'none',
    		'display_name' => 'none',
    		'id' => 'none'
    	);
    }
     
    // DEBUG
    /*echo 'Username: ' . $current_user->user_login . '<br />';
    echo 'User email: ' . $current_user->user_email . '<br />';
    echo 'User first name: ' . $current_user->user_firstname . '<br />';
    echo 'User last name: ' . $current_user->user_lastname . '<br />';
    echo 'User display name: ' . $current_user->display_name . '<br />';
    echo 'User ID: ' . $current_user->ID . '<br />'; */
    //print_r($user);
	 
	$ip = $_SERVER['REMOTE_ADDR'];
	$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	// TEST ONLY
	$ip = "173.13.115.174";

	//DEBUG
	//echo $ip;
	
	// check institutions here
	$institution = array(
	);

	try {
	    $record = $reader->city($ip);
	    $country = array (
	    	'iso' => $record->country->isoCode,
	    	'name' => $record->country->name
	    );
		$region = $record->mostSpecificSubdivision->isoCode;
		$city = $record->city->name;
	} catch (Exception $e) {
		// if can't find, default to Boston, MA, US
		$country = array(
			'iso' => 'US',
			'name' => 'United States'
		);
		$region = 'MA';
		$city = 'Boston';
	    //return new WP_Error( 'ip_not_found', "I've fallen and can't get up" );
	}

	// DEBUG
	/*print("\n" . $record->country->isoCode . "\n"); // 'US'
	print($record->country->name . "\n"); // 'United States'
	print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
	print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'
	print($record->city->name . "\n"); // 'Minneapolis'*/

	$user_info = array(
		'logged_in' => $logged_in,
		'user' => $user,
		'institution' => $institution,
		'ip' => $ip,
		'country' => $country,
		'region' => $region,
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

	if(empty($rules)) {
		//echo "empty rules";
		return;
	}
	if(empty($user_info)) {
		//echo "no check data";
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

		// TODO: check for invalid time results
		
		// SPLIT UP CHECK TYPES
		$check_types = explode(',', $rule->check_type);
		$check_values = explode(',', $rule->check_value);

		$checks = count($check_types);
		$check_count = 0;

		foreach($check_types as $index => $check_type) {
			switch($check_type) {
				case 'is_ip':
					$ip_check = $user_info['ip'];
					$ips = explode(',', $check_values[$index]);

					foreach($ips as $ip) {
						if($ip_check == $ip) {
							//echo "ip matched\n";
							$check_count++;
							//continue 2;
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
							//echo "country matched\n";
							$check_count++;
							//continue 2;
						}
					}
					break;

				case 'is_user':

					$user_check = $user_info['user'];

					$users = explode(",", $check_values[$index]);
					foreach($users as $user) {
						if($user_check['login'] == $user or
						   $user_check['email'] == $user or
						   $user_check['display_name'] == $user or
						   $user_check['id'] == $user) {
							//echo "user matched\n";
							$check_count++;
							//continue 2;
							//return;
						}
					}
					break;
				case 'is_logged_in':

					$logged_in_check = $user_info['logged_in'];
					$logged_ins = explode(',', $check_values[$index]);
					foreach($logged_ins as $logged_in) {
						if(($logged_in == 'T' && $logged_in_check) || ($logged_in == 'F' && !$logged_in_check)) {
							//echo "loggedin matched!\n";
							$check_count++;
						}
					}

					break;
				default:
					echo "invalid check type";
					break;

			//END SWITCH
			}
		//END FOREACH
		}
		//echo 'checks passed: ' . $check_count . '/' . $checks . "\n";
		if($check_count == $checks) {
			array_push($blocks, array(
				'msg' => $rule->result_msg,
				'time_start' => $rule->result_time_start,
				'time_end' => $rule->result_time_end,
				'time_elapsed' => $rule->result_time_elapsed
			));
		}
	}
	//remove dupes
	//$blocks = array_unique($blocks);
	$no_more_duplicates = false;
	while(!$no_more_duplicates) {
		foreach($blocks as $block) {
			foreach($blocks as $index_check => $block_check) {
				// dont check yerself
				if($block_check == $block) continue;
				if($block_check['time_start'] == $block['time_start'] &&
				   $block_check['time_end'] == $block['time_end'] &&
				   $block_check['time_elapsed'] == $block['time_elapsed']) {

					array_splice($blocks, $index_check, 1);
					break 2;
				}
			}
		}
		$no_more_duplicates = true;
	}

	//print_r($blocks);

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

  $debug = true;

  $selector_meta = extract_selector_meta(get_the_ID());
  if($debug) echo '<pre>';
  //print_r($selector_meta);
  //echo $selector_meta['status'];
  $institution_meta = extract_institution_meta();
  //print_r($institution_meta);
  //$institution_id = $institution_meta['id'];

  $all_rules_query = "SELECT * 
                      FROM $access_table_name";
  $all_rules = $wpdb->get_results($all_rules_query);
  //print_r($all_rules);

  $rules = collect_rules($selector_meta, $institution_meta);
  if($debug) print_r($rules);

  $user_info = load_user_info();

  $access_blocks = array();
  $access_blocks = get_blocks($rules, $user_info);
  if($debug) print_r($access_blocks);

  // FOR DEBUGGING ONLY. STOPS ALL BLOCKS FROM LOADING
  //$blocks = array();

  if($debug) echo '</pre>';

  //return $blocks;
}


?>