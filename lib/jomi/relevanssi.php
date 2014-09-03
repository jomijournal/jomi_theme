<?php
/**
 * CUSTOM RELEVANSSI STUFF
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

function add_relevanssi_post_statuses($query) {
  //echo '<pre>';
  //print_r($query);
  //echo '</pre>';

  return $query;
}
add_filter('relevanssi_query_filter', 'add_relevanssi_post_statuses', 10, 1);

function debug_relevanssi_hits($in) {
  $hits = $in[0];
  $query = $in[1];

  //echo '<pre>';
  //print_r($hits);
  //print_r($query);
  //print_r($in);
  //echo '</pre>';

  return array($hits);
}
//relevanssi_hits_filter (array($hits, $query))
add_filter('relevanssi_hits_filter', 'debug_relevanssi_hits', 10, 1);

function debug_relevanssi_match($match, $idf) {
  //echo '<pre>';
  //print_r($match);
  //print_r($idf);
  //echo '</pre>';

  return array($match, $idf);
}
//add_filter('relevanssi_match', 'debug_relevanssi_match', 10, 2);
//relevanssi_match ($match, $idf)

function debug_relevanssi_post_ok($post_ok, $post_ID) {
  //echo '<pre>';
  //print_r($post_ok);
  //print_r($post_ID);
  //echo '</pre>';

  return true;
}
add_filter('relevanssi_post_ok', 'debug_relevanssi_post_ok', 10, 2);
//relevanssi_post_ok ($post_ok, $post_ID)

?>