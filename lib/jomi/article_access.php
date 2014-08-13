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

/**
 * update an article access rule in the db
 * @return [type] [description]
 */
function update_rule() {
	global $wpdb;
	global $access_table_name;

	// no id passed in
	if(!isset($_POST['id']) || empty($_POST['id'])) {
		return false;
	}

	$id = $_POST['id'];

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
	
	$push_data = array(
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
	);
	echo '<pre>';
	print_r($push_data);
	echo '</pre>';

	$wpdb->update(
		$access_table_name,
		$push_data,
		array('ID' => $id),
		array(
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s'
		),
		array('%d')
	);

	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}

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

add_action( 'wp_ajax_nopriv_update-rule', 'update_rule' );
add_action( 'wp_ajax_update-rule', 'update_rule' );

function myajax_submit() {
	global $wpdb;
	global $access_table_name;

	$query = "SELECT * FROM $access_table_name";

	$rules = $wpdb->get_results($query);
  ?>
<table class="access_rules">
	<tr>
		<th>ID</th>
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
			<input id="id" placeholder="<?php echo $rule->id; ?>" data="<?php echo $rule->id; ?>">
		</td>
		<td>
			<input id="priority" placeholder="<?php echo $rule->priority; ?>" data="<?php echo $rule->priority; ?>">
		</td>
		<td>
		  	<select id="selector_type" data="<?php echo $rule->selector_type; ?>">
  				<option val=""           >None</option>
  				<option val="category"   >Category</option>
  				<option val="article_id" >Article ID</option>
  				<option val="institution">Institution</option>
  				<option val="post_status">Post Status</option>
  				<option val="author"     >Author</option>
  			</select>
			<input id="selector_value" placeholder="<?php echo $rule->selector_value; ?>" data="<?php echo $rule->selector_value; ?>">
		</td>
		<td>
		  	<select id="check_type" data="<?php echo $rule->check_type; ?>">
  				<option val=""              >None</option>
  				<option val="is_ip"         >Is Verified IP(s)</option>
  				<option val="is_institution">Is Verified Institution(s)</option>
  				<option val="is_region"     >Is Verified Region(s)</option>
  				<option val="is_user"       >Is Verified User(s)</option>
  			</select>
			<input id="check_value" placeholder="Value: <?php echo $rule->check_value; ?>" data="<?php echo $rule->check_value; ?>">
		</td>
		<td>
  			<select id="result_type" data="<?php echo $rule->result_type; ?>">
  			  	<option val=""          >None</option>
  				<option val="deny"      >DENY</option>
  				<option val="sign_up"   >SIGN UP</option>
  				<option val="checkpoint">CHECKPOINT</option>
  			</select>
			<input id="result_time_start" placeholder="Time Start: <?php echo $rule->result_time_start; ?>" data="<?php echo $rule->result_time_start; ?>">
			<input id="result_time_end" placeholder="Time End: <?php echo $rule->result_time_end; ?>" data="<?php echo $rule->result_time_end; ?>">
			<input id="result_time_elapsed" placeholder="Time Elapsed: <?php echo $rule->result_time_elapsed ?>" data="<?php echo $rule->result_time_elapsed ?>">
		</td>
		<td>
			<a class="btn" id="access_delete_rule" rule-id="<?php echo $rule->id ?>" style="display:block;">Delete Rule</a>
			<a class="btn" id="access_edit_rule" rule-id="<?php echo $rule->id ?>" style="display:block;">Edit Rule</a>
		</td>
	</tr>
<?php
}
?>
</table>
<?php
  exit;
}


/**
 * GLOBAL RULEBOOK SETTINGS PAGE
 * GUI FOR MANAGING RULES
 */
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

  <!-- ADD RULE UI -->
  <table class="access_rules" id="new_rules">
  	<tr>
  		<td><input type="number" id="access_priority" placeholder="Priority"></td>
  		<td>
  			<select id="access_selector_type">
  				<option val=""           >none</option>
  				<option val="category"   >category</option>
  				<option val="article_id" >article_id</option>
  				<option val="institution">institution</option>
  				<option val="post_status">post_status</option>
  				<option val="author"     >author</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_selector_value" placeholder="Selector Value"></td>
  		<td>
  			<select id="access_check_type">
  				<option val=""              >None</option>
  				<option val="is_ip"         >Is Verified IP(s)</option>
  				<option val="is_institution">Is Verified Institution(s)</option>
  				<option val="is_region"     >Is Verified Region(s)</option>
  				<option val="is_user"       >Is Verified User(s)</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_check_value" placeholder="Check Value"></td>
  	</tr>
  	<tr>
  		<td>
  			<select id="access_result_type">
  			  	<option val=""          >None</option>
  				<option val="deny"      >DENY</option>
  				<option val="sign_up"   >SIGN UP</option>
  				<option val="checkpoint">CHECKPOINT</option>
  			</select>
  		</td>
  		<td><input type="number" id="access_result_time_start" placeholder="Result Time Start"></td>
  		<td><input type="number" id="access_result_time_end" placeholder="Result Time End"></td>
  		<td><input type="number" id="access_result_time_elapsed" placeholder="Result Time Elapsed"></td>
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
				action:              'insert-rule',
				priority:            $('#access_priority').val(),
				selector_type:       $('#access_selector_type option:selected').attr('val'),
				selector_value:      $('#access_selector_value').val(),
				check_type:          $('#access_check_type option:selected').attr('val'),
				check_value:         $('#access_check_value').val(),
				result_type:         $('#access_result_type option:selected').attr('val'),
				result_time_start:   $('#access_result_time_start').val(),
				result_time_end:     $('#access_result_time_end').val(),
				result_time_elapsed: $('#access_result_time_elapsed').val()
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
		$('#results').on('click', 'a#access_delete_rule', function() {
			$.post(MyAjax.ajaxurl, {
				action: 'delete-rule',
				id: $(this).attr('rule-id')
			},
			function(response){
				console.log(response);
				refresh();
			});
		})
		$('#results').on('click', 'a#access_edit_rule', function() {
			$(this).parent().parent().find('input').removeAttr('readonly');
			$(this).parent().parent().find('select').removeAttr('disabled');
			$(this).parent().parent().find('input').each(function() {
				$(this).val($(this).attr('data'));
			});
			//$(this).parent().parent().find('select').each(function() {
			//	$(this).find('option[val='+ $(this).val() +']').attr('selected', '');
			//});
			$(this).text('Update Rule');
			$(this).attr('id', 'access_update_rule');
		});
		$('#results').on('click', 'a#access_update_rule', function() {
			$(this).parent().parent().find('input').attr('readonly', '');
			$(this).parent().parent().find('select').attr('disabled', '');
			$(this).text('Edit Rule');
			$(this).attr('id', 'access_edit_rule');

			$.post(MyAjax.ajaxurl, {
				action: 'update-rule',
				id: $(this).parent().parent().find('#id').val(),
				priority: $(this).parent().parent().find('#priority').val(),
				selector_type: $(this).parent().parent().find('#selector_type option:selected').attr('val'),
				selector_value: $(this).parent().parent().find('#selector_value').val(),
				check_type:  $(this).parent().parent().find('#check_type option:selected').attr('val'),
				check_value: $(this).parent().parent().find('#check_value').val(),
				result_type:  $(this).parent().parent().find('#result_type option:selected').attr('val'),
				result_time_start: $(this).parent().parent().find('#result_time_start').val(),
				result_time_end:  $(this).parent().parent().find('#result_time_end').val(),
				result_time_elapsed: $(this).parent().parent().find('#result_time_elapsed').val()
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
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
			  $('#results').find('input').attr('readonly', '');
			  $('#results').find('select').attr('disabled', '');

			  // visual assertion
			  $('#results').find('select').each(function() {
			  	var dat = $(this).attr('data');
				$(this).find('option[val="'+ dat +'"]').attr('selected', '');
			  });
			}
		);
	}
  </script>
  <?php
}

/**
 * get useful article meta to help comb through article access rules
 * @param  [int] $id article id
 * @return [array] (category, id, status, author)
 */
function extract_selector_meta($id) {

	// define your own defaults here if you so desire
	$categories = get_the_category($id);
	$cats_out = array();
	foreach($categories as $category) {
		//$category = ($category == '') ? '' : $category;
		array_push($cats_out, $category->cat_ID);
	}
	$status = (get_post_status($id) == false) ? '' : get_post_status($id);
	//$author = (get_the_author_meta('user_nicename', $id) == '') ? '' : get_the_author_meta('user_nicename', $id);
	$coauthors = get_coauthors($id);
	$coauth_out = array();
	foreach($coauthors as $coauthor) {
		array_push($coauth_out, $coauthor->ID);
	}

	$out = array(
		'id' => $id,
		'category' => $cats_out,
		'status' => $status,
		'author' => $coauth_out,
	);
	return $out;
}
/**
 * use the user IP to get institution meta
 * can probably cache the result of this in the future
 * @return [int] institution ID (corresponds with row ID in the DB)
 */
function extract_institution_meta() {
	$ip = $_SERVER['REMOTE_ADDR'];

	// query institution table and get the institution rules
	
	$out = array(
		'id' => 0
	);
	return $out;
}
/**
 * collect, sort, and concatenate the rules applying to this article
 * @param  [array] $selector_meta    selector meta object grabbed from extract_selector_meta
 * @param  [array] $institution_meta institution meta object grabbed from extract_institution_meta
 * @return [type]                   [description]
 */
function collect_rules($selector_meta, $institution_meta) {

  global $wpdb;
  global $access_table_name;
  
  //print_r($selector_meta);

  // init conditional
  $where_conditional = "(selector_type, selector_value) IN (";
  // categories
  $cats = $selector_meta['category'];
  foreach($cats as $cat) {
  	$where_conditional .= "('category', $cat),";
  }
  // article id
  $id = $selector_meta['id'];
  $where_conditional .= "('article_id', $id),";
  // status
  $status = $selector_meta['status'];
  $where_conditional .= "('post_status', '$status'),";
  // authors
  $authors = $selector_meta['author'];
  foreach($authors as $author) {
  	$where_conditional .= "('author', $author),";
  }
  // cap it off
  $where_conditional .= "('-1','-1'))";
  
  $rules_query = "SELECT * 
                  FROM $access_table_name 
                  WHERE $where_conditional 
                  GROUP BY selector_type
                  ORDER BY priority DESC";

  //echo $rules_query;
  $rules = $wpdb->get_results($rules_query);
  print_r($rules);
}
function check_access() {

}





?>