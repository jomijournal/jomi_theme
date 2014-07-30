<?php

/* COMPOSER */
require_once('vendor/autoload.php');

/* USERAPP */

use \UserApp\Widget\User;
User::setAppId("53b5e44372154");

/**
 * Roots includes
 */
$roots_includes = array(
  '/lib/utils.php',           // Utility functions
  '/lib/init.php',            // Initial theme setup and constants
  '/lib/wrapper.php',         // Theme wrapper class
  '/lib/sidebar.php',         // Sidebar class
  '/lib/config.php',          // Configuration
  '/lib/activation.php',      // Theme activation
  '/lib/titles.php',          // Page titles
  '/lib/cleanup.php',         // Cleanup
  '/lib/nav.php',             // Custom nav modifications
  '/lib/gallery.php',         // Custom [gallery] modifications
  '/lib/comments.php',        // Custom comments modifications
  '/lib/relative-urls.php',   // Root relative URLs
  '/lib/widgets.php',         // Sidebars and widgets
  '/lib/scripts.php',         // Scripts and stylesheets
  '/lib/custom.php',          // Custom functions
);

foreach($roots_includes as $file){
  if(!$filepath = locate_template($file)) {
    trigger_error("Error locating `$file` for inclusion!", E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

// Bug testing only. Not to be used on a production site!!
/*add_action('wp_footer', 'roots_wrap_info');

function roots_wrap_info() {  
  $format = '<h6>The %s template being used is: %s</h6>';
  $main   = Roots_Wrapping::$main_template;
  global $template;

  printf($format, 'Main', $main);
  printf($format, 'Base', $template);
}*/

/*
=================================
POST TYPES
=================================
*/

add_filter('pre_get_posts', 'query_post_type');
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

add_action('init', 'cptui_register_my_cpt_article');
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
) ); }

// register post types with author archive
function custom_post_author_archive($query) {
    if ($query->is_author)
        $query->set( 'post_type', array('article', 'post') );
    remove_action( 'pre_get_posts', 'custom_post_author_archive' );
}
add_action('pre_get_posts', 'custom_post_author_archive');



/*
=================================
POST STATUSES
=================================
*/

function unread_post_status(){
  register_post_status( 'preprint', array(
    'label'                     => _x( 'Preprint', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => true,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Preprint <span class="count">(%s)</span>', 'Preprint <span class="count">(%s)</span>' ),
  ) );

  register_post_status( 'in_production', array(
    'label'                     => _x( 'In Production', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => true,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'In Production <span class="count">(%s)</span>', 'In Production <span class="count">(%s)</span>' ),
  ) );

  register_post_status( 'coming_soon', array(
    'label'                     => _x( 'Coming Soon', 'article' ),
    'public'                    => true,
    'exclude_from_search'       => true,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop( 'Coming Soon <span class="count">(%s)</span>', 'Coming Soon <span class="count">(%s)</span>' ),
  ) );

}
add_action( 'init', 'unread_post_status' );


add_action('admin_footer-post.php', 'append_post_status_list');
function append_post_status_list(){
  global $post;
  $complete = '';
  $label = '';
  if($post->post_type == 'article')
  {
      if($post->post_status == 'preprint')
      {
           $complete = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> Preprint</span>';
      }
      if($post->post_status == 'coming_soon')
      {
           $complete = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> Coming Soon</span>';
      }
      if($post->post_status == 'in_production')
      {
           $complete = ' selected=\"selected\"';
           $label = '<span id=\"post-status-display\"> In Production</span>';
      }
      echo '
      <script>
      jQuery(document).ready(function($){
           $("select#post_status").append("<option value=\"preprint\" '.$complete.'>Preprint</option>");
           $("select#post_status").append("<option value=\"coming_soon\" '.$complete.'>Coming Soon</option>");
           $("select#post_status").append("<option value=\"in_production\" '.$complete.'>In Production</option>");
           $(".misc-pub-section label").append("'.$label.'");
      });
      </script>
      ';
  }
}

function display_archive_state( $states ) {
     global $post;
     $arg = get_query_var( 'post_status' );
     if($arg != 'preprint'){
          if($post->post_status == 'preprint'){
               return array('Preprint');
          }
          if($post->post_status == 'in_production'){
               return array('In Production');
          }
          if($post->post_status == 'coming_soon'){
               return array('Coming Soon');
          }
     }
    return $states;
}
add_filter( 'display_post_states', 'display_archive_state' );

/*
=================================
REWRITE RULES 
BLACK MAGIC BEGINS HERE
=================================
*/

add_filter( 'template_include', 'wpse_97347_force_article', 100);
function wpse_97347_force_article( $template )
{
  global $wp_query;

  if ( $wp_query->get( 'publication_id' ) ) {
    Roots_Wrapping::wrap(@realpath(dirname(__FILE__)) . '/templates/content-single.php');
    return @realpath(dirname(__FILE__)) . "/base.php";

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

function login_rewrite($wp_rewrite) {
  //add_rewrite_rule('^login/','wp-login.php?action=login','top');
  //add_rewrite_rule('^register/','wp-login.php?action=register','top');
}
add_filter('init', 'login_rewrite');



/*
=================================
CUSTOM SIDEBARS
=================================
*/

register_sidebar(array(
	'name' => __('About Sidebar'),
	'id' => 'sidebar-about',
	'description' => __('Sidebar for the About Page'),
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '<h3>',
	'after_title' => '</h3>',
) );

register_sidebar(array(
  'name' => __('Article Sidebar'),
  'id' => 'sidebar-article',
  'description' => __('Sidebar for Article Pages'),
  'before_widget' => '',
  'after_widget' => '',
  'before_title' => '<h3>',
  'after_title' => '</h3>',
) );

/*
===============================
wp-login page style
===============================
 */
function jomi_login_head() {
  get_template_part('templates/head');
  do_action('get_header');
}
add_action('login_head', 'jomi_login_head');
function jomi_login_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '/assets/css/main.min.css' );
    wp_enqueue_script( 'custom-login', get_template_directory_uri() . '/assets/js/scripts.min.js' );
}
//add_action( 'login_enqueue_scripts', 'jomi_login_stylesheet' );
function jomi_login_header_url($url) {
  return site_url();
}
add_filter('login_headerurl', 'jomi_login_header_url');
function jomi_login_footer(){
  echo site_url('','relative');
  echo '
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
  $(function(){
    $("#loginform").attr("action", "' . site_url() . '/login/");
    $("#registerform").attr("action", "' . site_url() . '/register/");
    $("#login a").first().attr("title","Journal of Medical Insight");
    $("input[name=' . "'redirect_to'" . ']").attr("value","'.site_url().'");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=register'" . ']").attr("href", "'.site_url().'/register");
    $("a[href=' . "'" . site_url() . "/wp-login.php?action=lostpassword'" . ']").attr("href", "'.site_url().'/forgot");
    $("a[href=' . "'" . site_url() . "/wp-login.php'" . ']").attr("href", "'.site_url().'/login");

    $("#registerform input[name=' . "'redirect_to'" . ']").attr("value","'.site_url('/login?checkemail=registered').'")
  });
  </script>
  ';
}
add_action('login_footer', 'jomi_login_footer');
add_action('register_footer', 'jomi_login_footer');
add_action('lostpassword_footer', 'jomi_login_footer');


if (!function_exists('possibly_redirect'))
{
  function possibly_redirect()
  {
    global $pagenow;
    if( 'wp-login.php' == $pagenow )
    {
      $action = $_GET["action"];
      if($action=="logout"){
         wp_logout();
         header("Location: /login?loggedout=true");
      }
      # hide wp-login
      if($_SERVER['REQUEST_URI'] == "/wp-login.php?action=".$action)
      {
        wp_redirect('/login?action='.$action);
      }
      if($_SERVER['REQUEST_URI'] == "/wp-login.php?checkemail=registered")
      {
        wp_redirect('/login?checkemail=registered');
      }
      /*else
      {
        wp_redirect('/');       
      }*/
      //exit();
    }
  }
  add_action('init','possibly_redirect');
}

add_filter( 'registration_redirect', 'ckc_registration_redirect', 10000);
function ckc_registration_redirect() {
    return site_url('/login?checkemail=registered');
}

?>