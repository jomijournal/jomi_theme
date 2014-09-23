<?php

/* =============================================
 * DATABASE MANAGEMENT
 * UNDER CONSTRUCTION
 * =============================================
 */

/**
 * Add a widget to the dashboard.
 *
 * This function is hooked into the 'wp_dashboard_setup' action below.
 */
function list_db_add_dashboard_widgets() {
  wp_add_dashboard_widget(
   'list_db_dashboard_widget',         // Widget slug.
   'List DB',         // Title.
   'list_db_dashboard_widget_function' // Display function.
  );  
}
add_action( 'wp_dashboard_setup', 'list_db_add_dashboard_widgets' );

function switch_db_add_dashboard_widgets() {
  wp_add_dashboard_widget(
   'switch_db_dashboard_widget',         // Widget slug.
   'Switch DB',         // Title.
   'switch_db_dashboard_widget_function' // Display function.
  );  
}
add_action( 'wp_dashboard_setup', 'switch_db_add_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function list_db_dashboard_widget_function() {
  global $wpdb;

  echo '<b>Databases at: ' . DB_HOST . '</b>';

  $results = $wpdb->get_results('SHOW DATABASES');
  echo '<ul>';
  foreach($results as $result) {
    echo '<li>';
    echo $result->Database;
    echo '</li>';
  }
  echo '</ul>';
}

/**
 * write new database info to wp-config.php
 * @return [type] [description]
 */
function switch_db_dashboard_widget_function() {

  global $wpdb;
  // display variables we got from post
  if(!empty($_POST['db_user'])) $db_user = $_POST['db_user']; else $db_user = DB_USER;
  if(!empty($_POST['db_pass'])) $db_pass = $_POST['db_pass']; else $db_pass = DB_PASSWORD;
  if(!empty($_POST['db_name'])) $db_name = $_POST['db_name']; else $db_name = DB_NAME;
  if(!empty($_POST['db_host'])) $db_host = $_POST['db_host']; else $db_host = DB_HOST;

  if(!empty($_POST['db_user']) and !empty($_POST['db_pass']) and !empty($_POST['db_name']) and !empty($_POST['db_host'])) {
    $path_to_file = ABSPATH . "/wp-config.php";
    $file_contents = file_get_contents($path_to_file, false, $ctx);
    if (empty($file_contents)) {
      echo "empty file";
    } else {
      $file_contents = str_replace("define('DB_USER', '". DB_USER ."');","define('DB_USER', '". $db_user ."');",$file_contents);
      $file_contents = str_replace("define('DB_PASSWORD', '". DB_PASSWORD ."');","define('DB_PASSWORD', '". $db_pass ."');",$file_contents);
      $file_contents = str_replace("define('DB_NAME', '". DB_NAME ."');","define('DB_NAME', '". $db_name ."');",$file_contents);
      $file_contents = str_replace("define('DB_HOST', '". DB_HOST ."');","define('DB_HOST', '". $db_host ."');",$file_contents);
      file_put_contents($path_to_file,$file_contents);
    }
  }
?>
<p>DB User: <?php echo $db_user; ?></p>
<p>DB Pass: <?php echo $db_pass; ?></p>
<p>DB Name: <?php echo $db_name; ?></p>
<p>DB Host: <?php echo $db_host; ?></p>
<form name="db_switch" action="/wp-admin/index.php" method="post" id="db_switch" class="">
  <div class="row">
    <label class="prompt" for="db_user" id="db_user_prompt_text">DB User</label>
    <input type="text" name="db_user" id="db_user" autocomplete="off">
  </div>
  <div class="row">
    <label class="prompt" for="db_pass" id="db_pass_prompt_text">DB Pass</label>
    <input type="text" name="db_pass" id="db_pass" autocomplete="off">
  </div>
  <div class="row">
    <label class="prompt" for="db_name" id="db_name_prompt_text">DB Name</label>
    <input type="text" name="db_name" id="db_name" autocomplete="off">
  </div>
  <div class="row">
    <label class="prompt" for="db_host" id="db_host_prompt_text">DB Host</label>
    <input type="text" name="db_host" id="db_host" autocomplete="off">
  </div>
  <p class="submit">
    <input type="submit" name="db_save" id="db_save" class="button button-primary" value="Save DB Options">
  </p>
</form>

<?php
}

?>