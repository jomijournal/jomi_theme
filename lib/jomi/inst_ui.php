<?php
/**
 * INSTITUTION UI
 */

/**
 * INSTITUTION SETTINGS PAGE
 * GUI FOR MANAGING INSTITUTION
 */
add_action('admin_menu', 'inst_register_menu');
/**
 * show this page on the settings menu
 * @return [type] [description]
 */
function inst_register_menu(){
  add_options_page( "Institution Management", "Institution Management", "manage_options", "inst_menu", "inst_menu");
}
/**
 * render the list of institutions
 * @return [type] [description]
 */
function inst_menu(){

?>
<h3 style="text-align:center;">INSTITUTION MANAGEMENT</h3>
<div id="greyout" class="greyout">
	<div id="signal" class="signal"></div>
</div>
<div class="row">
	<div class="col-md-3 inst-list-col">
		<table class="inst-list" id="inst-list">
		</table>
	</div>
	<div class="col-md-9 inst-location-list-col">
		<table class="inst-location-list" id="inst-location-list">
		</table>
	</div>
</div>

<script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
$(function() {
	refresh();

	// institution list jquery
	$('#inst-list').on('click', 'td#input, td#insert', function() {
		if($(this).attr('id') == 'insert') return;
		// reset previous active element
		$('#inst-list').find('td.active')
			.removeClass('active')
			.find('input#inst-name').attr('readonly', '')
									.val('');

		$(this).addClass('active');
		$(this).find('input#inst-name')
			.removeAttr('readonly')
			.val($(this).find('input#inst-name').attr('placeholder'));
		refresh_location($(this).attr('inst-id'));
	});
	$('#inst-list').on('click', 'td a#update_inst', function() {
		$('#greyout,#signal').show();
		$.post(MyAjax.ajaxurl, {
			action: 'update-inst',
			id: $(this).parent().parent().find('td#input').attr('inst-id'),
			name: $(this).parent().parent().find('td#input input#inst-name').val()
		},
		function(response) {
			$('#greyout,#signal').hide();
			refresh();
		});
	});
	$('#inst-list').on('click', 'td a#delete_inst', function() {

		if(confirm("are you sure?")) {
			$('#greyout,#signal').show();
			$.post(MyAjax.ajaxurl, {
				action: 'delete-inst',
				id: $(this).parent().parent().find('td#input').attr('inst-id')
			},
			function(response) {
				$('#greyout,#signal').hide();
				refresh();
			});
		}
	});
	$('#inst-list').on('click', 'td a#insert-inst-submit', function() {
		$('#greyout,#signal').show();
		$.post(MyAjax.ajaxurl, {
			action: 'insert-inst',
			name: $(this).parent().parent().find('#insert-inst').val()
		},
		function(response) {
			$('#greyout,#signal').hide();
			refresh();
		});
	});

	// location list jquery
	$('#inst-location-list').on('click', '#inst-location-add', function() {
		var row = $(this).parent().parent();
		var inst_id = row.find('#inst-add-location-inst-id').val();
		var description = row.find('#inst-add-location-description').val();

		$.post(MyAjax.ajaxurl, {
			action: 'insert-inst-location',
			id: inst_id,
			description: description 
		}, function(response) {
			refresh_location(inst_id);
		})
	});
	$('#inst-location-list').on('click', '#inst-location-update', function() {
		var row = $(this).parent().parent();

		var id = row.find('#inst-location-id').val();
		var inst_id = row.find('#inst-location-inst-id').val();
		var description = row.find('#inst-location-description').val();
		var address = row.find('#inst-location-address').val();
		var city = row.find('#inst-location-city').val();
		var region = row.find('#inst-location-region').val();
		var country = row.find('#inst-location-country').val();
		var zip = row.find('#inst-location-zip').val();

		$.post(MyAjax.ajaxurl, {
			action: 'update-inst-location',
			id: id,
			inst_id: inst_id,
			description: description,
			address: address,
			city: city,
			region: region,
			country: country,
			zip: zip
		}, function(response) {
			refresh_location(inst_id);
		});
	});
	$('#inst-location-list').on('click', '#inst-location-delete', function() {
		var row = $(this).parent().parent();

		var id = row.find('#inst-location-id').val();
		var inst_id = row.find('#inst-location-inst-id').val();

		if(confirm('are you sure?')) {
			$.post(MyAjax.ajaxurl, {
				action: 'delete-inst-location',
				id: id
			}, function(response) {
				refresh_location(inst_id);
			});
		}
	});

	// ip list jquery
	$('#inst-location-list').on('click', '#inst-ip-add-submit', function() {
		var row = $(this).parent().parent();

		var location_id = row.find('#inst-ip-add-location-id').val();
		var ip_start = row.find('#inst-ip-add-start').val();
		var ip_end = row.find('#inst-ip-add-end').val();

		$.post(MyAjax.ajaxurl, {
			action: 'insert-inst-ip',
			location_id: location_id,
			ip_start: ip_start,
			ip_end: ip_end
		}, function(response) {
			refresh_ip_list(location_id);
		});
	});
	$('#inst-location-list').on('click', '#inst-ip-update', function() {
		var row = $(this).parent().parent();

		var id = row.find('#inst-ip-id').val();
		var location_id = row.find('#inst-ip-location-id').val();
		var ip_start = row.find('#inst-ip-start').val();
		var ip_end = row.find('#inst-ip-end').val();

		$.post(MyAjax.ajaxurl, {
			action: 'update-inst-ip',
			id: id,
			location_id: location_id,
			ip_start: ip_start,
			ip_end: ip_end
		}, function(response) {
			refresh_ip_list(location_id);
		})
	});
	$('#inst-location-list').on('click', '#inst-ip-delete', function() {
		var row = $(this).parent().parent();

		var id = row.find('#inst-ip-id').val();
		var location_id = row.find('#inst-ip-location-id').val();

		if(confirm("are you sure?")) {
			$.post(MyAjax.ajaxurl, {
				action: 'delete-inst-ip',
				id: id
			}, function(response) {
				refresh_ip_list(location_id);
			});
		}
	});

	//institution order jquery
	$('#inst-location-list').on('click', '#inst-order-insert', function(){
		var table = $(this).parent().parent().parent().parent();

		// don't need inst_id (for now)
		var inst_id = 0;
		var location_id = table.attr('location-id');

		var date = new Date();
		var date_start = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
		var date_end = (date.getFullYear() + 1) + '-' + date.getMonth() + '-' + date.getDate();

		var type = 'default';
		var amount = -1;

		$.post(MyAjax.ajaxurl, {
			action: 'insert-inst-order',
			inst_id: inst_id,
			location_id: location_id,
			date_start: date_start,
			date_end: date_end,
			type: type,
			amount: amount
		}, function(response) {
			refresh_order_list(location_id);
		});
	});

});

function refresh() {
	$('#greyout,#signal').show();
	$.post(MyAjax.ajaxurl,{
		action: 'inst-list-update'
	},
	function(response) {
		$('#greyout,#signal').hide();
		$('#inst-list').html(response);
		$('#inst-list').find('input#inst-name').attr('readonly', '');
	});
	refresh_location();

}
function refresh_location(id) {
	$('#greyout,#signal').show();
	$.post(MyAjax.ajaxurl, {
		action: 'inst-location-update',
		id: id
	},
	function(response) {
		$('#greyout,#signal').hide();
		$('#inst-location-list').html(response);
	});
}
function refresh_ip_list(location_id) {
	$('#greyout,#signal').show();
	$.post(MyAjax.ajaxurl, {
		action: 'inst-ip-update',
		location_id: location_id
	},
	function(response) {
		$('#greyout,#signal').hide();
		$('#inst-ip-list[location-id="' + location_id + '"]').html(response);
	});
}
function refresh_order_list(location_id) {
	$('#greyout,#signal').show();
	$.post(MyAjax.ajaxurl, {
		action: 'inst-order-update',
		location_id: location_id
	}, function(response) {
		$('#greyout,#signal').hide();
		$('#inst-order-list[location-id="' + location_id + '"]').html(response);
	});
}
</script>
<?php
}

/**
 * update institution list
 * @return [type] [description]
 */
function inst_list_update() {
?>
<tr>
	<th>Institution List</th>
	<th></th>
</tr>
<?php

global $wpdb;
global $inst_table_name;

$inst_query = "SELECT * FROM $inst_table_name";

$insts = $wpdb->get_results($inst_query);
//print_r($insts);
foreach($insts as $inst) {
?>
<tr>
	<td id="input" inst-id="<?php echo $inst->id; ?>">
		<input id="inst-name" type="text" placeholder="<?php echo $inst->name; ?>">
	</td>
	<td>
		<a id="update_inst"><span class="glyphicon glyphicon-floppy-disk"></span></a>
		<a id="delete_inst"><span class="glyphicon glyphicon-remove"></span></a>
	</td>
</tr>
<?php
}
?>
<tr>
	<td id="insert"><input id="insert-inst" type="text"></td>
	<td><a id="insert-inst-submit" href="#"><span class="glyphicon glyphicon-plus"></span></a></td>
</tr>
<?php
}
add_action( 'wp_ajax_nopriv_inst-list-update', 'inst_list_update');
add_action( 'wp_ajax_inst-list-update', 'inst_list_update');

/**
 * render the institution location list
 * @return [type] [description]
 */
function inst_location_update() {

?>
<!-- headers -->
<tr>
	<th>Location Info</th>
	<th>Orders</th>
	<th>IPs</th>
	<th>Actions</th>
</tr>
<?php

global $wpdb;
global $inst_table_name;
global $inst_location_table_name;
global $inst_ip_table_name;
global $inst_order_table_name;

$id = (empty($_POST['id'])) ? 1 : $_POST['id'];

$inst_location_query = "SELECT * FROM $inst_location_table_name WHERE inst_id = $id";
$locations = $wpdb->get_results($inst_location_query); 
//print_r($locations);
foreach($locations as $location) {
?>
<!-- data -->
<tr>
	<td>
		<input id="inst-location-description" type="text" placeholder="description" value="<?php echo $location->description; ?>">
		<br>
		<input id="inst-location-address" type="text" placeholder="address" value="<?php echo $location->address; ?>">
		<br>
		<input id="inst-location-city" type="text" placeholder="city" value="<?php echo $location->city; ?>">
		<br>
		<input id="inst-location-region" type="text" placeholder="region" value="<?php echo $location->region; ?>">
		<br>
		<input id="inst-location-country" type="text" placeholder="country" value="<?php echo $location->country; ?>">
		<br>
		<input id="inst-location-zip" type="text" placeholder="zip code" value="<?php echo $location->zip; ?>">
		<input id="inst-location-id" type="hidden" value="<?php echo $location->id; ?>">
		<input id="inst-location-inst-id" type="hidden" value="<?php echo $id; ?>">
	</td>
	<td>
		<?php inst_order_update($location->id); ?>
	</td>
	<td>
		<?php inst_ip_update($location->id); ?>
	</td>
	<td>
		<a id="inst-location-update">update</a>
		<br>
		<a id="inst-location-delete">delete</a>
	</td>
</tr>
<?php
}
?>
<!-- add location ui -->
<tr>
	<td>
		<input id="inst-add-location-description" type="text" value="">
		<input id="inst-add-location-inst-id" type="hidden" value="<?php echo $id; ?>">
	</td>
	<td>
		<a id="inst-location-add">add</a>
	</td>
</tr>
<?php
}
add_action( 'wp_ajax_nopriv_inst-location-update', 'inst_location_update');
add_action( 'wp_ajax_inst-location-update', 'inst_location_update');

/**
 * render ip lists
 * @param  [type] $location_id [description]
 * @return [type]              [description]
 */
function inst_ip_update($location_id) {

// allow use via ajax if the post variable is set
if(!empty($_POST['location_id'])) $location_id = $_POST['location_id'];

?>
<table id="inst-ip-list" class="inst-ip-list" location-id="<?php echo $location_id; ?>">
	<tr>
		<th>IP Start</th>
		<th>IP End</th>
		<th>Actions</th>
	</tr>
	<tr>
		<td><input id="inst-ip-add-start" type="text" placeholder="Start IP Range"></td>
		<td><input id="inst-ip-add-end" type="text" placeholder="End IP Range"></td>
		<td>
			<a id="inst-ip-add-submit">add</a>
			<input id="inst-ip-add-location-id" type="hidden" value="<?php echo $location_id; ?>">
		</td>
	</tr>
<?php 

global $wpdb;
global $inst_ip_table_name;

$inst_ip_query = "SELECT * FROM $inst_ip_table_name WHERE location_id = $location_id";
$ips = $wpdb->get_results($inst_ip_query);

foreach($ips as $ip) {
?>
<tr>
	<td><input id="inst-ip-start" type="text" value="<?php echo long2ip($ip->start); ?>"></td>
	<td><input id="inst-ip-end" type="text" value="<?php echo long2ip($ip->end); ?>"></td>
	<td>
		<a id="inst-ip-update">update</a>
		<br>
		<a id="inst-ip-delete">delete</a>

		<input id="inst-ip-id" type="hidden" value="<?php echo $ip->id; ?>">
		<input id="inst-ip-location-id" type="hidden" value="<?php echo $location_id; ?>">
	</td>
</tr>
<?php 
}
?>
</table>
<?php
}
add_action( 'wp_ajax_nopriv_inst-ip-update', 'inst_ip_update');
add_action( 'wp_ajax_inst-ip-update', 'inst_ip_update');

/**
 * render the order table
 * @param  [type] $location_id [description]
 * @return [type]              [description]
 */
function inst_order_update($location_id) {
	// use the POST variable if its set (if being used via AJAX)
	if(!empty($_POST['location_id'])) $location_id = $_POST['location_id'];

?>
<table id="inst-order-list" class="inst-order-list" location-id="<?php echo $location_id; ?>">
<tr>
	<td>
		<a id="inst-order-insert">add</a>
	</td>
</tr>
<?php

global $wpdb;
global $inst_order_table_name;

$inst_order_query = "SELECT * FROM $inst_order_table_name WHERE location_id = $location_id";
$orders = $wpdb->get_results($inst_order_query);

foreach($orders as $order) {
?>
<tr>
	<th>Date Start</th>
	<td><input id="inst-order-date-start" type="date" value="<?php echo $order->date_start; ?>"></td>
</tr>
<tr>
	<th>Date End</th>
	<td><input id="inst-order-date-end" type="date" value="<?php echo $order->date_end; ?>"></td>
</tr>
<tr>
	<th>Type</th>
	<td><input id="inst-order-type" type="text" value="<?php echo $order->type; ?>"></td>
</tr>
<tr>
	<th>Amount</th>
	<td><input id="inst-order-amount" type="number" value="<?php echo $order->amount; ?>"></td>
</tr>
<tr>
	<th>Actions</th>
	<td>
		<a id="inst-order-update">update</a> | 
		<a id="inst-order-delete">delete</a>
	</td>
</tr>
<tr>
	<td><br></td>
</tr>
<?php 
}
?>
</table>
<?php
}
add_action( 'wp_ajax_nopriv_inst-order-update', 'inst_order_update');
add_action( 'wp_ajax_inst-order-update', 'inst_order_update');

?>