<div class="sidebar">

<?php
global $user_inst;
$user_order = $user_inst['order'];

global $access_blocks;
$logged_in = $_SESSION['access_logged_in'];


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

	$inst = $user_inst['inst'];
	$ip = $user_inst['ip'];
	$location = $user_inst['location'];
	$is_sub = $user_inst['is_subscribed'];

	$require_login = $order->require_login;
	$require_login = (empty($_GET['requirelogin'])) ? $require_login : $_GET['requirelogin'];

	if(in_array($order_type, $valid_trial_types)) {
	?>
	<div id="free-trial-notification">
		<span class="free-trial-head">TRIAL ACCESS</span>
		<p>
			Your institution,&nbsp;
			<span style="text-decoration: underline;">
				<?php echo $inst->name ?>
			</span>
			&nbsp;is currently using trial access.
			<br>
			<?php if($require_login && !$logged_in) { ?>
				Please create an account and let your librarian know about JoMI.
			<?php } else { ?>
				Please recommend JoMI to your librarian.
			<?php } ?>
		</p>
	</div>
	<?php }
}

// DISPLAY NOTIFICATION FOR NON-LOGGED IN USERS TO SIGN UP OR CONTACT LIBRARIAN

// ok so for now don't rely on the access table db to display this
// we're just going to assume that any non-logged in user is going to get blocked one way or another
// so we can start conversations and whatever

// if the user isn't logged in
if(!$logged_in) {
	// if an order does not exist or if the order has expired
	if(empty($user_order) || (!empty($user_order) && !$user_inst['is_subscribed'])) {
?>
<div class="sign-up-block">
	<?php
		# @COPY_SIGNIN
		# let users know that they have to sign in and let their librarian know to subscribe to jomi
	?>
	<span class="sign-up-head">JoMI is not a free resource.</span>
	<p>
		You may&nbsp;
		<a title='Register'
			href='<?php echo wp_registration_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")?>'>
			create an account
		</a>
		&nbsp;to gain access.
		<br />
		Please make a request to your librarian or&nbsp;
		<a href='mailto:lib@jomi.com' target='_blank'>
			send us an email.
		</a>
		&nbsp;to maintain access.
		<br />
		Or, please&nbsp;
		<a title='Sign In'
			href='<?php echo wp_login_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")?>'>
			sign in
		</a>
		&nbsp;if you are at a subscribed institution.
	</p>
</div>
<?php
	}
}

if(!empty($user_order) && $user_inst['is_subscribed'] && !in_array($user_order->type, $valid_trial_types)) {
	$date_end = $user_order->date_end;

	$year = substr($date_end, 0, 4);

	$month = substr($date_end, 5, 2);
	$month = date('F', mktime(0, 0, 0, $month, 10));

	$day = substr($date_end, 8, 2);
	$day = date('jS', mktime(0, 0, 0, 0, $day));

	if($user_order->require_login && !$logged_in) {
?>
<div class="subscribed-block">
	<?php
		# @COPY_REQUIRE_SIGN_IN
		# institution is subscribed but user still has to sign in
	?>
	<span class="subscribed-head">
		Subscribed
	</span>
	<p>
		Your institution,&nbsp;
		<span style="text-decoration: underline;">
			<?php echo $inst->name ?>
		</span>
		&nbsp;is subscribed.
		<br />
		Your subscription expires on&nbsp;
		<?php echo $month . ' ' . $day . ', ' . $year; ?>
		<br />
		Please&nbsp;
		<a title='Sign In'
			href='<?php echo wp_login_url("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")?>'>
			sign in
		</a>
		&nbsp;to access content
	</p>
</div>
<?php } else { ?>
<div class="subscribed-block">
	<?php
		# @COPY_SUBSCRIBED
		# instutiton is subscribed and user is signed in/ does not have to sign in
	?>
	<span class="subscribed-head">
		Subscribed
	</span>
	<p>
		Your institution,&nbsp;
		<span style="text-decoration: underline;">
			<?php echo $inst->name ?>
		</span>
		&nbsp;is subscribed.
		<br />
		Your subscription expires on&nbsp;
		<?php echo $month . ' ' . $day . ', ' . $year; ?>
	</p>
</div>
<?php } }



global $jomi_user_order;

if(!empty($jomi_user_order)) { ?>

	<div class="row stripe-notification">
		<div class="col-xs-12">
			<span class="stripe-subscribed">Subscribed</span>
		</div>
		<div class="col-xs-12">
			<span class="plan">Plan:</span>
			<span class="plan-details"><?php echo $jomi_user_order->type; ?></span>
		</div>
	</div>

<?php } elseif ($stripe_user_subscribed) { ?>

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
		<?php if(get_post_status() == 'publish') { ?>
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
		<?php if(get_post_status() == 'publish') { ?>
			<td><?php the_time('Y'); ?></td>
		<?php } else { ?>
			<td>Article Not Yet Published</td>
		<?php } ?>
	</tr>
	<tr>
		<td><strong>Issue</strong></td>
		<?php if(get_post_status() == 'publish') { ?>
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
