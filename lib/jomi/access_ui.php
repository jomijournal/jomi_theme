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
			<input type="number" id="priority" placeholder="<?php echo $rule->priority; ?>" data="<?php echo $rule->priority; ?>">
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
					<a id="delete_check" rule-index="<?php echo $index; ?>" href="#" style="background-color:#f00;color:#fff;width:10px;height:10px;padding:3px 5px;text-decoration:none;">--</a>
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
			<input type="number" id="result_time_start" placeholder="Time Start: <?php echo $rule->result_time_start; ?>" data="<?php echo $rule->result_time_start; ?>">
			<input type="number" id="result_time_end" placeholder="Time End: <?php echo $rule->result_time_end; ?>" data="<?php echo $rule->result_time_end; ?>">
			<input type="number" id="result_time_elapsed" placeholder="Time Elapsed: <?php echo $rule->result_time_elapsed ?>" data="<?php echo $rule->result_time_elapsed ?>">
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

			var row = $(this).parent().parent().parent();

			// disable editing again
			row.find('input').attr('readonly', '');
			row.find('select').attr('disabled', '');
			// switch to 'edit' button
			$(this).text('Edit Rule');
			$(this).attr('id', 'access_edit_rule');

			update(row, {});

		});
		$('#results').on('click', 'a#access_add_selector', function() {

			var row = $(this).parent().parent().parent();

			var selector_types = "";
			row.find('#selector_type option:selected').each(function() {
				selector_types += ($(this).attr('val') + ',');
			});
			var selector_vals = "";
			row.find('#selector_value').each(function() {
				selector_vals += ($(this).val() + ',');
			});

			update(row, {
				selector_type: selector_types + 'none',
				selector_value: selector_vals + 'none'
			});
		});
		$('#results').on('click', 'a#access_add_check', function() {

			var row = $(this).parent().parent().parent();

			var check_types = "";
			row.find('#check_type option:selected').each(function() {
				check_types += ($(this).attr('val') + ',');
			});
			var check_vals = "";
			row.find('#check_value').each(function() {
				check_vals += ($(this).val() + ',');
			});

			update(row, {
				check_type: check_types + 'none',
				check_value: check_vals + 'none'
			});
		});
		$('#select_container select').change(refresh);
	});
	function update(row, params) {

		//params = (typeof prop !== "object") ? {} : params;
		params.action = params.action || 'update-rule';
		params.id = params.id || row.find('input#id').attr('data');
		params.priority = params.priority || row.find('#priority').val();
		if(!params.selector_type) {
			console.log('update');
			var selector_types = "";
			row.find('#selector_type option:selected').each(function() {
				selector_types += ($(this).attr('val') + ',');
			});
			params.selector_type = selector_types.substring(0, selector_types.length - 1);
		}
		if(!params.selector_value) {
			var selector_vals = "";
			row.find('#selector_value').each(function() {
				selector_vals += ($(this).val() + ',');
			});
			params.selector_value = selector_vals.substring(0, selector_vals.length - 1);
		}
		if(!params.check_type) {
			var check_types = "";
			row.find('#check_type option:selected').each(function() {
				check_types += ($(this).attr('val') + ',');
			});
			params.check_type = check_types.substring(0, check_types.length - 1);
		}
		if(!params.check_value) {
			var check_vals = "";
			row.find('#check_value').each(function() {
				check_vals += ($(this).val() + ',');
			});
			params.check_value = check_vals.substring(0, check_vals.length - 1);
		}
		params.result_type = params.result_type || row.find('#result_type option:selected').attr('val');
		params.result_time_start = params.result_time_start || row.find('#result_time_start').val();
		params.result_time_end = params.result_time_end || row.find('#result_time_end').val();
		params.result_time_elapsed = row.find('#result_time_elapsed').val();

		console.log(params);

		$.post(MyAjax.ajaxurl, params,
		function(response) {
			console.log(response);
			refresh();
		});
	}
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
?>