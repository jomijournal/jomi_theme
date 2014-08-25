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

?>
<h3 style="text-align:center;">INSTITUTION MANAGEMENT</h3>
<div class="row">
	<div class="col-xs-3">
		<table class="inst-list" id="inst-list">
		</table>
	</div>
	<div class="col-xs-9">
	</div>
</div>

<script type="text/javascript" src="/wp-content/themes/jomi/assets/js/scripts.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>
$(function() {
	refresh();
	$('#inst-list').on('click', 'td', function() {
		//console.log($(this).html());
		
		// reset previous active element
		$('#inst-list').find('td.active').removeClass('active');
		$(this).addClass('active');
	})
})
function refresh() {
	$.post(MyAjax.ajaxurl,{
		action: 'inst-list-update'
	},
	function(response) {
		console.log(response);
		$('#inst-list').html(response);
	});
}
</script>
<?php
}


function inst_list_update() {
?>
<tr>
	<th>Institution List</th>
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
	<td><?php echo $inst->name; ?></td>
</tr>
<?php
}


}
add_action( 'wp_ajax_nopriv_inst-list-update', 'inst_list_update');
add_action( 'wp_ajax_inst-list-update', 'inst_list_update');
?>