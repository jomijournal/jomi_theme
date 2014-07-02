<?php
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