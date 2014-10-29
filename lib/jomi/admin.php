<?php
/*
=================================
ADMIN DISPLAY
=================================
*/
// Add publication ID as a sortable column in the admin listing of articles
//Add custom column
add_filter('manage_edit-article_columns', 'my_columns_head');
function my_columns_head($defaults) {
  $defaults['publication_id'] = 'Pub ID';
  $defaults['production_id'] = 'Prod ID';
  return $defaults;
}
//Add rows data
add_action( 'manage_article_posts_custom_column' , 'my_custom_column', 10, 2 );
function my_custom_column($column, $post_id ){
  switch ( $column ) {
    case 'publication_id':
      echo get_field('publication_id');
      break;
    case 'production_id':
      echo get_field('production_id');
      break;
  }
}
// Make these columns sortable
function sortable_columns() {
  return array(
    'publication_id' => 'publication_id',
    'production_id' => 'production_id'
  );
}

add_filter( "manage_edit-article_sortable_columns", "sortable_columns" );

/*
 * ADMIN COLUMN - SORTING - ORDERBY
 * http://scribu.net/wordpress/custom-sortable-columns.html#comment-4732
 */
add_filter( 'request', 'pub_id_column_orderby' );
function pub_id_column_orderby( $vars ) {
  if ( isset( $vars['orderby'] ) && 'publication_id' == $vars['orderby'] ) {
    $vars = array_merge( $vars, array(
      'meta_key' => 'publication_id',
      'orderby' => 'meta_value_num'
      //'orderby' => 'meta_value'
      //'order' => 'asc' // don't use this; blocks toggle UI
    ) );
  }
  return $vars;
}

add_filter( 'request', 'prod_id_column_orderby' );
function prod_id_column_orderby( $vars ) {
  if ( isset( $vars['orderby'] ) && 'production_id' == $vars['orderby'] ) {
    $vars = array_merge( $vars, array(
      'meta_key' => 'production_id',
      'orderby' => 'meta_value_num'
      //'orderby' => 'meta_value'
      //'order' => 'asc' // don't use this; blocks toggle UI
    ) );
  }
  return $vars;
}


// Delete columns:
// tags (useless)
// all stuff added by yoast SEO
function my_columns_filter( $columns ) {
    unset($columns['tags']);
    unset($columns['wpseo-title']);
    unset($columns['wpseo-metadesc']);
    unset($columns['wpseo-focuskw']);
    return $columns;
}
add_filter( 'manage_edit-article_columns', 'my_columns_filter', 10, 1 );

?>
