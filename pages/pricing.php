<?php
/**
 * Template Name: Pricing
 */
?>

<?php 

global $user_stripe_subscribed;

verify_user_stripe_subscribed();

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

	<input type="hidden" id="user-stripe-subscribed" value="<?php echo $user_stripe_subscribed; ?>">

	<?php //if(!is_user_logged_in()) { ?>
	<div class="row">
		<div class="col-xs-12">
			<div href="#login" class="login-notification">
				Please log in before subscribing to JoMI
			</div>
		</div>
	</div>
	<?php //} ?>
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



		<div class="student col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Students</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For inquisitive pre-medical and medical students</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$99/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="student-monthly" type="radio" name="student-option" period="monthly" value="1000">
					Monthly &nbsp;&nbsp;
					($10.00/mo.)
				</p>
				<p>
					<input id="student-annually" type="radio" name="student-option" period="annually" value="9900" checked>
					Annually &nbsp;&nbsp;
					($8.25/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$99<sup class="cents">.00</sup></p>
				<p><button class="subscribe-btn" id="student-sub">Subscribe</button></p>
			</div>
		</div>



		<div class="resident col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Residents</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For apprehensive medical and surgical residents</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$999/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="resident-monthly" type="radio" name="resident-option" period="monthly" value="10000">
					Monthly &nbsp;&nbsp;
					($100.00/mo.)
				</p>
				<p>
					<input id="resident-annually" type="radio" name="resident-option" period="annually" value="99900" checked>
					Annually &nbsp;&nbsp;
					($83.25/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$999<sup class="cents">.00</sup></p>
				<p><button class="subscribe-btn" id="resident-sub">Subscribe</button></p>
			</div>
		</div>



		<div class="attending col-xs-12 col-sm-4 col-md-3">
			<div class="plan-header">
				<h2>Attendings</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For adaptive surgeons and attending physicians</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$1998/year</p>
			</div>
			<div class="plan-form">
				<p>
					<input id="attending-monthly" type="radio" name="attending-option" period="monthly" value="20000">
					Monthly &nbsp;&nbsp;
					($200.00/mo.)
				</p>
				<p>
					<input id="attending-annually" type="radio" name="attending-option" period="annually" value="199800" checked>
					Annually &nbsp;&nbsp;
					($166.50/mo.)
				</p>
			</div>
			<div class="plan-cost">
				<p class="price">$1998<sup class="cents">.00</sup></p>
				<p><button class="subscribe-btn" id="attending-sub">Subscribe</button></p>
			</div>
		</div>


		<div class="institution col-xs-12 col-sm-12 col-md-3">
			<div class="plan-header">
				<h2>Institutions</h2>
			</div>
			<div class="plan-body">
				<p class="desc">For progressive medical and teaching institutions</p>
				<p class="as-low-as">As low as</p>
				<p class="price">$100/video/year</p>
			</div>
			<div class="plan-form">
				<p>Contact lib@jomi.com pls</p>
				<p>&nbsp;</p>
			</div>
			<div class="plan-cost">
				<p>&nbsp;</p>
				<p>&nbsp;</p>
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

$(function() {

	// init modal and hide at page load
	$('#warning-modal').modal({
		show: false
	});

	// load stripe handler
	var handler = StripeCheckout.configure({
		key: '<?php echo get_option("stripe_test_public_api_key"); ?>'
		, image: '/wp-content/themes/jomi/assets/img/enso_transparent.png'
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
			$('.modal-title').html("Login Request");
			$('.modal-body p').html("Please login to JoMI before subscribing.");
			$('#warning-modal').modal('show');
			return;
		}

		if($('#user-stripe-subscribed').attr('value') == 1 ||
			$('#user-stripe-subscribed').attr('value') == true) {
			$('.modal-title').html("Warning");
			$('.modal-body p').html("You're already subscribed to JoMI!<br>If you're having trouble accessing our content, please email us at <a href='mailto:contact@jomi.com'>contact@jomi.com</a>");
			$('#warning-modal').modal('show');
			return;
		}

		var price = $(this).parent().parent().parent().find('input[type="radio"]:checked');
		var price_amount = price.attr('value');
		//price *= 12;

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
		} else if(price.attr('period') == 'annually') {
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

		//var price = (value/100) * 12;
		price = value / 100;
		//price.toFixed(3);
		//price.toFixed(2);
		// taken from http://stackoverflow.com/questions/149055/how-can-i-format-numbers-as-money-in-javascript
		//price = price.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

		var price_text = '$' + price;

		price_display.html(price_text + "<sup class='cents'>.00</sup>");
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
<?php } ?>
