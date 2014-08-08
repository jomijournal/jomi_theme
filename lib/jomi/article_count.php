<?php

// get the total number of articles available
// used so we can check when the user enters an invalid query when searching for an article
function count_articles(){
  // only count articles that are published or in preprint
  $post_args = array(
    'posts_per_page' => -1,
    'post_type' => 'article',
    'post_status' => array('publish', 'preprint', 'coming_soon', 'in_production')
  );
  $posts = get_posts($post_args);

  global $article_count;
  $article_count = count($posts);
}
add_action('init', 'count_articles');

// hide admin bar for now
add_filter('show_admin_bar', '__return_false');

?>