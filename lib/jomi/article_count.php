<?php

// get the total number of articles available
// used so we can check when the user enters an invalid query when searching for an article
function count_articles(){
	global $num_articles;
	$num_articles = 0;

	//$article_count = wp_count_posts('article');
	//echo '<pre>';
	//print_r($article_count);
	//echo '</pre>';
	//echo '<pre>';
	$post_count = wp_count_posts('article');
	$num_articles = $post_count->publish + $post_count->in_production + $post_count->preprint + $post_count->coming_soon /* + $post_count->internal_review*/;
	//echo '</pre>';
}
add_action('init', 'count_articles', 10000);

// hide admin bar for now
add_filter('show_admin_bar', '__return_false');

?>