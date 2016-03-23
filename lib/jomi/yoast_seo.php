<?php
/**
 * yoast seo modification
 */

function wpseo_load_article_image($url) {

	global $wp_query;
	$query = $wp_query->query;
	$post_type = $query['post_type'];
	//var_dump($post_type);

	// if is article post type
	if(is_single() && $post_type == 'article') {
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()));
		//var_dump($thumb);
		return $thumb[0];
	} else {
		return $url;
	}
	
}
add_filter('wpseo_opengraph_image', 'wpseo_load_article_image', 100, 1);


?>