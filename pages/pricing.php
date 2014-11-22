<?php
/**
 * Template Name: Pricing
 */
?>

<?php 

// get these vars from stripe
$prices = stripe_get_subscription_prices();

// set defaults
$student_monthly   = (empty($prices['student-monthly']))   ? 1000 : $prices['student-monthly'];
$student_annual    = (empty($prices['student-annual']))    ? 1000 : $prices['student-annual'];
$resident_monthly  = (empty($prices['resident-monthly']))  ? 1000 : $prices['resident-monthly'];
$resident_annual   = (empty($prices['resident-annual']))   ? 1000 : $prices['resident-annual'];
$attending_monthly = (empty($prices['attending-monthly'])) ? 1000 : $prices['attending-monthly'];
$attending_annual  = (empty($prices['attending-annual']))  ? 1000 : $prices['attending-annual'];

// get discount code
$discount_code = $_POST['discount_code'];

// process discount code and percent off
if(!empty($discount_code)) {
	$discount = stripe_get_coupon_discount($discount_code);

	if($discount != 1) {
		$percent_off = $discount * 100;
		$discount = 1 - $discount;
	} else {
		$discount = 1;
		$percent_off = 0;
	}
} else {
	$discount_code = '';
	$discount = 1;
}

// apply test variable
// dont do this in production
if(WP_ENV != "PROD") {
	if(!empty($_GET['testdiscount'])) {
		$discount = $_GET['testdiscount'];
		$percent_off = (1 - $discount) * 100;
		$discount_code = "COUPONFROMGET";
	}
}

// apply discount to prices
if($discount < 1) {
	$student_monthly   *= $discount;
	$student_annual    *= $discount;
	$resident_monthly  *= $discount;
	$resident_annual   *= $discount;
	$attending_monthly *= $discount;
	$attending_annual  *= $discount;
}

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
						<p class="as-low-as">As low as</p>
						<p class="price">$<?php echo ($student_annual_dollars . '.' . $student_annual_cents) ?>/year</p>
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
						<p class="as-low-as">As low as</p>
						<p class="price">$<?php echo ($resident_annual_dollars . '.' . $resident_annual_cents); ?>/year</p>
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
						<p class="as-low-as">As low as</p>
						<p class="price">$<?php echo ($attending_annual_dollars . '.' . $attending_annual_cents); ?>/year</p>
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
				<?php if(!empty($discount_code)) { ?>
				<div class="col-xs-12">
					<div class="coupon-display">

						<?php if ($discount < 1) { ?>
						Coupon used: <span class="coupon-code"><?php echo $discount_code; ?></span><br>
						Percent Off: <?php echo $percent_off; ?>%
						<?php } else { ?>
						Invalid Coupon: <span class="coupon-code"><?php echo $discount_code; ?></span><br>
						No Discount Applied
						<?php } ?>
					</div>
				</div>
				<?php } ?>
				<div class="col-xs-12">
					<form class="coupon-container" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
						Coupon Code:
						<input type="text" class="coupon-input" id="coupon-input" name="discount_code">
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
						<p><a href="<?php echo site_url('/subscribers/'); ?>">See all subscribers</a></p>
					</div>
					<div class="plan-cost">
						<p>JoMI was created to improve outcomes in healthcare and surgery.<br> If your institution does not have the resources or budget to subscribe at our normal rate, we will personally work with your institution to guarantee access to our content</p>
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

			, bank_id:                   (token.bank_account['id'] == null)                   ? null : token.bank_account['id']
			, bank_object:               (token.bank_account['object'] == null)               ? null : token.bank_account['object']
			, bank_country:              (token.bank_account['country'] == null)              ? null : token.bank_account['country']
			, bank_currency:             (token.bank_account['currency'] == null)             ? null : token.bank_account['currency']
			, bank_default_for_currency: (token.bank_account['default_for_currency'] == null) ? null : token.bank_account['default_for_currency']
			, bank_last4:                (token.bank_account['last4'] == null)                ? null : token.bank_account['last4']
			, bank_status:               (token.bank_account['status'] == null)               ? null : token.bank_account['status']
			, bank_name:                 (token.bank_account['bank_name'] == null)            ? null : token.bank_account['bank_name']
			, bank_fingerprint:          (token.bank_account['fingerprint'] == null)          ? null : token.bank_account['fingerprint']

		}, function(response) {
			console.log(response);
		});
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

			, card_id:              (token.card['id'] == null)              ? null : token.card['id']
			, card_object:          (token.card['object'] == null)          ? null : token.card['object']
			, card_brand:           (token.card['brand'] == null)           ? null : token.card['brand']
			, card_exp_month:       (token.card['exp_month'] == null)       ? null : token.card['exp_month']
			, card_exp_year:        (token.card['exp_year'] == null)        ? null : token.card['exp_year']
			, card_fingerprint:     (token.card['fingerprint'] == null)     ? null : token.card['fingerprint']
			, card_funding:         (token.card['funding'] == null)         ? null : token.card['funding']
			, card_last4:           (token.card['last4'] == null)           ? null : token.card['last4']
			, card_address_city:    (token.card['address_city'] == null)    ? null : token.card['address_city']
			, card_address_country: (token.card['address_country'] == null) ? null : token.card['address_country']
			, card_address_line1:   (token.card['address_line1'] == null)   ? null : token.card['address_line1']
			, card_address_line2:   (token.card['address_line2'] == null)   ? null : token.card['address_line2']
			, card_address_state:   (token.card['address_state'] == null)   ? null : token.card['address_state']
			, card_address_zip:     (token.card['address_zip'] == null)     ? null : token.card['address_zip']
			, card_country:         (token.card['country'] == null)         ? null : token.card['country']
			, card_customer:        (token.card['customer'] == null)        ? null : token.card['customer']
			, card_dynamic_last4:   (token.card['dynamic_last4'] == null)   ? null : token.card['dynamic_last4']
			, card_name:            (token.card['name'] == null)            ? null : token.card['name']

		}, function(response) {
			console.log(response);

			if(response == "success" || response == "success0") window.location.href = "<?php echo site_url('/pricing/?action=orderplaced'); ?>";
			else window.location.href = "<?php echo site_url('/pricing/?action=ordererror'); ?>";
		});
	}
}

</script>

<?php } elseif($action == 'orderplaced') { ?>


<div class="pricing orderplaced">

	<div class="row">
		<div class="col-xs-12">
			<h1>Thank You for Subscribing to JoMI!</h1>
			<hr>
		</div>
		<div class="col-xs-12">
			<h2>You're now helping us produce the latest and greatest surgical procedures and educational videos</h2>
			<br>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-4 col-xs-12">
			<a href="<?php echo site_url('/profile/'); ?>" class="link-block">Edit Your Profile</a>
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
