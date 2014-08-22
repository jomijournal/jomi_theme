<?php

// get the total number of articles available
// used so we can check when the user enters an invalid query when searching for an article
function count_articles(){
	global $article_count;
	$article_count = 0;

	/*$args=array(
	  'post_type' => 'article',
	  'post_status' => array('publish', 'preprint', 'coming_soon', 'in_production'),
	  'posts_per_page' => -1,
	  //'caller_get_posts'=> 1
	);
	$my_query = new WP_Query($args);*/

  //global $article_count;
  //$article_count = 0;
  //$article_count = count($posts);
  
  //while($my_query->have_posts()) {
  //	$my_query->the_post();
  	//$article_count++;
  //}
  //echo $article_count;
}
add_action('init', 'count_articles');

// hide admin bar for now
add_filter('show_admin_bar', '__return_false');

?>