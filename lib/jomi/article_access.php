<?php

function access_table_install() {
	global $wpdb;
	global $access_db_version;

	$table_name = $wpdb->prefix . 'article_access';
	
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

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		result_time_start int(5) DEFAULT '-1' NOT NULL,
		result_time_end int(5) DEFAULT '-1' NOT NULL,
		result_time_elapsed int(5) DEFAULT '-1' NOT NULL,
		result_msg text NOT NULL,
		check_type VARCHAR(20) DEFAULT '' NOT NULL,
		check_value text NOT NULL,
		priority int(3) DEFAULT '0' NOT NULL,
		selector_type VARCHAR(20) DEFAULT '' NOT NULL,
		selector_value text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'access_db_version', $access_db_version );
}

?>