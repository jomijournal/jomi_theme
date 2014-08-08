<?php

/**
 * INSTITUTION IMPORT
 */

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