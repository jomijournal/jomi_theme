<?php 

add_action('show_user_profile', 'add_user_order_table');
add_action('edit_user_profile', 'add_user_order_table');

function add_user_order_table($user) {
?>

<h1>USER ORDERS</h1>

<div id="user-orders"></div>
<div id="greyout" class="greyout">
	<div id="signal" class="signal"></div>
</div>

<script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
<link rel="stylesheet" href="/wp-content/themes/jomi/assets/css/main.min.css?v=b85ad1&amp;ver=4.0">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>

jQuery(function() {

	refresh_user_order_list();
	
	//institution order jquery
	jQuery('#user-orders').on('click', '#user-order-insert', function(e){
		e.preventDefault();

		var table = jQuery(this).parent().parent().parent().parent();

		// don't need inst_id (for now)
		var inst_id = 0;
		var user_id = table.attr('user-id');
		var location_id = -1;

		var date = new Date();
		var date_start = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
		var date_end = (date.getFullYear() + 1) + '-' + date.getMonth() + '-' + date.getDate();

		var type = 'default';
		var amount = -1;

		jQuery.post(MyAjax.ajaxurl, {
			action: 'insert-inst-order',
			inst_id: inst_id,
			location_id: location_id,
			user_id: user_id,
			date_start: date_start,
			date_end: date_end,
			type: type,
			amount: amount
		}, function(response) {
			refresh_user_order_list();
		});
	});
	jQuery('#user-orders').on('click', '#user-order-update', function(e) {
		e.preventDefault();

		var table = jQuery(this).parent().parent().parent().parent();

		var id = table.find('#user-order-id').val();

		// dont need inst id for now
		var inst_id = 0;
		var user_id = table.attr('user-id');
		var location_id = -1;

		var date_start = table.find('#user-order-date-start').val();
		var date_end = table.find('#user-order-date-end').val();

		var type = table.find('#user-order-type').val();
		var amount = table.find('#user-order-amount').val();

		jQuery.post(MyAjax.ajaxurl, {
			action: 'update-inst-order',
			id: id,
			inst_id: inst_id,
			location_id: location_id,
			user_id: user_id,
			date_start: date_start,
			date_end: date_end,
			type: type,
			amount: amount
		}, function(response) {
			refresh_user_order_list();
		});
	});
	jQuery('#user-orders').on('click', '#user-order-delete', function(e) {
		e.preventDefault();

		var table = jQuery(this).parent().parent().parent().parent();

		var id = table.find('#user-order-id').val();
		var user_id = table.attr('user-id');
		var location_id = -1;

		jQuery.post(MyAjax.ajaxurl, {
			action: 'delete-inst-order',
			id: id
		}, function(response) {
			refresh_user_order_list();
		});
	});
});

function refresh_user_order_list() {
	jQuery('#greyout,#signal').show();

	jQuery.post(MyAjax.ajaxurl, {
		action: 'user-order-update',
		user_id: '<?php echo $user->ID; ?>'
	}, function(response) {
		jQuery('#greyout,#signal').hide();
		//console.log(response);
		jQuery('#user-orders').html(response);
	});
}



</script>

<?php
}




/**
 * render the order table
 * @param  [type] $location_id [description]
 * @return [type]              [description]
 */
function user_order_update($user_id) {
	// use the POST variable if its set (if being used via AJAX)
	if(!empty($_POST['user_id'])) $user_id = $_POST['user_id'];

?>
<table id="user-order-list" class="inst-order-list" user-id="<?php echo $user_id; ?>">
<tr>
	<td>
		<a href="#" id="user-order-insert">add</a>
	</td>
</tr>
<?php

global $wpdb;
global $inst_order_table_name;

$inst_order_query = "SELECT * FROM $inst_order_table_name WHERE user_id = $user_id";
$orders = $wpdb->get_results($inst_order_query);

foreach($orders as $order) {
?>
<tr>
	<th>Date Start</th>
	<td><input id="user-order-date-start" type="date" value="<?php echo $order->date_start; ?>"></td>
</tr>
<tr>
	<th>Date End</th>
	<td><input id="user-order-date-end" type="date" value="<?php echo $order->date_end; ?>"></td>
</tr>
<tr>
	<th>Type</th>
	<td><input id="user-order-type" type="text" value="<?php echo $order->type; ?>"></td>
</tr>
<tr>
	<th>Amount</th>
	<td><input id="user-order-amount" type="number" value="<?php echo $order->amount; ?>"></td>
</tr>
<tr>
	<th>Actions</th>
	<td>
		<a href="#" id="user-order-update">update</a> | 
		<a href="#" id="user-order-delete">delete</a>
		<input id="user-order-id" type="hidden" value="<?php echo $order->id; ?>">
	</td>
</tr>
<?php 
}
?>
</table>
<?php
}
add_action( 'wp_ajax_nopriv_user-order-update', 'user_order_update');
add_action( 'wp_ajax_user-order-update', 'user_order_update');

?>