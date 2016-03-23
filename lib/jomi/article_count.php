<?php

/**
 * count how many articles we have so we can sanitize stupid user input (done in rewrite.php)
 * @return [type] [description]
 */
function count_articles(){
	// reset article list
	global $article_list;
	$article_list = array();
	
	// what articles we count
	$args=array(
	  'post_type' => 'article',
	  'post_status' => array('publish', 'preprint', 'in_production', 'coming_soon'),
	  'posts_per_page' => -1,
	  'caller_get_posts'=> 1
	);
	$my_query = new WP_Query($args);

	// loop thru and count
	while ($my_query->have_posts()) : 
	  $my_query->the_post();
	  array_push($article_list, get_field('publication_id'));
	endwhile; 
	
	// update db value
	update_option('article_list', serialize($article_list));
}
add_action('save_post', 'count_articles');

/**
 * load db option into global
 * @return [type] [description]
 */
function init_article_list() {
	global $article_list;
	$article_list = unserialize(get_option('article_list'));
}
add_action('init', 'init_article_list');

// hide admin bar for now
add_filter('show_admin_bar', '__return_false');

?>