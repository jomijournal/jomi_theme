<?php 
/*
=================================
POST TYPES
=================================
*/

/**
 * ???
 * @param  [type] $query [description]
 * @return [type]        [description]
 */
function query_post_type($query) {
  if(is_category() || is_tag()) {
    $post_type = get_query_var('post_type');
    if($post_type)
        $post_type = $post_type;
    else
        $post_type = array('post','article',);
    $query->set('post_type',$post_type);
    return $query;
    }
}
add_filter('pre_get_posts', 'query_post_type');

/**
 * register our custom "article" post type
 * @return [type] [description]
 */
function cptui_register_my_cpt_article() {
register_post_type('article', array(
  'label' => 'Journal',
  'description' => '',
  'public' => true,
  'show_ui' => true,
  'show_in_menu' => true,
  'capability_type' => 'post',
  'map_meta_cap' => true,
  'hierarchical' => false,
  #'rewrite' => array('slug' => 'article', 'with_front' => true),
  'rewrite' => false,
  'query_var' => true,
  'menu_icon' => '/wp-content/themes/jomi/assets/img/logo-notext-s.png',
  'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
  'taxonomies' => array('category','post_tag'),
  'labels' => array (
    'name' => 'Journal',
    'singular_name' => 'Article',
    'menu_name' => 'Journal',
    'add_new' => 'Add Article',
    'add_new_item' => 'Add New Article',
    'edit' => 'Edit',
    'edit_item' => 'Edit Article',
    'new_item' => 'New Article',
    'view' => 'View Article',
    'view_item' => 'View Article',
    'search_items' => 'Search Journal',
    'not_found' => 'No Journal Found',
    'not_found_in_trash' => 'No Journal Found in Trash',
    'parent' => 'Parent Article',
  )
) ); 
}
add_action('init', 'cptui_register_my_cpt_article');

/**
 * register post types with author archive
 * @param  [type] $query [description]
 * @return [type]        [description]
 */
function custom_post_author_archive($query) {
    if ($query->is_author)
        $query->set( 'post_type', array('article', 'post') );
    remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}
add_action('pre_get_posts', 'custom_post_author_archive');

?>
