<?php

/*
=================================
REWRITE RULES 
BLACK MAGIC BEGINS HERE
SERIOUSLY DONT TOUCH OR ELSE ARTICLE PERMALINKS BREAK
=================================
*/

add_filter( 'template_include', 'wpse_97347_force_article', 100);
function wpse_97347_force_article( $template )
{
  global $wp_query;

  if ( $wp_query->get( 'publication_id' ) ) {
    Roots_Wrapping::wrap(ABSPATH . 'wp-content/themes/jomi/templates/content-single.php');
    return ABSPATH . "wp-content/themes/jomi/base.php";

  }
  return $template;
}
function set_single()
{
  global $wp_query;
  if($wp_query->get('publication_id')) {

    $wp_query->is_single = true;
  }
}
add_action('wp', 'set_single', 0, 0);

function register_publication_id() {
  global $wp;
  $wp->add_query_var( 'publication_id' );
}
add_action( 'init', 'register_publication_id' );

function map_publication_id( $wp_query ) {
  if ( $meta_value = $wp_query->get( 'publication_id' ) ) {

    global $article_list;

    if(!in_array((int)$meta_value, $article_list)) {
      wp_redirect('/404');
      exit();
    }

    $rd_args = array(
      'post_type' => 'article',
      'post_count' => 1,
      'meta_key' => 'publication_id',
      'meta_value' => $meta_value
    );
     
    $rd_query = new WP_Query( $rd_args );

    if( $rd_query->have_posts() ) {
      while( $rd_query->have_posts() ) {
        $rd_query->the_post();
        $postID = get_the_ID();
        if(in_array(get_post_status($postID), array('coming_soon', 'in_production'))) {
          //wp_redirect('/404.php');
          //exit();
        }
      }
    }
    wp_reset_postdata();
    
    $wp_query->set( 'p', $postID );
  }
}
add_action( 'parse_query', 'map_publication_id' );

function add_article_rewrite_rules() {
  add_rewrite_rule('^article/([^/]*)','index.php?post_type=article&publication_id=$matches[1]','top');
  add_rewrite_rule('^article/([^/]*)/([^/]*)','index.php?post_type=article&publication_id=$matches[1]','top');

  flush_rewrite_rules();
}
add_action( 'init', 'add_article_rewrite_rules' );

function article_rewrite() {
  global $wp_rewrite;
  $wp_rewrite->add_rewrite_tag('%publication_id%', '([^/]+)', 'publication_id=');
  $wp_rewrite->add_rewrite_tag('%article_name%', '([^/]+)', 'article_name=');
  $wp_rewrite->add_permastruct('article', '/article/%publication_id%/%article_name%/', false);
  flush_rewrite_rules();
}
add_action('init', 'article_rewrite');

function article_permalink($permalink, $post, $leavename) {
  $no_data = 'no-article';
  $post_id = $post->ID;

  if($post->post_type != 'article' || empty($permalink) || in_array($post->post_status, array('draft', 'pending', 'auto-draft')))
    return $permalink;

  $pubID = get_field( "publication_id", $post->ID );
  if(!$pubID) { $pubID = $no_data; }

  $permalink = str_replace("%publication_id%", $pubID, $permalink);
  $permalink = str_replace("%article_name%", $post->post_name, $permalink);

  return $permalink;
}
add_filter('post_type_link', 'article_permalink', 10, 3);
/*
=================================
BLACK MAGIC ENDS HERE
=================================
*/

?>