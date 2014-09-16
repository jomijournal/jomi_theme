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
	<div class="col-xs-3">
		<table class="inst-list" id="inst-list">
		</table>
	</div>
	<div class="col-xs-9">
		<table class="inst-location-list" id="inst-location-list">
		</table>
	</div>
</div>

<script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
$(function() {
	refresh();

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
			console.log(response);
			refresh();
		});
	});

	$('#inst-list').on('click', 'td a#delete_inst', function() {
		$('#greyout,#signal').show();
		$.post(MyAjax.ajaxurl, {
			action: 'delete-inst',
			id: $(this).parent().parent().find('td#input').attr('inst-id')
		},
		function(response) {
			$('#greyout,#signal').hide();
			console.log(response);
			refresh();
		});
	});

	$('#inst-list').on('click', 'td a#insert-inst-submit', function() {
		$('#greyout,#signal').show();
		$.post(MyAjax.ajaxurl, {
			action: 'insert-inst',
			name: $(this).parent().parent().find('#insert-inst').val()
		},
		function(response) {
			$('#greyout,#signal').hide();
			console.log(response);
			refresh();
		});
	});

	$('#inst-location-list').on('click', '#inst-ip-add-submit', function() {

	});


})
function refresh() {
	$('#greyout,#signal').show();
	$.post(MyAjax.ajaxurl,{
		action: 'inst-list-update'
	},
	function(response) {
		$('#greyout,#signal').hide();
		console.log(response);
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
		console.log(response);
		$('#inst-location-list').html(response);
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
		<a id="update_inst" href="#"><span class="glyphicon glyphicon-floppy-disk"></span></a>
		<a id="delete_inst" href="#"><span class="glyphicon glyphicon-remove"></span></a>
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
	<th>Info</th>
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
		<input id="inst-location-description" type="text" value="<?php echo $location->description; ?>">
		<br>
		<input id="inst-location-address" type="text" value="<?php echo $location->address; ?>">
		<br>
		<input id="inst-location-city" type="text" value="<?php echo $location->city; ?>">
		<br>
		<input id="inst-location-region" type="text" value="<?php echo $location->region; ?>">
		<br>
		<input id="inst-location-country" type="text" value="<?php echo $location->country; ?>">
	</td>
	<td>
		<?php echo 'orders'; ?>
	</td>
	<td>
		<?php inst_ip_update($location->id); ?>
	</td>
	<td>
		<a id="inst-location-update" href="#">update</a>
		<br>
		<a id="inst-location-delete" href="#">delete</a>
	</td>
</tr>
<?php
}
?>
<!-- add location ui -->
<tr>
	<td>
		<input id="inst-add-location-description" type="text" value="">
	</td>
	<td>
		<a id="inst-location-add" href="#">add</a>
	</td>
</tr>
<?php
}
add_action( 'wp_ajax_nopriv_inst-location-update', 'inst_location_update');
add_action( 'wp_ajax_inst-location-update', 'inst_location_update');

function inst_ip_update($location_id) {

?>
<table class="inst-ip-list">
	<tr>
		<th>IP Start</th>
		<th>IP End</th>
		<th>Actions</th>
	</tr>
	<tr>
		<td><input id="inst-ip-add-start" type="text" value="" placeholder="Start IP Range"></td>
		<td><input id="inst-ip-add-end" type="text" value="" placeholder="End IP Range"></td>
		<td><a id="inst-ip-add-submit" href="#">add</a></td>
	</tr>
<?php 

global $wpdb;
global $inst_ip_table_name;

$inst_ip_query = "SELECT * FROM $inst_ip_table_name WHERE location_id = $location_id";
$ips = $wpdb->get_results($inst_ip_query);

foreach($ips as $ip) {
?>
<tr>
	<td><input type="text" value="<?php echo long2ip($ip->start); ?>"></td>
	<td><input type="text" value="<?php echo long2ip($ip->end); ?>"></td>
	<td>
		<a id="inst-ip-update" href="#">update</a>
		<br>
		<a id="inst-ip-delete" href="#">delete</a>
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

?>