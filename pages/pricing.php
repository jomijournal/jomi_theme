<?php
/**
 * Template Name: Pricing
 */
?>

<?php 
//** FETCH PRICES

// get these vars from stripe
$prices = stripe_get_subscription_prices();

// set defaults
$student_monthly   = (empty($prices['student-monthly']))   ? 1000 : $prices['student-monthly'];
$student_annual    = (empty($prices['student-annual']))    ? 1000 : $prices['student-annual'];
$resident_monthly  = (empty($prices['resident-monthly']))  ? 1000 : $prices['resident-monthly'];
$resident_annual   = (empty($prices['resident-annual']))   ? 1000 : $prices['resident-annual'];
$attending_monthly = (empty($prices['attending-monthly'])) ? 1000 : $prices['attending-monthly'];
$attending_annual  = (empty($prices['attending-annual']))  ? 1000 : $prices['attending-annual'];


//** PROCESS COUPONS AND CODES

if($_GET['clearsession'] == 'true') {
	$_SESSION['coupons'] = null;
	$_SESSION['referral'] = null;
}

// collect session vars
$coupons = (empty($_SESSION['coupons'])) ? array() : $_SESSION['coupons'];
$referral = $_SESSION['referral'];

$code = $_POST['code'];

// parse user-entered code
if(!empty($code)) {

	// try a stripe coupon first
	$coupon = stripe_get_coupon($code);
	if(!empty($coupon)) {

		// check for invalid/expired coupon?
		
		array_push($coupons, $coupon);
	} else {
		// try a referral code
		$referral_obj = get_referral_object($code);
		if(!empty($referral_obj)) {
			// valid code
			$referral = $referral_obj;
		} else {
			// invalid code
		}
	}
}

// parse GET codes
if(!empty($_GET['referral'])) {
	$referral_obj = get_referral_object($_GET['referral']);
	if(!empty($referral_obj)) $referral = $referral_obj;
}

// redundancy check
if(!empty($referral)) {
	if($referral->user_id == get_current_user_id()) {
		// cant refer yerself
		$referral = null;
		/*echo "
		<script>
		alert('you cant refer yourself!');
		</script>
		";*/
	}
}

// default discounts
$discount_amount = 0;
$discount_percent = 1;

//** APPLY COUPONS TO GLOBALS

if(!empty($coupons)) {
	// remove dupes
	$coupons = array_unique($coupons);

	// modify global discounts
	foreach($coupons as $coupon) {
		if($coupon['amount_off'] > 0) $discount_amount += $coupon['amount_off'];
		elseif($coupon['percent_off'] > 0) $discount_percent *= (1 - ($coupon['percent_off'] / 100));
	}
}
if($referral->discount_amount > 0) $discount_amount += $referral->discount_amount;
elseif($referral->discount_percent > 0) $discount_percent *= (1 - ($referral->discount_percent / 100));


// save coupons and referral
$_SESSION['coupons'] = $coupons;
$_SESSION['referral'] = $referral;

// apply test variable
// dont do this in production
if(WP_ENV != "PROD") {
	if(!empty($_GET['testdiscountpercent'])) {
		$discount_percent = $_GET['testdiscountpercent'];
		$discount_code = "TESTDISCOUNTPERCENT";
	}
	if(!empty($_GET['testdiscountamount'])) {
		$discount_amount = $_GET['testdiscountamount'];
		$discount_code = "TESTDISCOUNTAMOUNT";
	}
}


$percent_off = (1 - $discount_percent) * 100;

if($discount_percent < 1 || $discount_amount > 0) $discounted = true;
else $discounted = false;

if($_GET['showdebug'] == true) {
	print_r_pre($coupons);
	print_r_pre($referral);
	print_r_pre($discount_percent);
	print_r_pre($discount_amount);
}

//** APPLY DISCOUNTS TO PRICES

if($discount_amount > 0) {
	$student_monthly   -= $discount_amount;
	$student_annual    -= ($discount_amount * 12);
	$resident_monthly  -= $discount_amount;
	$resident_annual   -= ($discount_amount * 12);
	$attending_monthly -= $discount_amount;
	$attending_annual  -= ($discount_amount * 12);
}
if($discount_percent < 1) {
	$student_monthly   *= $discount_percent;
	$student_annual    *= $discount_percent;
	$resident_monthly  *= $discount_percent;
	$resident_annual   *= $discount_percent;
	$attending_monthly *= $discount_percent;
	$attending_annual  *= $discount_percent;
}

if($student_monthly < 0) $student_monthly = 0;
if($student_annual < 0) $student_annual = 0;
if($resident_monthly < 0) $resident_monthly = 0;
if($resident_annual < 0) $resident_annual = 0;
if($attending_monthly < 0) $attending_monthly = 0;
if($attending_annual < 0) $attending_annual = 0;




// helper vars to display cents of price
$student_monthly_cents   = sprintf("%02d", $student_monthly   % 100);
$student_annual_cents    = sprintf("%02d", $student_annual    % 100);
$resident_monthly_cents  = sprintf("%02d", $resident_monthly  % 100);
$resident_annual_cents   = sprintf("%02d", $resident_annual   % 100);
$attending_monthly_cents = sprintf("%02d", $attending_monthly % 100);
$attending_annual_cents  = sprintf("%02d", $attending_annual  % 100);

// helper vars to display dollars of price
$student_monthly_dollars   = number_format(floor($student_monthly   / 100));
$student_annual_dollars    = number_format(floor($student_annual    / 100));
$resident_monthly_dollars  = number_format(floor($resident_monthly  / 100));
$resident_annual_dollars   = number_format(floor($resident_annual   / 100));
$attending_monthly_dollars = number_format(floor($attending_monthly / 100));
$attending_annual_dollars  = number_format(floor($attending_annual  / 100));

// verify if user is subscribed
global $stripe_user_subscribed;
stripe_verify_user_subscribed();

global $current_user;
get_currentuserinfo();

// default page
$action = $_GET['action'];
if(empty($action)) {

?>

<div class="modal fade" id="warning-modal" tabindex="-1" role="dialog" aria-labelledby="Warning" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Warning</h4>
			</div>
			<div class="modal-body">
				<p>Generic Warning</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="pricing">

	<input type="hidden" id="stripe-user-subscribed" value="<?php echo $stripe_user_subscribed; ?>">

	<div class="row">
		<div class="col-xs-12">
			<div class="pricing-notification" id="pricing-notification"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<h1>Subscribe to the World's Highest Quality Surgical Video Journal</h1>
		</div>
	</div>
	<div class="plans row">

		<div class="col-md-9 col-xs-12">

			<div class="row">

				<div class="student col-xs-12 col-sm-4">
					<div class="plan-header">
						<h2>Students</h2>
					</div>
					<div class="plan-body">
						<p class="desc">For inquisitive pre-medical and medical students</p>
						<p class="as-low-as"><?php if($discounted) { 
								echo "Normally";
							} else { 
								echo "As low as";
						} ?></p>
						<p class="price">$<?php if($discounted) { 
								echo sprintf("%01.2f", (($student_annual / $discount_percent) + $discount_amount) / 100);
							} else {
								echo ($student_annual_dollars . '.' . $student_annual_cents);
						} ?>/year</p>
					</div>
					<div class="plan-form">
						<p>
							<input id="student-monthly" type="radio" name="student" period="monthly" value="<?php echo $student_monthly; ?>">
							Monthly &nbsp;&nbsp;
							($<?php echo ($student_monthly_dollars . '.' . $student_monthly_cents) ?>/mo.)
						</p>
						<p>
							<input id="student-annually" type="radio" name="student" period="annual" value="<?php echo $student_annual; ?>" checked>
							Annually &nbsp;&nbsp;
							($<?php echo ($student_annual_dollars . '.' . $student_annual_cents) ?>/year)
						</p>
					</div>
					<div class="plan-cost">
						<p class="price">$<?php echo $student_annual_dollars; ?><sup class="cents">.<?php echo $student_annual_cents; ?></sup></p>
						<p><button class="subscribe-btn" id="student-sub">Subscribe</button></p>
					</div>
				</div>



				<div class="resident col-xs-12 col-sm-4">
					<div class="plan-header">
						<h2>Residents</h2>
					</div>
					<div class="plan-body">
						<p class="desc">For apprehensive medical and surgical residents</p>
						<p class="as-low-as"><?php if($discounted) {
								echo "Normally";
							} else {
								echo "As low as";
						} ?></p>
						<p class="price">$<?php if($discounted) {
								echo sprintf("%01.2f", (($resident_annual / $discount_percent) + $discount_amount) / 100);
							} else {
								echo ($resident_annual_dollars . '.' . $resident_annual_cents);
						} ?>/year</p>
					</div>
					<div class="plan-form">
						<p>
							<input id="resident-monthly" type="radio" name="resident" period="monthly" value="<?php echo $resident_monthly; ?>">
							Monthly &nbsp;&nbsp;
							($<?php echo ($resident_monthly_dollars . '.' . $resident_monthly_cents); ?>/mo.)
						</p>
						<p>
							<input id="resident-annually" type="radio" name="resident" period="annual" value="<?php echo $resident_annual; ?>" checked>
							Annually &nbsp;&nbsp;
							($<?php echo ($resident_annual_dollars . '.' . $resident_annual_cents); ?>/year)
						</p>
					</div>
					<div class="plan-cost">
						<p class="price">$<?php echo $resident_annual_dollars; ?><sup class="cents">.<?php echo $resident_annual_cents; ?></sup></p>
						<p><button class="subscribe-btn" id="resident-sub">Subscribe</button></p>
					</div>
				</div>



				<div class="attending col-xs-12 col-sm-4">
					<div class="plan-header">
						<h2>Attendings</h2>
					</div>
					<div class="plan-body">
						<p class="desc">For adaptive surgeons and attending physicians</p>
						<p class="as-low-as"><?php if($discounted) {
								echo "Normally";
							} else {
								echo "As low as";
						} ?></p>
						<p class="price">$<?php if($discounted) {
								echo sprintf("%01.2f", (($attending_annual / $discount_percent) + $discount_amount) / 100);
							} else {
								echo ($attending_annual_dollars . '.' . $attending_annual_cents);
						} ?>/year</p>
					</div>
					<div class="plan-form">
						<p>
							<input id="attending-monthly" type="radio" name="attending" period="monthly" value="<?php echo $attending_monthly; ?>">
							Monthly &nbsp;&nbsp;
							($<?php echo ($attending_monthly_dollars . '.' . $attending_monthly_cents); ?>/mo.)
						</p>
						<p>
							<input id="attending-annually" type="radio" name="attending" period="annual" value="<?php echo $attending_annual; ?>" checked>
							Annually &nbsp;&nbsp;
							($<?php echo ($attending_annual_dollars . '.' . $attending_annual_cents); ?>/year)
						</p>
					</div>
					<div class="plan-cost">
						<p class="price">$<?php echo $attending_annual_dollars; ?><sup class="cents">.<?php echo $attending_annual_cents; ?></sup></p>
						<p><button class="subscribe-btn" id="attending-sub">Subscribe</button></p>
					</div>
				</div>
			</div>

			<div class="row">
				<?php if($discounted) { ?>
				<div class="col-xs-6">
					<div class="coupon-display">
						
						Coupons used: 
						<?php foreach($coupons as $key=>$coupon) { ?>
							<strong> <?php echo $coupon['id']; ?></strong><?php if($key != count($coupons) - 1) echo ','; ?>
						<?php } ?>
						<strong><?php if(empty($coupons)) {echo "none"; } ?></strong>
						<br>
						<?php if(!empty($referral)) { ?>
						<?php $referred = get_user_by('id', $referral->user_id);
							$referred_email = $referred->user_email; ?>
						Referred from: <strong><?php echo $referred_email; ?></strong>
						<?php } ?>
					</div>
				</div>
				<div class "col-xs-6">
					<div class="coupon-display">
					<?php //if($discount_percent < 1) { ?>
					Percent off: <strong><?php echo $percent_off; ?>%</strong><br>
					<?php //} ?>
					<?php //if ($discount_amount > 0) { ?>
					Amount off: <strong>$<?php echo sprintf("%01.2f", ($discount_amount / 100)); ?></strong>
					<?php //} else echo '&nbsp;'?>
					</div>
				</div>
				<?php } ?>
				<div class="col-xs-12">
					<form class="coupon-container" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
						<!--Coupon / Referral Code:-->
						Coupon Code:&nbsp;
						<input type="text" class="coupon-input" id="coupon-input" name="code">
						<input type="submit" class="btn coupon-submit" value="Submit">
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-3 col-xs-12">
			<div class="row">
				<div class="institution col-xs-12 col-sm-12">
					<div class="plan-header">
						<h2>Institutions</h2>
					</div>
					<div class="plan-body">
						<p class="desc">For progressive medical and teaching institutions</p>
						<p class="as-low-as">Limited-Time</p>
						<p class="price">Early Adopter Pricing</p>
					</div>
					<div class="plan-form">
						<p>JoMI is used at prestigious medical schools and hospitals around the world</p>
						<p><a href="<?php echo site_url('/subscribers/'); ?>">See all subscribers →</a></p>
					</div>
					<div class="plan-cost">
						<p>JoMI was created to improve outcomes in healthcare and surgery.<br> If your institution does not have the resources or budget to subscribe at our normal rate, we will personally work with your institution to guarantee access to our content</p><br>
						<a class="contact-btn" href="mailto:lib@jomi.com">Contact Us</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://checkout.stripe.com/checkout.js"></script>
<script>

// turn off non-js api for modals
$(document).off('.modal.data-api');

var amount;
var name;
var desc;
var currency = 'USD';
var plan;
var discount = '<?php echo $discount_code?>';

$(function() {

	// init modal and hide at page load
	$('#warning-modal').modal({
		show: false
	});

	// load stripe handler
	var handler = StripeCheckout.configure({
		key: '<?php echo get_option("stripe_test_public_api_key"); ?>'
		, image: '/wp-content/themes/jomi/assets/img/enso_transparent.png'
		, email: '<?php echo $current_user->user_email; ?>'
		, token: function(token) {
			// Use the token to create the charge with a server-side script.
			// You can access the token ID with `token.id`
			stripe_charge(token);
		}
	});

	$('.subscribe-btn').on('click', function(e) {

		e.preventDefault();

		// logged out
		if($('#login-btn').is(':visible')) {
			$('.modal-title').html("Log In Request");
			$('.modal-body p').html("Please log in to JoMI before subscribing.<br>Logging in allows us to store your subscription information and give you access to our content.");
			$('#warning-modal').modal('show');
			return;
		}

		// already subscribed
		if($('#stripe-user-subscribed').attr('value') == 1 ||
			$('#stripe-user-subscribed').attr('value') == true) {
			$('.modal-title').html("Warning");
			$('.modal-body p').html("You're already subscribed to JoMI!<br>If you're having trouble accessing our content, please email us at <a href='mailto:contact@jomi.com'>contact@jomi.com</a>");
			$('#warning-modal').modal('show');
			return;
		}

		// grab price
		var price = $(this).parent().parent().parent().find('input[type="radio"]:checked');
		var price_amount = price.attr('value');

		// default values
		name = 'JoMI';
		desc = 'Default Subscription';
		amount = 99;

		// amount update
		if(this.id == 'student-sub') {
			desc = 'Student Subscription';
			amount = price_amount;
			plan = 'student-';
		} else if (this.id == 'resident-sub') {
			desc = 'Resident Subscription';
			amount = price_amount;
			plan = 'resident-';
		} else if (this.id == 'attending-sub') {
			desc = 'Attending Subscription';
			amount = price_amount;
			plan = 'attending-';
		}

		// description update
		if(price.attr('period') == 'monthly') {
			desc += ' - Monthly';
			plan += 'monthly';
		} else if(price.attr('period') == 'annual') {
			desc += ' - Annual';
			plan += 'annual';
		}

		// open checkout
		handler.open({
			name: name
			, description: desc
			, amount: amount
		});
		
	});

	// Close Checkout on page navigation
	window.addEventListener('popstate', function() {
		handler.close();
	});


	$('input[type="radio"]').click(function(e) {
		var value = $(this).attr('value');

		var price_display = $(this).parent().parent().parent().find('.plan-cost .price');

		var type = $(this).attr('name');
		var period = $(this).attr('period');

		var dollars;
		var cents;

		if(type == 'student') {
			if(period == 'monthly') {
				dollars = '<?php echo $student_monthly_dollars; ?>';
				cents = '<?php echo $student_monthly_cents; ?>';
			} else {
				dollars = '<?php echo $student_annual_dollars; ?>';
				cents = '<?php echo $student_annual_cents; ?>';
			} 
		} else if (type == 'resident') {
			if(period == 'monthly') {
				dollars = '<?php echo $resident_monthly_dollars; ?>';
				cents = '<?php echo $resident_monthly_cents; ?>';
			} else {
				dollars = '<?php echo $resident_annual_dollars; ?>';
				cents = '<?php echo $resident_annual_cents; ?>';
			} 
		} else if (type == 'attending') {
			if(period == 'monthly') {
				dollars = '<?php echo $attending_monthly_dollars; ?>';
				cents = '<?php echo $attending_monthly_cents; ?>';
			} else {
				dollars = '<?php echo $attending_annual_dollars; ?>';
				cents = '<?php echo $attending_annual_cents; ?>';
			} 
		}
		price_display.html("$" + dollars + "<sup class='cents'>." + cents + "</sup>");
	});
});

function stripe_charge(token) {

	console.log(token);

	if(token.bank_account != null) {
		/*$.post(MyAjax.ajaxurl, {
			action: 'stripe-charge'

			, amount: amount
			, name: name
			, desc: desc
			, currency: currency
			, plan: plan
			, discount: discount

			, id:       (token.id       == null) ? null : token.id
			, object:   (token.object   == null) ? null : token.object
			, livemode: (token.livemode == null) ? null : token.livemode
			, created:  (token.created  == null) ? null : token.created
			, type:     (token.type     == null) ? null : token.type
			, used:     (token.used     == null) ? null : token.used
			, email:    (token.email    == null) ? null : token.email

		}, function(response) {
			console.log(response);
		});*/
	} else {
		$.post(MyAjax.ajaxurl, {
			action: 'stripe-charge'

			, amount: amount
			, name: name
			, desc: desc
			, currency: currency
			, plan: plan
			, discount: discount

			, id:       (token.id       == null) ? null : token.id
			, object:   (token.object   == null) ? null : token.object
			, livemode: (token.livemode == null) ? null : token.livemode
			, created:  (token.created  == null) ? null : token.created
			, type:     (token.type     == null) ? null : token.type
			, used:     (token.used     == null) ? null : token.used
			, email:    (token.email    == null) ? null : token.email

		}, function(response) {
			console.log(response);

			if(response == "success" || response == "success0") window.location.href = "<?php echo site_url('/pricing/?action=orderplaced'); ?>";
			else window.location.href = "<?php echo site_url('/pricing/?action=ordererror'); ?>";
		});
	}
}

</script>

<?php } elseif($action == 'orderplaced') { ?>

<?php 

$user_id = get_current_user_id();

$referred_by = $_SESSION['referral'];

if($user_id == 0) {
	// not logged in
	echo "It looks like you're lost...<br>";
	echo "Click <a href='" . site_url('/') . "'>here</a> to go back to the home page";
	exit();
}

$user = get_user_by('id', $user_id);

$referral = has_referral($user_id);
if(!$referral) {
	// generate code
	$code = hash('crc32', $user->user_email);

	//echo 'created ' . $code;

	$_POST['user_id'] = $user_id;
	$_POST['refer_code'] = $code;
	$_POST['referred_by'] = $referred_by->user_id;
	$_POST['num_referrals'] = 0;
	$_POST['discount_amount'] = 2000;
	$_POST['discount_percent'] = 1;

	insert_referral();

} else {
	// show code
	$code = $referral->refer_code;
	//echo 'fetched: ' . $code;
}

?>

<?php $referral_credit = 15; ?>

<div class="pricing orderplaced">

	<div class="row">
		<div class="col-xs-12">
			<h1>Thank You for Subscribing to JoMI!</h1>
			<hr>
		</div>
		<div class="col-xs-12">
			<h2>You're now helping us produce the latest and greatest surgical procedures and educational videos</h2>
			<br>
			<!--hr>
			<h3>Give $<?php echo $referral_credit; ?> Get $<?php echo $referral_credit; ?></h3-->
		</div>
	</div>

	<!--div class="row refercode">
		<div class="col-xs-4">
			<p class="text">Your Referral Code:</p>
		</div>
		<div class="col-xs-8">
			<input class="referbox" type="text" readonly value="<?php echo $code; ?>">
			&nbsp;&nbsp;Or&nbsp;&nbsp;
			<a class="referlink" target="_blank" href="<?php echo site_url('/pricing?referral=') . $code;?>"><?php echo site_url('/pricing?referral=') . $code;?></a>
		</div>
	</div>
	
	<div class="row referinfo">
		<div class="col-xs-12">
			<p>Give a friend $<?php echo $referral_credit; ?> off their first payment, and get $<?php echo $referral_credit; ?> in credit yourself.</p>
			<p>Credit earned through referrals can count towards future payments, or immediately be cashed out.</p>
			<p>That's right, you can <b>earn money</b> by spreading the word about JoMI</p>
			<br>
		</div>
	</div-->

	<div class="row">
		<div class="col-sm-4 col-xs-12">
			<!--a href="<?php echo site_url('/profile/'); ?>" class="link-block">Edit Your Profile</a-->
			<a href="#" data-toggle="tooltip" data-placement="top" title="Feature Coming Soon" class="link-block">Edit Your Profile</a>
		</div>
		<div class="col-sm-4 col-xs-12">
			<a href="<?php echo site_url('/index/'); ?>" class="link-block">Check out the Index</a>
		</div>
		<div class="col-sm-4 col-xs-12">
			<a href="<?php echo site_url('/articles/'); ?>" class="link-block">Watch Some Videos</a>
		</div>
	</div>

	

</div>
<?php } elseif($action = 'ordererror') { ?>

<div class="pricing ordererror">

	<div class="row">
		<div class="col-xs-12">
			<h1>Uh Oh! Something went wrong...</h1>
			<hr>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6">
			<strong>This could be a result of:</strong>
			<ul>
				<li>A connection interruption</li>
				<li>Incorrect billing information</li>
				<li>An error on JoMI's end</li>
			</ul>
			<strong>Please email us at <a href="mailto:contact@jomi.com">contact@jomi.com</a> or <a href="<?php echo site_url('/contact/'); ?>">contact us</a> if the problem persists. Thank you for your patience!</strong>
		</div>
		<div class="col-xs-6">
			<img src="https://i.imgur.com/swKtO.png">
		</div>
	</div>

</div>

<?php } ?>
