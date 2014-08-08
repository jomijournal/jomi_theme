<?php

/* COMPOSER */
require_once('vendor/autoload.php');

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
$jomi_includes = array(
  '/lib/jomi/admin.php', // admin usability and visual improvements
  '/lib/jomi/article_access.php',
  '/lib/jomi/article_count.php',  // count # of articles published/preprinted
  '/lib/jomi/db_switch.php', // db switch utility on dashboard
  '/lib/jomi/institution_import.php', // import institutions from CRM (WIP)
  '/lib/jomi/login.php', // login page utility and restyling
  '/lib/jomi/post_status.php', // register post statuses
  '/lib/jomi/post_types.php', // register post types (article)
  '/lib/jomi/rewrite.php', // rewrite rules for article
  '/lib/jomi/sidebars.php' // custom sidebars
);

$includes = array_merge($roots_includes, $jomi_includes);

foreach($includes as $file){
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



/**
 * ARTICLE ACCESS MANAGEMENT
*/

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
 
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

add_action( 'wp_ajax_nopriv_myajax-submit', 'myajax_submit' );
add_action( 'wp_ajax_myajax-submit', 'myajax_submit' );

function myajax_submit() {
  // get the submitted parameters
  $cat_id = (isset($_POST['cat'])) ? $_POST['cat'] : '';

  $args = array(
    'post_type' => 'article',
    'cat' => $cat_id
  );

  $query = new WP_Query($args);

  if(!$query->have_posts()) {
   // return nothing
  }
  while($query->have_posts()) {
    $query->the_post();
    echo the_title() . '<br>';
  }
  wp_reset_query();

  // IMPORTANT: don't forget to "exit"
  exit;
}



// custom settings page
// global rulebook
add_action('admin_menu', 'global_rulebook_menu');
function global_rulebook_menu(){
  add_options_page( "Global Access Rulebook", "Global Access Rulebook", "manage_options", "global_rulebook", "global_rulebook");
}
function global_rulebook(){
  echo "hello";
  ?>

  <h4>Category</h4>
  <select id="category">
    <option val="all">All</option>
  <?php
  $args = array(
    'type' => 'article',
    'hide_empty' => 1
  );
  $categories = get_categories($args);
  foreach($categories as $category) { ?>
    <option value="<?php echo $category->cat_ID; ?>"><?php echo $category->name; ?></option>
  <?php } ?>
  </select>

  <div id="results">
  </div>

  <script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
  $(function(){
    $('#category').change(function() {

      $('#results')
        .empty()
        .html("<p>nope</p>");

      $.post(
        // see tip #1 for how we declare global javascript variables
        MyAjax.ajaxurl,
        {
            // here we declare the parameters to send along with the request
            // this means the following action hooks will be fired:
            // wp_ajax_nopriv_myajax-submit and wp_ajax_myajax-submit
            action : 'myajax-submit',

            // other parameters can be added along with "action"
            //postID : MyAjax.postID,

            cat : $('#category').val()
        },
        function( response ) {
          $('#results').html(response);
        }
    );

    });
  });
  </script>
  <?php
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */

function block_filter_add_meta_box() {

  $screens = array( 'article' );

  foreach ( $screens as $screen ) {

    add_meta_box(
      'block_filter_sectionid',
      'Block Filtering',
      'block_filter_meta_box_callback',
      $screen
    );
  }
}
add_action( 'add_meta_boxes', 'block_filter_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function block_filter_meta_box_callback( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'block_filter_meta_box', 'block_filter_meta_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $value = get_post_meta( $post->ID, '_my_meta_value_key', true );

  /*echo '<label for="myplugin_new_field">';
  _e( 'Description for this field', 'myplugin_textdomain' );
  echo '</label> ';
  echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="' . esc_attr( $value ) . '" size="25" />';*/

?>

<h3>

<h4>Filter by Country. Uses <a href="http://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">ISO alpha-2 country codes.</a> Separate by commas.</h4>
<input type="radio" name="filter_country_list_type" value="whitelist"> Whitelist<br>
<input type="radio" name="filter_country_list_type" value="blacklist"> Blacklist
<br>
<input type="text" id="filter_country" name="filter_country" placeholder="US,UK,FR,SE" size="25" />

<h4>Filter by Continent</h4>
<input type="checkbox" name="filter_continent_All" value="All" checked="true">All<br>
<input type="checkbox" name="filter_continent_AF" value="AF">Africa<br>
<input type="checkbox" name="filter_continent_AN" value="AN">Antarctica<br>
<input type="checkbox" name="filter_continent_AS" value="AS">Asia<br>
<input type="checkbox" name="filter_continent_EU" value="EU">Europe<br>
<input type="checkbox" name="filter_continent_NA" value="NA">North America<br>
<input type="checkbox" name="filter_continent_OC" value="OC">Oceania<br>
<input type="checkbox" name="filter_continent_SA" value="SA">South America<br>

<h4>Filter by IP</h4>
<input type="radio" name="filter_ip" value="verify">Only Verified IPs<br>
<input type="radio" name="filter_ip" value="no_verify" checked="true">Any IP<br>

<h4>Filter by User</h4>
<input type="radio" name="filter_user" value="verify">Only Logged-In Users<br>
<input type="radio" name="filter_user" value="no_verify" checked="true">Anyone<br>


<?php 
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function block_filter_save_meta_box_data( $post_id ) {

  /*
   * We need to verify this came from our screen and with proper authorization,
   * because the save_post action can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['block_filter_meta_box_nonce'] ) ) {
    return;
  }

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $_POST['block_filter_meta_box_nonce'], 'block_filter_meta_box' ) ) {
    return;
  }

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  // Check the user's permissions.
  if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) ) {
      return;
    }

  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
      return;
    }
  }

  /* OK, it's safe for us to save the data now. */
  
  // Make sure that it is set.
  //if ( ! isset( $_POST['myplugin_new_field'] ) ) {
  //  return;
  //}

  // Sanitize user input.
  //$my_data = sanitize_text_field( $_POST['myplugin_new_field'] );

  // Update the meta field in the database.
  //update_post_meta( $post_id, '_my_meta_value_key', $my_data );
}
add_action( 'save_post', 'block_filter_save_meta_box_data' );

?>