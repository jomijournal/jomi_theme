<?php 

/**
 * ACCESS UI
 */

global $wpdb;
global $access_db_version;
global $access_table_name;

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
	$rules = array_reverse($rules);

	// add an extra rule that will represent the 'add rule' row
	$add_rule = (object)array(
		'id'=>-1,
		'result_type' => 'DEFAULT',
		'result_time_start' => -1,
		'result_time_end' => -1,
		'result_time_elapsed' => -1,
		'result_msg' => '<p>DEFAULT</p>',
		'check_type' => 'none',
		'check_value' => 'none',
		'priority' => 0,
		'selector_type' => 'none',
		'selector_value' => 'none'
	);
	array_unshift($rules, $add_rule);

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
foreach($rules as $rule_index=>$rule) {
	?>
	<tr>
		<td>
			<input id="id" placeholder="<?php echo $rule->id; ?>" data="<?php echo $rule->id; ?>">
		</td>
		<td>
			<input type="number" id="priority" placeholder="<?php echo $rule->priority; ?>" data="<?php echo $rule->priority; ?>">
		</td>
		<td>
			<?php 
			#$selector_types = explode(',', $rule->selector_type);
			#$selector_vals = explode(',', $rule->selector_value); 
			#$selectors = array();
			#foreach($selector_types as $key=>$value) {
			#	array_push($selectors, array(
			#		'type' => $selector_types[$key],
			#		'value' => $selector_vals[$key]
			#	));
			#}
			#$index = 0;
			#foreach($selectors as $selector) { $index++;?>
		  	<select id="selector_type" data="<?php echo $rule->selector_type; ?>">
  				<option val=""           >None</option>
  				<option val="all"        >All</option>
  				<option val="category"   >Category</option>
  				<option val="article_id" >Article ID</option>
  				<option val="pub_id"     >Publication ID</option>
  				<option val="institution">Institution</option>
  				<option val="post_status">Post Status</option>
  				<option val="author"     >Author</option>
  			</select>
			<input id="selector_value" placeholder="<?php echo $rule->selector_value; ?>" data="<?php echo $rule->selector_value ?>">
				<?php #if($index > 1) { ?>
					<!--a id="delete_selector" href="#" style="background-color:#f00;color:#fff;width:10px;height:10px;padding:3px 5px;text-decoration:none;">-</a-->
				<?php #} ?>
			<?php #} ?>
		</td>
		<td id="checks">
			<?php 
			$check_types = explode(',', $rule->check_type);
			$check_vals = explode('|', $rule->check_value); 
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
  				<option val=""               >None</option>
  				<option val="is_ip"          >Is IP(s)</option>
  				<option val="is_institution" >Is Institution(s)</option>
  				<option val="is_country"     >Is Country(s)</option>
  				<option val="is_region"      >Is Region(s)</option>
  				<option val="is_continent"   >Is Continent(s)</option>
  				<option val="is_user"        >Is User(s)</option>
  				<option val="is_logged_in"   >Is Logged In (T/F)</option>
  				<option val="is_subscribed"  >Is Subscribed (T/F)</option>
  			</select>
			<input id="check_value" placeholder="<?php echo $check['value']; ?>" data="<?php echo $check['value']; ?>">
				<?php if($index > 1) { ?>
					<a id="delete_check" href="#" rule-index="<?php echo $index; ?>" style="background-color:#f00;color:#fff;width:10px;height:10px;padding:3px 5px;text-decoration:none;">--</a>
				<?php } ?>
			<?php } ?>
		</td>
		<td>
  			<select id="result_type" data="<?php echo $rule->result_type; ?>">
  			  	<option val=""                 >None</option>
  				<option val="deny"             >DENY</option>
  				<option val="sign_up"          >SIGN UP</option>
  				<option val="checkpoint"       >CHECKPOINT</option>
  				<option val="free_trial"       >FREE TRIAL</option>
  				<option val="free_trial_thanks">FREE TRIAL THANKS</option>
  			</select>
			<input type="text" id="result_time_start" placeholder="Time Start: <?php echo $rule->result_time_start; ?>" data="<?php echo $rule->result_time_start; ?>">
			<input type="text" id="result_time_elapsed" placeholder="Time Elapsed: <?php echo $rule->result_time_elapsed ?>" data="<?php echo $rule->result_time_elapsed ?>">
			<br>Closable
			<input type="checkbox" id="result_closable" <?php echo ($rule->result_closable > 0) ? 'checked' : ''; ?>>
		</td>
		<?php if($rule_index > 0) { ?>
		<td class="row">
			<div class="col-xs-6">
				<a class="btn" id="access_delete_rule" rule-id="<?php echo $rule->id ?>">Delete Rule</a>
				<a class="btn" id="access_edit_rule" rule-id="<?php echo $rule->id ?>">Edit Rule</a>
			</div>
			<div class="col-xs-6">
				<!--a class="btn" id="access_add_selector" rule-id="<?php echo $rule->id ?>">Add Selector</a-->
				<a class="btn" id="access_add_check" rule-id="<?php echo $rule->id ?>">Add Check</a>
			</div>
		</td>
		<?php } else { ?>
		<td class="row">
			<div class="col-xs-12">
				<a class="btn" id="access_add_rule" rule-id="<?php echo $rule->id ?>">Add Rule</a>
			</div>
		</td>
		<?php } ?>
	</tr>
<?php
}
?>
</table>
<?php
  exit;
}
add_action( 'wp_ajax_nopriv_list-rules', 'list_rules' );
add_action( 'wp_ajax_list-rules', 'list_rules' );

/**
 * GLOBAL RULEBOOK SETTINGS PAGE
 * GUI FOR MANAGING RULES
 */


/**
 * register global rulebook page
 * @return [type] [description]
 */
function global_rulebook_menu(){
  add_options_page( "Global Access Rulebook", "Global Access Rulebook", "manage_options", "global_rulebook", "global_rulebook");
}
add_action('admin_menu', 'global_rulebook_menu');

/**
 * render global rulebook page
 * @return [type] [description]
 */
function global_rulebook(){
  ?>
  <div id="greyout" class="greyout">
	<div id="signal" class="signal"></div>
  </div>
  <h4>Category</h4>

  <b>DEBUG</b>
  <label class="switch">
    <input id="debug_toggle" type="checkbox" class="switch-input" <?php echo (get_option('access_debug', 'false') == 'true') ? 'checked' : ''; ?>>
    <span class="switch-label" data-on="On" data-off="Off"></span>
    <span class="switch-handle"></span>
  </label>

  <br>

  <div id="select_container">
	  <select id="category">
	    <option val="all">All</option>
	    <option val="adf">asdf</option>
	  </select>
  </div>

  <div id="results">
  </div>

  <script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script>
	$(function(){
		refresh();
		$('#debug_toggle').change(function() {
			$.post(MyAjax.ajaxurl, {
				action: 'ajax-update-option',
				option_name: 'access_debug',
				option_val: $(this).is(':checked')
			},
			function(response) {
			});
		});
		$('#results').on('click', 'a#access_add_rule', function() {
			var row = $(this).parent().parent().parent();
			update(row, {
				action: 'insert-rule',
				id: ''
			});
		});
		$('#results').on('click', 'a#access_delete_rule', function() {
			$.post(MyAjax.ajaxurl, {
				action: 'delete-rule',
				id: $(this).attr('rule-id')
			},
			function(response){
				refresh();
			});
		})
		$('#results').on('click', 'a#access_edit_rule', function() {
			var row = $(this).parent().parent().parent();
			// enable editing
			row.find('input').removeAttr('readonly');
			row.find('input[type="checkbox"]').removeAttr('disabled');
			row.find('select').removeAttr('disabled');
			  row.find('#access_add_check').each(function() {	$(this).show(); });
			  row.find('#delete_check').each(function() { $(this).show(); });

			row.find('input').each(function() {
				$(this).val($(this).attr('data'));
			});
			$(this).text('Update Rule');
			$(this).attr('id', 'access_update_rule');
		});
		$('#results').on('click', 'a#access_update_rule', function() {
			var row = $(this).parent().parent().parent();

			// disable editing again
			row.find('input').attr('readonly', '');
			row.find('input[type="checkbox"]').attr('disabled', '');
			row.find('select').attr('disabled', '');
			  row.find('#access_add_check').each(function() {	$(this).hide(); });
			  row.find('#delete_check').each(function() { $(this).hide(); });
			// switch to 'edit' button
			$(this).text('Edit Rule');
			$(this).attr('id', 'access_edit_rule');
			update(row, {});
		});
		/*$('#results').on('click', 'a#access_add_selector', function() {
			var row = $(this).parent().parent().parent();

			var selector_types = get_selector_types(row);
			var selector_vals = get_selector_vals(row);
			update(row, {
				selector_type: selector_types + ',none',
				selector_value: selector_vals + ',none'
			});
		});*/
		$('#results').on('click', 'a#access_add_check', function() {
			var row = $(this).parent().parent().parent();

			var check_types = get_check_types(row);
			var check_vals = get_check_vals(row);
			update(row, {
				check_type: check_types + ',none',
				check_value: check_vals + ',none'
			});
		});
		/*$('#results').on('click', 'a#delete_selector', function() {
			var row = $(this).parent().parent();
			var rule_index = $(this).attr('rule-index') - 1;

			var selector_types = get_selector_types(row);
			selector_types = selector_types.split(',');
			selector_types.splice(rule_index, 1);
			selector_types = selector_types.join(',');

			var selector_vals = get_selector_vals(row);
			selector_vals = selector_vals.split(',');
			selector_vals.splice(rule_index, 1);
			selector_vals = selector_vals.join(',');

			update(row, {
				selector_type: selector_types,
				selector_value: selector_vals
			});
		});*/
		$('#results').on('click', 'a#delete_check', function() {
			var row = $(this).parent().parent();
			var rule_index = $(this).attr('rule-index') - 1;

			var check_types = get_check_types(row);
			check_types = check_types.split(',');
			check_types.splice(rule_index, 1);
			check_types = check_types.join(',');

			var check_vals = get_check_vals(row);
			check_vals = check_vals.split(',');
			check_vals.splice(rule_index, 1);
			check_vals = check_vals.join(',');

			update(row, {
				check_type: check_types,
				check_value: check_vals
			});
		});
		$('#select_container select').change(refresh);
	});
	function update(row, params) {
		params = (typeof params !== "object") ? {} : params;
		params.action = params.hasOwnProperty("action") ? params.action : 'update-rule';
		params.id = params.hasOwnProperty("id") ? params.id : row.find('input#id').attr('data');
		params.priority = params.hasOwnProperty("priority") ? params.priority : row.find('#priority').val();
		/*if(!params.selector_type) {
			params.selector_type = get_selector_types(row);
		}
		if(!params.selector_value) {
			params.selector_value = get_selector_vals(row);
		}*/
		params.selector_type = params.hasOwnProperty("selector_type") ? params.selector_type : row.find('select#selector_type option:selected').attr('val');
		params.selector_value = params.hasOwnProperty("selector_value") ? params.selector_value : row.find('input#selector_value').val();
		if(!params.hasOwnProperty("check_type")) 
			params.check_type = get_check_types(row);
		if(!params.hasOwnProperty("check_value")) 
			params.check_value = get_check_vals(row);
		params.result_type = params.hasOwnProperty("result_type") ? params.result_type : row.find('#result_type option:selected').attr('val');
		params.result_time_start = params.hasOwnProperty("result_time_start") ? params.result_time_start : row.find('input#result_time_start').val();
		params.result_time_elapsed = params.hasOwnProperty("result_time_elapsed") ? params.result_time_elapsed : row.find('input#result_time_elapsed').val();
		params.result_closable = params.hasOwnProperty("result_closable") ? params.result_closable : ((row.find('input#result_closable').is(':checked')) ? 1 : 0);

		console.log(params);

		$.post(MyAjax.ajaxurl, params,
		function(response) {
			console.log(response);
			refresh();
		});
	}
	function refresh() {

		$('#greyout,#signal').show();
		$.post( MyAjax.ajaxurl, {
		    action : 'list-rules',
			},
			function( response ) {
		      $('#greyout,#signal').hide();
			  $('#results').html(response);
			  // disable editing
			  $('#results').find('input').attr('readonly', '');
			  $('#results').find('input[type="checkbox"]').attr('disabled', '');
			  $('#results').find('select').attr('disabled', '');
			  $('#results').find('a#access_add_check').each(function() {	$(this).hide(); });
			  $('#results').find('a#delete_check').each(function() { $(this).hide(); });


			  // visual assertion
			  $('#results').find('select').each(function() {
			  	var dat = $(this).attr('data');
				$(this).find('option[val="'+ dat +'"]').attr('selected', '');
			  });
			}
		);
	}
	/*function get_selector_types(row) {
		var selector_types = "";
		row.find('#selector_type option:selected').each(function() {
			if($(this).attr('val') === '')
				selector_types += 'none,';
			else
				selector_types += ($(this).attr('val') + ',');
		});
		selector_types = selector_types.substring(0, selector_types.length - 1);
		return selector_types;
	}
	function get_selector_vals(row) {
		var selector_vals = "";
		row.find('input#selector_value').each(function() {
			if($(this).val() === '')
				selector_vals += 'none,';
			else {
				selector_vals += ($(this).val() + ',');
			}
		});
		selector_vals = selector_vals.substring(0, selector_vals.length - 1);
		return selector_vals;
	}*/
	function get_check_types(row) {
		var check_types = "";
		row.find('#check_type option:selected').each(function() {
			if(isBlank($(this).attr('val'))) {
				check_types += 'none,';
			}
			else
				check_types += ($(this).attr('val') + ',');
		});
		check_types = check_types.substring(0, check_types.length - 1);
		return check_types;
	}
	function get_check_vals(row) {
		var check_vals = "";
		row.find('input#check_value').each(function() {
			if(isBlank($(this).val())) {
				check_vals += 'none,';
			}
			else
				check_vals += ($(this).val() + '|');
		});
		check_vals = check_vals.substring(0, check_vals.length - 1);
		return check_vals;
	}
	function isBlank(str) {
	    return (!str || /^\s*$/.test(str));
	}
  </script>
  <?php
}
?>