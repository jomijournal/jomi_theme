<div class="sidebar">

<?php 
global $user_inst;
$user_order = $user_inst['order'];

global $stripe_user_subscribed;
global $stripe_user_stripe;
global $stripe_user_active_sub;

// all valid order types that will trigger a free trial smidget
$valid_trial_types = array(
	'free-trial',
	'Free-Trial',
	'trial',
	'Trial',
	'free trial',
	'Free Trial'
);
// all valid order types that will changed sidebar text to 'Subscribed'
$valid_subscribed_types = array(
	'',
	'default',
	'Default',
	'subscribed',
	'Subscribed'
);

// display free trial
if(!empty($user_order)) {
	$order_type = $user_order->type;

	$status = get_post_status();

	if(in_array($order_type, $valid_trial_types)) {
	?>
	<div id="free-trial-notification">
		<span class="free-trial-head">TRIAL</span>
		<p>You are currently using trial access.<br>
		Please recommend JoMI to your institution.</p>
	</div>
	<?php } 
} elseif ($stripe_user_subscribed) { ?>
	
	<div class="row stripe-notification">
		<div class="col-xs-12">
			<span class="stripe-subscribed">Subscribed</span>
		</div>
		<div class="col-xs-12">
			<span class="plan">Plan:</span>
			<span class="plan-details"><?php echo $stripe_user_active_sub['plan']['name']; ?></span>
		</div>
	</div>

<?php } ?>


<h3>Share This Article</h3>

<input id="url-share-box" type="text" value="<?php echo (site_url('/article/') . get_field('publication_id') . '/'); ?>">
<br>
<br>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_sharing_toolbox"></div>

<!-- AUTHOR INFO -->
<h3>Authors</h3>
<?php
$coauthors = get_coauthors();
foreach( $coauthors as $coauthor )
{
	?>
	<!--a class="author" href="/author/<?php echo $coauthor->user_nicename; ?>"></a-->
	<a class="author" href="<?php echo site_url('/author/' . $coauthor->user_nicename); ?>">
	  	<div class="avatar col-xs-3">
	  		<?php echo get_wp_user_avatar($coauthor->ID, 64); ?>
	  	</div>
	  	<div class="bio col-xs-9">
	    	<h4><?php echo $coauthor->display_name; ?></h4>
	    	<h5><?php echo $coauthor->description; ?></h5>
	  	</div>
	  	<div style="clear:both;"></div>
	</a>
<?php
}
?>


<!-- INSTITUTION INFO -->
<h3>Institution</h3>
<h5 style="text-align:center;"><?php the_field('hospital_name'); ?></h5>
<div id="view1">
<?php
$location = get_field('hospital_map');
if( ! empty($location) ):
?>
<div id="map" style="width: 100%; height: 200px;"></div>
<script src='https://maps.googleapis.com/maps/api/js?sensor=false' type='text/javascript'></script>

<script type="text/javascript">
  //<![CDATA[
	function load() {
	var lat = <?php echo $location['lat']; ?>;
	var lng = <?php echo $location['lng']; ?>;
// coordinates to latLng
	var latlng = new google.maps.LatLng(lat, lng);
// map Options
	var myOptions = {
	zoom: 14,
	center: latlng,
	mapTypeId: google.maps.MapTypeId.ROADMAP
   };
//draw a map
	var map = new google.maps.Map(document.getElementById("map"), myOptions);
	var marker = new google.maps.Marker({
	position: map.getCenter(),
	map: map
   });
}
// call the function
   load();
//]]>
</script>
<?php endif; ?> 
</div>



<!-- ARTICLE INFORMATION -->
<table class="info">
	<tr>
		<h3>Information</h3>
	</tr>
	<tr>
		<td><strong>Publication Date</strong></td>
		<?php if($status == 'publish') { ?>
			<td><?php echo get_the_date(); ?></td>
		<?php } else { ?>
			<td>Article Not Yet Published</td>
		<?php } ?>
	</tr>
	<tr>
		<td><strong>Article ID</strong></td>
		<td><?php the_field('publication_id'); ?></td>
	</tr>
	<tr>
		<td><strong>Volume</strong></td>
		<?php if($status == 'publish') { ?>
			<td><?php the_time('Y'); ?></td>
		<?php } else { ?>
			<td>Article Not Yet Published</td>
		<?php } ?>
	</tr>
	<tr>
		<td><strong>Issue</strong></td>
		<?php if($status == 'publish') { ?>
			<td><?php the_time('n'); ?></td>
		<?php } else { ?>
			<td>Article Not Yet Published</td>
		<?php } ?>
	</tr>
</table>


<!-- PUBLISH NOTIFICATION -->
<?php if($status != 'publish') { ?> 
<h3>Stay Updated</h3>
<table class="info">
	<tr>
		<td id="notification-status" class="notification-status">Request Sent!</td>
	</tr>
	<tr>
		
		<td><input type="text" placeholder="Email:" id="notification-input" class="notification-input">
		<a href="#" class="btn notification-submit" id="notification-submit">Submit</a></td>
	</tr>
</table>

<script>
$('#notification-submit').on('click', function(e){
	e.preventDefault();

	var content = 'Article <?php echo get_field("publication_id"); ?> - <?php echo get_the_title(); ?>';
	var email = $('#notification-input').val();

	if(!isEmail(email)) {
		$('.notification-status').css('background-color', '#FF4A4A');
		$('.notification-status').html('Invalid Email!');
		$('.notification-status').show();
		return;
	}

	$.post(MyAjax.ajaxurl, {
		action: 'send-notification-email'
		, content: content
		, email: email
	}, function(response) {
		$('.notification-status').css('background-color', '#2EBB2E');
		$('.notification-status').html('Request Sent!');
		$('.notification-status').show();
	});
});
//stolen from http://badsyntax.co/post/javascript-email-validation-rfc822
function isEmail(email){
    return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
}
</script>

<?php } ?>

<?php 
global $user_inst;
//print_r($inst);

if(!empty($user_inst)) {
	$inst = $user_inst['inst'];
	$ip = $user_inst['ip'];
	$location = $user_inst['location'];
	$order = $user_inst['order'];
	$is_sub = $user_inst['is_subscribed'];
	?>
<table class="info">
	<tr>
		<h3>Subscribing Institution</h3>
	</tr>
	<tr>
		<td><strong>Name</strong></td>
		<td><?php echo $inst->name; ?></td>
	</tr>
	<!--tr>
		<td><strong>Location</strong></td>
		<td><?php echo $location->description; ?><br><?php echo ($location->address . ', ' . $location->city . ' ' . $location->region . ' ' . $location->zip)?></td>
	</tr-->
	<tr>
		<td><strong>Status</strong></td>
		<td><?php echo ($is_sub > 0) ? '<!--span style="color:green;font-weight:bold;">SUBSCRIBED' : '<span style="color:red;font-weight:bold;">EXPIRED'; ?></span-->
		<?php if($is_sub > 0) { ?>
		<strong style="color:green;font-weight:bold;"><?php echo $order->type; ?></strong>
		<?php } else { ?>
		<strong style="color:red;font-weight:bold;">Not Subscribed</strong>
		<?php } ?>
		<br>
		<?php echo "Expires on " . $order->date_end; ?></td>
	</tr>
</table>

	<?php
}

?>

</div>