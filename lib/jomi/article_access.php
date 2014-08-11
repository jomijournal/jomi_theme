<?php

global $wpdb;

global $access_db_version;
$access_db_version = '1.01';

global $access_table_name;
$access_table_name = $wpdb->prefix . 'article_access';

function access_table_install() {
	global $wpdb;
	global $access_db_version;
	global $access_table_name;
	
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

	$sql = "CREATE TABLE $access_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		result_type VARCHAR(20) NOT NULL,
		result_time_start int(5) NOT NULL,
		result_time_end int(5) NOT NULL,
		result_time_elapsed int(5) NOT NULL,
		result_msg text NOT NULL,
		check_type VARCHAR(20) NOT NULL,
		check_value text NOT NULL,
		priority int(3) NOT NULL,
		selector_type VARCHAR(20) NOT NULL,
		selector_value text NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'access_db_version', $access_db_version );
}
function update_access_table() {
	global $access_db_version;
	if(get_option('access_db_version') != $access_db_version) {
		access_table_install();
	}
}
add_action('init', 'update_access_table');

/**
 * insert a rule
 * @param  array $args [description]
 * 'result_type' => [string] 
 * 		- ('DENY', 'NONE', etc...). 
 * 		- the type of the result. used to standardize msg outputs
 * 		- default: 'DEFAULT'
 * 'result_time_start' => [int] 
 * 		- the time, in seconds, of when to allow users to start watching
 * 		- default: -1. allow users to start anywhere
 * 'result_time_end' => [int] 
 * 		- the time, in seconds, of when stop users from watching
 * 		- default: -1. allow user to watch until end
 * 'result_time_elapsed' => [int]
 * 		- the time, in seconds, of how much of the video the user is allowed to watch
 * 		- default: -1. allow user to watch any amount of video
 * 'result_msg' => [string]
 * 		- the message, in HTML, to display when blocked.
 * 		- default: ''. use the msg determined by the result_type
 * 'check_type' => [string]
 * 		- the type of check to perform. ex, 'region', 'ip', 'user', 'country'
 * 		- default: ''. perform no check
 * 'check_value' => [string]
 * 		- the values to check against, as a CSV. ex, 'US,AS,AF' or '193.345.34.32,325.34.23.43,23.4.43.43'
 * 		- default: ''. perform no check
 * 'priority' => [int]
 * 		- the order of when this rule is enforced. lower number means it will be enforced earlier. ex, -10 will be enforced earlier than 5
 * 		- default: '0'. medium priority.
 * 'selector_type' => [string]
 * 		- the type of selector to apply. selectors determine what articles this rule will apply to.
 * 		- default: 'NONE'. apply to nothing
 * 'selector_value' => [string]
 * 		- 'the value to check against, as a CSV.'
 * 		- default: ''. apply to nothing
 * 
 * @return [type]       [description]
 */
function insert_rule() {

	global $wpdb;

	// default values.
	// edit this array to modify default behavior
	$default = array(
		'result_type' => 'DEFAULT',
		'result_time_start' => -1,
		'result_time_end' => -1,
		'result_time_elapsed' => -1,
		'result_msg' => '<p>DEFAULT</p>',
		'check_type' => '',
		'check_value' => '',
		'priority' => 0,
		'selector_type' => '',
		'selector_value' => ''
	);
	$result_type = (empty($_POST['result_type'])) ? $default['result_type'] : $_POST['result_type'];
	$result_time_start = (empty($_POST['result_time_start'])) ? $default['result_time_start'] : $_POST['result_time_start'];
	$result_time_end = (empty($_POST['result_time_end'])) ? $default['result_time_end'] : $_POST['result_time_end'];
	$result_time_elapsed = (empty($_POST['result_time_elapsed'])) ? $default['result_time_elapsed'] : $_POST['result_time_elapsed'];
	if(empty($_POST['result_msg'])) {
		switch ($result_type) {
			case $default['result_type']:
			default:
				$result_msg = $default['result_msg'];
		}
	} else {
		$result_msg = $_POST['result_msg'];
	}
	$check_type = (empty($_POST['check_type'])) ? $default['check_type'] : $_POST['check_type'];
	$check_value = (empty($_POST['check_value'])) ? $default['check_value'] : $_POST['check_value'];
	$priority = (empty($_POST['priority'])) ? $default['priority'] : $_POST['priority'];
	$selector_type = (empty($_POST['selector_type'])) ? $default['selector_type'] : $_POST['selector_type'];
	$selector_value = (empty($_POST['selector_value'])) ? $default['selector_value'] : $_POST['selector_value'];

	global $access_table_name;
	
	$wpdb->insert( 
		$access_table_name, 
		array( 
			'result_type' => $result_type,
			'result_time_start' => $result_time_start,
			'result_time_end' => $result_time_end,
			'result_time_elapsed' => $result_time_elapsed,
			'result_msg' => $result_msg,
			'check_type' => $check_type,
			'check_value' => $check_value,
			'priority' => $priority,
			'selector_type' => $selector_type,
			'selector_value' => $selector_value
		) 
	);

	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}
}

/*
 * delete an article access rule
 * returns true if successful
 * returns false if unsucessful
 */
function delete_rule() {
	global $wpdb;
	global $access_table_name;

	// no id passed in
	if(!isset($_POST['id']) || empty($_POST['id'])) {
		return false;
	}

	$id = $_POST['id'];
	$wpdb->delete(
		$access_table_name,
		array(
			'id' => $id
		)
	);

	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}

	return true;
}


// DEBUG ONLY: insert an empty rule
//insert_rule(array());

/**
 * ARTICLE ACCESS MANAGEMENT
*/

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

add_action( 'wp_ajax_nopriv_myajax-submit', 'myajax_submit' );
add_action( 'wp_ajax_myajax-submit', 'myajax_submit' );

add_action( 'wp_ajax_nopriv_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_insert-rule', 'insert_rule' );

add_action( 'wp_ajax_nopriv_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_delete-rule', 'delete_rule' );

function myajax_submit() {
	global $wpdb;
	global $access_table_name;

	$query = "SELECT * FROM $access_table_name";

	$rules = $wpdb->get_results($query);
  ?>
<table class="access_rules">
	<tr>
		<th>Priority</th>
		<th>Selector</th>
		<th>Check</th>
		<th>Result</th>
		<th>Actions</th>
	</tr>
	<?php
foreach($rules as $rule) {
	?>
	<tr>
		<td>
			<p><?php echo $rule->priority; ?></p>
		</td>
		<td>
			<p>Type: <?php echo $rule->selector_type; ?></p>
			<p>Value: <?php echo $rule->selector_value; ?></p>
		</td>
		<td>
			<p>Type: <?php echo $rule->check_type; ?></p>
			<p>Value: <?php echo $rule->check_value; ?></p>
		</td>
		<td>
			<p>Type: <?php echo $rule->result_type; ?></p>
			<p>Time Start: <?php echo $rule->result_time_start; ?></p>
			<p>Time End: <?php echo $rule->result_time_end; ?></p>
			<p>Time Elapsed: <?php echo $rule->result_time_elapsed ?></p>
		</td>
		<td class="hidden">
			<p rule-id="<?php echo $rule->id; ?>">ID: <?php echo $rule->id; ?></p>
		</td>
		<td>
			<a class="btn" id="access_delete_rule" rule-id="<?php echo $rule->id ?>">Delete Rule</a>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
  exit;
}

// custom settings page
// global rulebook
add_action('admin_menu', 'global_rulebook_menu');
function global_rulebook_menu(){
  add_options_page( "Global Access Rulebook", "Global Access Rulebook", "manage_options", "global_rulebook", "global_rulebook");
}
function global_rulebook(){
  ?>

  <h4>Category</h4>
  <div id="select_container">
	  <select id="category">
	    <option val="all">All</option>
	    <option val="adf">asdf</option>
	  </select>
  </div>

  <div id="results">
  </div>

  <table class="access_rules" id="new_rules">
  	<tr>
  		<td><input type="number" id="access_priority" placeholder="Priority"></td>
  		<td>
  			<select id="access_selector_type">
  				<option val="category">Category</option>
  				<option val="article_id">Article ID</option>
  				<option val="institution">Institution</option>
  				<option val="post_status">Post Status</option>
  				<option val="author">Author</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_selector_value" placeholder="Selector Value"></td>
  		<td>
  			<select id="access_check_type">
  				<option val="is_ip">Is Verified IP(s)</option>
  				<option val="is_institution">Is Verified Institution(s)</option>
  				<option val="is_region">Is Verified Region(s)</option>
  				<option val="is_user">Is Verified User(s)</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_check_value" placeholder="Check Value"></td>
  	</tr>
  	<tr>
  		<td>
  			<select id="access_result_type">
  				<option val="deny">DENY</option>
  				<option val="none">NONE</option>
  				<option val="default">DEFAULT</option>
  				<option val="sign_up">SIGN UP</option>
  				<option val="checkpoint">CHECKPOINT</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_result_time_start" placeholder="Result Time Start"></td>
  		<td><input type="text" id="access_result_time_end" placeholder="Result Time End"></td>
  		<td><input type="text" id="access_result_time_elapsed" placeholder="Result Time Elapsed"></td>
  	</tr>
  	<tr>
  		<td><a class="btn fat white" id="access_add_rule">Add Rule</a></td>
  	</tr>
  </table>

  <script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
	$(function(){
		refresh();
		$('#access_add_rule').on('click', function() {
			$.post(MyAjax.ajaxurl, {
				action: 'insert-rule',
				priority: $('#access_priority').val(),
				selector_type: $('#access_selector_type').val(),
				selector_value: $('#access_selector_value').val(),
				check_type: $('#access_check_type').val(),
				check_value: $('#access_check_value').val(),
				result_type: $('#access_result_type').val(),
				result_time_start: $('access_result_time_start').val(),
				result_time_end: $('access_result_time_end').val(),
				result_time_elapsed: $('access_result_time_elapsed').val()
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
		$('#results').on('click', 'a', function() {
			$.post(MyAjax.ajaxurl, {
				action: 'delete-rule',
				id: $(this).attr('rule-id')
			},
			function(response){
				console.log(response);
				refresh();
			});
		})
		$('#select_container select').change(refresh);
	});
	function refresh() {
		//$('#results')
		//	.empty()
		//	.html("<p>nope</p>");

		$.post( MyAjax.ajaxurl, {
		    action : 'myajax-submit',
		    //cat : $('#category').val()
			},
			function( response ) {
			  $('#results').html(response);
			}
		);
	}
  </script>
  <?php
}
?>