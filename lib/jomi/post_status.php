<?php

/*
=================================
POST STATUSES
=================================
*/

function unread_post_status(){
  register_post_status( 'preprint', array(
    'label'                     => _x( 'Preprint', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Preprint <span class="count">(%s)</span>', 'Preprint <span class="count">(%s)</span>' ),
  ) );

  register_post_status( 'internal_review', array(
    'label'                     => _x( 'Internal Review', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => true,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Internal Review <span class="count">(%s)</span>', 'Internal Review <span class="count">(%s)</span>' ),
  ) );

  register_post_status( 'in_production', array(
    'label'                     => _x( 'In Production', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'In Production <span class="count">(%s)</span>', 'In Production <span class="count">(%s)</span>' ),
  ) );

  register_post_status( 'coming_soon', array(
    'label'                     => _x( 'Coming Soon', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Coming Soon <span class="count">(%s)</span>', 'Coming Soon <span class="count">(%s)</span>' ),
  ) );

}
add_action( 'init', 'unread_post_status' );


add_action('admin_footer-post.php', 'append_post_status_list');
function append_post_status_list(){
  global $post;
  $complete = array(
    'preprint' => '',
    'internal_review' => '',
    'coming_soon' => '',
    'in_production' => ''
  );
  $label = "";
  if($post->post_type == 'article')
  {
      if($post->post_status == 'preprint')
      {
           $complete['preprint'] = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> Preprint</span>';
      }
      if($post->post_status == 'internal_review')
      {
           $complete['internal_review'] = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> Internal Review</span>';
      }
      if($post->post_status == 'coming_soon')
      {
           $complete['coming_soon'] = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> Coming Soon</span>';
      }
      if($post->post_status == 'in_production')
      {
           $complete['in_production'] = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> In Production</span>';
      }

      echo '
      <script>
      jQuery(document).ready(function($){
           $("select#post_status").append("<option value=\"preprint\" ' . $complete['preprint'] . '>Preprint</option>");
           $("select#post_status").append("<option value=\"internal_review\" ' . $complete['internal_review'] . '>Internal Review</option>");
           $("select#post_status").append("<option value=\"coming_soon\" ' . $complete['coming_soon'] . '>Coming Soon</option>");
           $("select#post_status").append("<option value=\"in_production\" ' . $complete['in_production'] . '>In Production</option>");
           $(".misc-pub-section label").append("'.$label.'");
      });
      </script>
      ';
  }
}

function display_archive_state( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     //if($arg != 'preprint'){
          if($post->post_status == 'preprint'){
               return array('Preprint');
          }
          if($post->post_status == 'internal_review'){
               return array('Internal Review');
          }
          if($post->post_status == 'in_production'){
               return array('In Production');
          }
          if($post->post_status == 'coming_soon'){
               return array('Coming Soon');
          }
     //}
    return $states;
}
add_filter( 'display_post_states', 'display_archive_state' );

?>