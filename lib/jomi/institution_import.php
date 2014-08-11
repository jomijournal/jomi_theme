<?php

/**
 * INSTITUTION IMPORT
 */

global $wpdb;

global $institution_db_version;
$institution_db_version = '1.0';

global $institution_table_name;
$institution_table_name = $wpdb->prefix . 'institutions';

function institution_table_install() {
  global $wpdb;
  global $institution_db_version;
  global $institution_table_name;
  
  /*
   * We'll set the default character set and collation for this table.
   * If we don't do this, some characters could end up being converted 
   * to just ?'s when saved in our table.
   */
  $charset_collate = '';

  if ( ! empty( $wpdb->charset ) ) {
    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
  }

  if ( ! empty( $wpdb->collate ) ) {
    $charset_collate .= " COLLATE {$wpdb->collate}";
  }

  $sql = "CREATE TABLE $institution_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
 /*
 PUT CORRESPONDING MSSQL COLUMNS HERE
  */
    UNIQUE KEY id (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );

  add_option( 'institution_db_version', $institution_db_version );
}
function update_institution_table() {
  global $institution_db_version;
  if(get_option('institution_db_version') != $institution_db_version) {
    institution_table_install();
  }
}
// lets not do this yet
//add_action('init', 'update_institution_table');


// custom settings page
add_action('admin_menu', 'my_plugin_menu');
function my_plugin_menu(){
  add_options_page( "Import Institutions", "Import Institutions", "manage_options", "import_institutions", "import_institutions");
}
function import_institutions(){
  echo "hello";

    // Connect to MSSQL
  #$link = mssql_connect('KALLESPC\SQLEXPRESS', 'sa', 'phpfi');

  #if (!$link || !mssql_select_db('php', $link)) {
  #    die('Unable to connect or select database!');
  #}
  
  // Do a simple query, select the version of 
  // MSSQL and print it.
  #$version = mssql_query('SELECT @@VERSION');
  #$row = mssql_fetch_array($version);

  #echo $row[0];

  // Clean up
  #mssql_free_result($version);
  

}


?>