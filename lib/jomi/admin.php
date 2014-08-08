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
  $defaults['publication_id'] = 'Publication ID/Article ID';
  return $defaults;
}
//Add rows data
add_action( 'manage_article_posts_custom_column' , 'my_custom_column', 10, 2 );
function my_custom_column($column, $post_id ){
  switch ( $column ) {
    case 'publication_id':
      echo get_field('publication_id');
      break;
  }
}
// Make these columns sortable
function sortable_columns() {
  return array(
    'publication_id' => 'publication_id'
  );
}

add_filter( "manage_edit-article_sortable_columns", "sortable_columns" );

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
