<?php
/**
 * CUSTOM RELEVANSSI STUFF
 */

/**
 * adds the statuses that relevanssi is allowed to index
 * @param [type] $statuses [description]
 */
function add_relevanssi_statuses($statuses) {
  return array(
    'publish',
    'preprint',
    'in_production',
    'coming_soon'
  );
}
add_filter('relevanssi_valid_status', 'add_relevanssi_statuses', 10, 1);

/**
 * pass all valid post statuses through as valid
 * this is a ugly workaround for relevanssi being dumb
 * ignore the debug function name. this is functional
 * @param  [type] $in [description]
 * @return [type]     [description]
 */
function debug_relevanssi_hits($in) {
  $hits = $in[0];
  $query = $in[1];

  if(empty($query)) {
  	$args=array(
	  'post_type' => 'article',
	  'post_status' => array('publish', 'preprint'),
	  'posts_per_page' => -1,
	  'caller_get_posts'=> 1
	);
	//$hits = array();
	//array_merge($hits, get_posts($args));
	$hits = get_posts($args);
	$args=array(
	  'post_type' => 'article',
	  'post_status' => array('coming_soon', 'in_production'),
	  'posts_per_page' => -1,
	  'caller_get_posts'=> 1
	);
	$hits = array_merge($hits, get_posts($args));
	//print_r($hits);
  }

  return array($hits);
}
add_filter('relevanssi_hits_filter', 'debug_relevanssi_hits', 10, 1);

/**
 * fix for stupid relevanssi
 * this is not a debug function. it is functional, dont delete
 * @param  [type] $post_ok [description]
 * @param  [type] $post_ID [description]
 * @return [type]          [description]
 */
function debug_relevanssi_post_ok($post_ok, $post_ID) {
  return true;
}
add_filter('relevanssi_post_ok', 'debug_relevanssi_post_ok', 10, 2);


function search_trigger($search_ok) {
	global $wp_query;

	return $search_ok;
	//return true;
}
add_filter('relevanssi_search_ok', 'search_trigger');

?>