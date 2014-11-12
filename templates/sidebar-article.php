<div class="sidebar">

<?php 
global $user_inst;
$user_order = $user_inst['order'];

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

$order_type = $user_order->type;

if(in_array($order_type, $valid_trial_types)) {
?>
<div id="free-trial-notification">
	<span class="free-trial-head">TRIAL</span>
	<p>You are currently using trial access.<br>
	Please recommend JoMI to your institution.</p>
</div>
<?php } ?>

<h3>Share This Article</h3>

<input id="url-share-box" type="text" value="<?php echo (site_url('/article/') . get_field('publication_id') . '/'); ?>">
<br>
<br>
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<div class="addthis_sharing_toolbox"></div>

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

<table class="info">
	<tr>
		<h3>Information</h3>
	</tr>
	<tr>
		<td><strong>Publication Date</strong></td>
		<td><?php echo get_the_date(); ?></td>
	</tr>
	<tr>
		<td><strong>Article ID</strong></td>
		<td><?php the_field('publication_id'); ?></td>
	</tr>
	<tr>
		<td><strong>Volume</strong></td>
		<td><?php the_time('Y'); ?></td>
	</tr>
	<tr>
		<td><strong>Issue</strong></td>
		<td><?php the_time('n'); ?></td>
	</tr>
</table>

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