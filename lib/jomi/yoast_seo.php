<?php
/**
 * yoast seo modification
 */

function wpseo_load_article_image($url) {
	// if is article post type
	if(is_single()) {
		 $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()));
		 //var_dump($thumb);
	}
	return $thumb[0];
}
add_filter('wpseo_opengraph_image', 'wpseo_load_article_image', 100, 1);


?>