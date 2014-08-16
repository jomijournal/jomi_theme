<?php

global $wpdb;

// change this when installing a new table version
// otherwise, access_table_install will not run every time (this is a good thing)
global $access_db_version;
$access_db_version = '1.01';

global $access_table_name;
$access_table_name = $wpdb->prefix . 'article_access';

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

/**
 * create the table that houses access rules
 * @return [type] [description]
 */
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
/**
 * runs the table install if the version numbers dont match
 * @return [type] [description]
 */
function update_access_table() {
	global $access_db_version;
	if(get_option('access_db_version') != $access_db_version) {
		access_table_install();
	}
}
add_action('init', 'update_access_table');

/**
 * match $_POST inputs against defaults and returns
 * @return [array] processed post data
 */
function process_access_post_data() {

	$result_type = (empty($_POST['result_type'])) ? $default['result_type'] : $_POST['result_type'];
	$result_time_start = (empty($_POST['result_time_start'])) ? $default['result_time_start'] : $_POST['result_time_start'];
	$result_time_end = (empty($_POST['result_time_end'])) ? $default['result_time_end'] : $_POST['result_time_end'];
	$result_time_elapsed = (empty($_POST['result_time_elapsed'])) ? $default['result_time_elapsed'] : $_POST['result_time_elapsed'];
	if(empty($_POST['result_msg'])) {
		switch ($result_type) {
			case 'DENY':
			case 'deny':
				$result_msg = "<strong>DENIED</strong>";
				break;
			case 'sign_up':
				$result_msg = "<strong>SIGN UP</strong>";
				break;
			case 'none':
			case '':
			case $default['result_type']:
			default:
				$result_msg = $default['result_msg'];
				break;
		}
	} else {
		$result_msg = $_POST['result_msg'];
	}
	$check_type = (empty($_POST['check_type'])) ? $default['check_type'] : $_POST['check_type'];
	$check_value = (empty($_POST['check_value'])) ? $default['check_value'] : $_POST['check_value'];
	$priority = (empty($_POST['priority'])) ? $default['priority'] : $_POST['priority'];
	$selector_type = (empty($_POST['selector_type'])) ? $default['selector_type'] : $_POST['selector_type'];
	$selector_value = (empty($_POST['selector_value'])) ? $default['selector_value'] : $_POST['selector_value'];

	$out = array(
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

	return $out;
}

/**
 * list all rules
 * TODO: functionality to display only some rules
 * @return [type] [description]
 */
function list_rules() {
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
		<td id="selectors">
			<?php 
			$selector_types = explode(',', $rule->selector_type);
			$selector_vals = explode(',', $rule->selector_value); 
			$selectors = array();
			foreach($selector_types as $key=>$value) {
				array_push($selectors, array(
					'type' => $selector_types[$key],
					'value' => $selector_vals[$key]
				));
			}
			$index = 0;
			foreach($selectors as $selector) { $index++;?>
		  	<select id="selector_type" data="<?php echo $selector['type']; ?>">
  				<option val=""           >None</option>
  				<option val="category"   >Category</option>
  				<option val="article_id" >Article ID</option>
  				<option val="pub_id"     >Publication ID</option>
  				<option val="institution">Institution</option>
  				<option val="post_status">Post Status</option>
  				<option val="author"     >Author</option>
  			</select>
			<input id="selector_value" placeholder="<?php echo $selector['value']; ?>" data="<?php echo $selector['value']; ?>">
				<?php if($index > 1) { ?>
					<a id="delete_selector" href="#" style="background-color:#f00;color:#fff;width:10px;height:10px;padding:3px 5px;text-decoration:none;">--</a>
				<?php } ?>
			<?php } ?>
		</td>
		<td id="checks">
			<?php 
			$check_types = explode(',', $rule->check_type);
			$check_vals = explode(',', $rule->check_value); 
			$checks = array();
			foreach($check_types as $key=>$value) {
				array_push($checks, array(
					'type' => $check_types[$key],
					'value' => $check_vals[$key]
				));
			}
			$index = 0;
			foreach($checks as $check) { $index++;?>
		  	<select id="check_type" data="<?php echo $check['type']; ?>">
  				<option val=""              >None</option>
  				<option val="is_ip"         >Is IP(s)</option>
  				<option val="is_institution">Is Institution(s)</option>
  				<option val="is_country"     >Is Country(s)</option>
  				<option val="is_user"       >Is User(s)</option>
  			</select>
			<input id="check_value" placeholder="<?php echo $check['value']; ?>" data="<?php echo $check['value']; ?>">
				<?php if($index > 1) { ?>
					<a id="delete_check" href="#" style="background-color:#f00;color:#fff;width:10px;height:10px;padding:3px 5px;text-decoration:none;">--</a>
				<?php } ?>
			<?php } ?>
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
		<td class="row">
			<div class="col-xs-6">
				<a class="btn" id="access_delete_rule" rule-id="<?php echo $rule->id ?>">Delete Rule</a>
				<a class="btn" id="access_edit_rule" rule-id="<?php echo $rule->id ?>">Edit Rule</a>
			</div>
			<div class="col-xs-6">
				<a class="btn" id="access_add_selector" rule-id="<?php echo $rule->id ?>">Add Selector</a>
				<a class="btn" id="access_add_check" rule-id="<?php echo $rule->id ?>">Add Check</a>
			</div>
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
 * insert a rule
 * @param  array $args [description]
 * @return [type]       [description]
 */
function insert_rule() {

	global $wpdb;
	global $access_table_name;

	$push_data = process_access_post_data();
	
	$wpdb->insert( 
		$access_table_name, 
		$push_data
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
	
	$push_data = process_access_post_data();

	$wpdb->update(
		$access_table_name,
		$push_data,
		array('ID' => $id),
		array(
			'%s', '%d', '%d', '%d', '%s', '%s', '%s', '%d', '%s', '%s'
		),
		array('%d')
	);

	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}

}

function add_selector() {
	global $wpdb;
	global $access_table_name;

	// no id passed in
	if(!isset($_POST['id']) || empty($_POST['id'])) {
		return false;
	}

	$id = $_POST['id'];

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$selector_type = $rule->selector_type;
	$selector_value = $rule->selector_value;
	//append empty selector
	$selector_type .= ",none";
	$selector_value .= ",none";

	$wpdb->update(
		$access_table_name,
		array(
			'selector_type'=>$selector_type, 
			'selector_value'=>$selector_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}
}
function add_check() {
	global $wpdb;
	global $access_table_name;

	// no id passed in
	if(!isset($_POST['id']) || empty($_POST['id'])) {
		return false;
	}

	$id = $_POST['id'];

	$query = "SELECT * FROM $access_table_name WHERE id = $id";
	$rules = $wpdb->get_results($query);
	$rule = $rules[0];

	$check_type = $rule->check_type;
	$check_value = $rule->check_value;
	//append empty selector
	$check_type .= ",none";
	$check_value .= ",none";

	$wpdb->update(
		$access_table_name,
		array(
			'check_type'=>$check_type, 
			'check_value'=>$check_value
		),
		array('ID' => $id),
		array('%s', '%s'),
		array('%d')
	);
	// print errors if any show up
	if(!empty($wpdb->print_error())) {
		return $wpdb->print_error();
	}
}

// DEBUG ONLY: insert an empty rule
//insert_rule(array());

// embed the javascript file that makes the AJAX request
wp_enqueue_script( 'my-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ajax.js', array( 'jquery' ) );
// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

// register ajax stuff
add_action( 'wp_ajax_nopriv_list-rules', 'list_rules' );
add_action( 'wp_ajax_list-rules', 'list_rules' );
add_action( 'wp_ajax_nopriv_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_insert-rule', 'insert_rule' );
add_action( 'wp_ajax_nopriv_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_delete-rule', 'delete_rule' );
add_action( 'wp_ajax_nopriv_update-rule', 'update_rule' );
add_action( 'wp_ajax_update-rule', 'update_rule' );
add_action( 'wp_ajax_nopriv_add-selector', 'add_selector' );
add_action( 'wp_ajax_add-selector', 'add_selector' );
add_action( 'wp_ajax_nopriv_add-check', 'add_check' );
add_action( 'wp_ajax_add-check', 'add_check' );


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
  				<option val=""           >None</option>
  				<option val="category"   >Category</option>
  				<option val="article_id" >Article ID</option>
  				<option val="pub_id"     >Publication ID</option>
  				<option val="institution">Institution</option>
  				<option val="post_status">Post Status</option>
  				<option val="author"     >Author</option>
  			</select>
  		</td>
  		<td><input type="text" id="access_selector_value" placeholder="Selector Value"></td>
  		<td>
  			<select id="access_check_type">
  				<option val=""              >None</option>
  				<option val="is_ip"         >Is Verified IP(s)</option>
  				<option val="is_institution">Is Verified Institution(s)</option>
  				<option val="is_country"     >Is Verified country(s)</option>
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
				$('#access_rules input, #access_rules select').val('');
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

			var table = $(this).parent().parent().parent();

			// enable editing
			table.find('input').removeAttr('readonly');
			table.find('select').removeAttr('disabled');

			table.find('input').each(function() {
				$(this).val($(this).attr('data'));
			});
			$(this).text('Update Rule');
			$(this).attr('id', 'access_update_rule');
		});
		$('#results').on('click', 'a#access_update_rule', function() {

			var table = $(this).parent().parent().parent();

			// disable editing again
			table.find('input').attr('readonly', '');
			table.find('select').attr('disabled', '');
			// switch to 'edit' button
			$(this).text('Edit Rule');
			$(this).attr('id', 'access_edit_rule');

			// collect selector types and values
			var selector_types = "";
			table.find('#selector_type option:selected').each(function() {
				selector_types += ($(this).attr('val') + ',');
			});
			selector_types = selector_types.substring(0, selector_types.length - 1);

			var selector_vals = "";
			table.find('#selector_value').each(function() {
				selector_vals += ($(this).val() + ',');
			});
			selector_vals = selector_vals.substring(0, selector_vals.length - 1);

			// collect check types and values
			var check_types = "";
			table.find('#check_type option:selected').each(function() {
				check_types += ($(this).attr('val') + ',');
			});
			check_types = check_types.substring(0, check_types.length - 1);

			var check_vals = "";
			table.find('#check_value').each(function() {
				check_vals += ($(this).val() + ',');
			});
			check_vals = check_vals.substring(0, check_vals.length - 1);


			$.post(MyAjax.ajaxurl, {
				action:             'update-rule',
				id:                  $(this).attr('rule-id'),
				priority:            table.find('#priority').val(),
				selector_type:       selector_types,
				selector_value:      selector_vals,
				check_type:          check_types,
				check_value:         check_vals,
				result_type:         table.find('#result_type option:selected').attr('val'),
				result_time_start:   table.find('#result_time_start').val(),
				result_time_end:     table.find('#result_time_end').val(),
				result_time_elapsed: table.find('#result_time_elapsed').val()
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
		$('#results').on('click', 'a#access_add_selector', function() {

			var table = $(this).parent().parent().parent();

			$.post(MyAjax.ajaxurl, {
				action: 'add-selector',
				id: $(this).attr('rule-id')
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
		$('#results').on('click', 'a#access_add_check', function() {

			var table = $(this).parent().parent().parent();

			$.post(MyAjax.ajaxurl, {
				action: 'add-check',
				id: $(this).attr('rule-id')
			},
			function(response) {
				console.log(response);
				refresh();
			});
		});
		$('#select_container select').change(refresh);
	});
	function refresh() {

		$.post( MyAjax.ajaxurl, {
		    action : 'list-rules',
		    //cat : $('#category').val()
			},
			function( response ) {
			  $('#results').html(response);
			  // disable editing
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

	// TODO: match up against defaults?
	
	$publication_id = get_field('publication_id');

	$categories = get_the_category($id);
	$cats_out = array();
	foreach($categories as $category) {
		//$category = ($category == '') ? '' : $category;
		array_push($cats_out, $category->cat_ID);
	}
	$status = (get_post_status($id) == false) ? '' : get_post_status($id);
	$coauthors = get_coauthors($id);
	$coauth_out = array();
	foreach($coauthors as $coauthor) {
		array_push($coauth_out, $coauthor->ID);
	}

	$out = array(
		'id' => $id,
		'pub_id' => $publication_id,
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

	// TODO: query institution table and get the institution rules
	
	$out = array(
		// institution ID
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
  // publication id
  $pub_id = $selector_meta['pub_id'];
  $where_conditional .= "('pub_id', $pub_id),";
  // status
  $status = $selector_meta['status'];
  $where_conditional .= "('post_status', '$status'),";
  // authors
  $authors = $selector_meta['author'];
  foreach($authors as $author) {
  	$where_conditional .= "('author', $author),";
  }
  // TODO: add institution meta cond. here:

  // cap it off
  $where_conditional .= "('-1','-1'))";
  
  $rules_query = "SELECT * 
                  FROM $access_table_name 
                  WHERE $where_conditional 
                  GROUP BY selector_type
                  ORDER BY priority DESC";

  //echo $rules_query;
  $rules = $wpdb->get_results($rules_query);

  // print errors if any show up
  if(!empty($wpdb->print_error())) {
  	return $wpdb->print_error();
  }

  print_r($rules);
  return $rules;
}

function load_check_info() {
	global $reader;
	
	$current_user = wp_get_current_user();
    
    if ( ($current_user instanceof WP_User) ) {
    	$logged_in = true;
    	$user = array(
    		'login' => $current_user->user_login,
    		'email' => $current_user->user_email,
    		'display_name' => $current_user->display_name,
    		'id' => $current_user->ID
    	);
    	//return;
    } else {
    	$logged_in = false;
    	$user = array(
    		'login' => 'none',
    		'email' => 'none',
    		'display_name' => 'none',
    		'id' => 'none'
    	);
    }
     
    // DEBUG
    /*echo 'Username: ' . $current_user->user_login . '<br />';
    echo 'User email: ' . $current_user->user_email . '<br />';
    echo 'User first name: ' . $current_user->user_firstname . '<br />';
    echo 'User last name: ' . $current_user->user_lastname . '<br />';
    echo 'User display name: ' . $current_user->display_name . '<br />';
    echo 'User ID: ' . $current_user->ID . '<br />'; */
    //print_r($user);
	 
	$ip = $_SERVER['REMOTE_ADDR'];
	$ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

	// TEST ONLY
	$ip = "173.13.115.174";

	//DEBUG
	//echo $ip;
	
	// check institutions here
	$institution = array(
	);

	try {
	    $record = $reader->city($ip);
	    $country = array (
	    	'iso' => $record->country->isoCode,
	    	'name' => $record->country->name
	    );
		$region = $record->mostSpecificSubdivision->isoCode;
		$city = $record->city->name;
	} catch (Exception $e) {
		// if can't find, default to Boston, MA, US
		$country = array(
			'iso' => 'US',
			'name' => 'United States'
		);
		$region = 'MA';
		$city = 'Boston';
	    //return new WP_Error( 'ip_not_found', "I've fallen and can't get up" );
	}

	// DEBUG
	/*print("\n" . $record->country->isoCode . "\n"); // 'US'
	print($record->country->name . "\n"); // 'United States'
	print($record->mostSpecificSubdivision->name . "\n"); // 'Minnesota'
	print($record->mostSpecificSubdivision->isoCode . "\n"); // 'MN'
	print($record->city->name . "\n"); // 'Minneapolis'*/

	$out = array(
		'logged_in' => $logged_in,
		'user' => $user,
		'institution' => $institution,
		'ip' => $ip,
		'country' => $country,
		'region' => $region,
		'city' => $city
	);
	return $out;
}


/**
 * use rules to check access to article
 * @param  [type] $rules [description]
 * @param  [type] $check_data array of user/session data to check against
 * @return [array] $blocks a list of block objects to apply
 */
function check_access($rules, $check_data) {

	if(empty($rules)) {
		//echo "empty rules";
		return;
	}
	if(empty($check_data)) {
		//echo "no check data";
		return;
	}

	$blocks = array();

	foreach($rules as $rule) {
		// check for invalid/empty result first and return if so
		switch($rule->result_type) {
			case '':
			case 'None':
			case 'NONE':
			case 'Default':
			case 'DEFAULT':
				//return;
				continue;
				break;
		}

		// TODO: check for invalid time results
		
		switch($rule->check_type) {
			case 'is_ip':

				$ip_check = $check_data['ip'];

				$ips = explode(',', $rule->check_value);
				foreach($ips as $ip) {
					if($ip_check == $ip) {
						
						array_push($blocks, array(
							'msg' => $rule->result_msg,
							'time_start' => $rule->result_time_start,
							'time_end' => $rule->result_time_end,
							'time_elapsed' => $rule->result_time_elapsed
						));

						//echo "ip matched";
						//return;
					}
				}

				break;
			case 'is_institution':

				$institution_check = $check_data['institution'];

				$institutions = explode(',', $rule->check_value);
				foreach($institutions as $institution) {
					// TODO institution check
				}

				break;
			case 'is_country':

				$country_check = $check_data['country'];

				// split up the CSV
				$countries = explode(",", $rule->check_value);
				foreach($countries as $country) {
					if($country_check['iso'] == $country or $country_check['name'] == $country) {

						array_push($blocks, array(
							'msg' => $rule->result_msg,
							'time_start' => $rule->result_time_start,
							'time_end' => $rule->result_time_end,
							'time_elapsed' => $rule->result_time_elapsed
						));

						//echo "country matched";
						//return;
					}
				}
				break;
			case 'is_user':

				$user_check = $check_data['user'];

				$users = explode(",", $rule->check_value);
				foreach($users as $user) {
					if($user_check['login'] == $user or
					   $user_check['email'] == $user or
					   $user_check['display_name'] == $user or
					   $user_check['id'] == $user) {

						array_push($blocks, array(
							'msg' => $rule->result_msg,
							'time_start' => $rule->result_time_start,
							'time_end' => $rule->result_time_end,
							'time_elapsed' => $rule->result_time_elapsed
						));
						//TODO: place block
						//echo "user matched";
						//return;
					}
				}
				break;
			default:
				echo "invalid check type";
				break;
		}
	}
	//remove dupes
	$blocks = array_unique($blocks);

	print_r($blocks);

	return $blocks;
}

?>