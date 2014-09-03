<?php

// get the total number of articles available
// used so we can check when the user enters an invalid query when searching for an article
function count_articles(){
	global $article_list;
	$article_list = array();
	//$num_articles = 0;

	//$article_count = wp_count_posts('article');
	//echo '<pre>';
	//print_r($article_count);
	//echo '</pre>';
	//echo '<pre>';
	//$post_count = wp_count_posts('article');
	//$num_articles = $post_count->publish + $post_count->in_production + $post_count->preprint + $post_count->coming_soon /* + $post_count->internal_review*/;
	
	$type = 'article';
	$args=array(
	  'post_type' => $type,
	  'post_status' => array('publish', 'preprint', 'in_production', 'coming_soon'),
	  'posts_per_page' => -1,
	  'caller_get_posts'=> 1
	);
	$my_query = new WP_Query($args);
	while ($my_query->have_posts()) : 
	  $my_query->the_post();
	  array_push($article_list, get_field('publication_id'));
	  //echo get_the_ID() . "\n";
	endwhile; 
	//print_r($article_list);
	
	update_option('article_list', serialize($article_list));

	//echo '</pre>';
	//wp_reset_postdata();
	//wp_reset_query();
}
add_action('save_post', 'count_articles');
function init_article_list() {
	global $article_list;
	$article_list = unserialize(get_option('article_list'));
}
add_action('init', 'init_article_list');

// hide admin bar for now
add_filter('show_admin_bar', '__return_false');

?>