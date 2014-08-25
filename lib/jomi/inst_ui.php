<?php
/**
 * INSTITUTION UI
 */

/**
 * INSTITUTION SETTINGS PAGE
 * GUI FOR MANAGING INSTITUTION
 */
add_action('admin_menu', 'inst_register_menu');
function inst_register_menu(){
  add_options_page( "Institution Management", "Institution Management", "manage_options", "inst_menu", "inst_menu");
}
function inst_menu(){
	echo 'hello world';
}
?>