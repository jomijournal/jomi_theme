<?php
/**
 * STRIPE HELPER FUNCS
 */

/**
 * get stripe customer ID from user meta
 * @param  [type] $user_id [description]
 * @return [type]          [description]
 */
function stripe_get_cust_id($user_id) {
	// not logged in. dont go through with purchase.
	if($user_id == 0) {
		return false;
	}

	$user = get_user_by('id',$user_id);

	// get user failed? dont go through with purchase
	if($user == false) {
		return false;
	}

	$cust_id = get_user_meta($user_id, 'stripe_cust_id', true);

	// in case it doesnt exist
	if(empty($cust_id)) return false;

	return $cust_id;
}

/**
 * called when a user subscribes (via ajax)
 * create a customer (if needed) and a subscription
 * also takes care of some logistical stuff (ie, email)
 * @return [type] [description]
 */
function stripe_charge() {

	// collect POST vars
	$amount = $_POST['amount'];
	$currency = $_POST['currency'];
	$token_id = $_POST['id'];
	$desc = $_POST['desc'];
	$email = $_POST['email'];
	$plan = $_POST['plan'];
	$discount = (empty($_POST['discount'])) ? '' : $_POST['discount'];

	// get the user id from wordpress
	$user_id = get_current_user_id();

	// just in case:
	// this shouldn't happen, as the pricing page should stop this function from being called
	// if the user isn't logged in.
	if(empty($user_id)) {
		echo "user not logged in";
		return;
	}

	// get the stripe customer id
	$cust_id = stripe_get_cust_id($user_id);

	// user does not have a stripe customer ID associated with it
	// create a new customer and link it to the wordpress user
	if(empty($cust_id)) {

		// create customer
		try{
			$customer = Stripe_Customer::create( array(
				'email' => $email
				, 'card'  => $token_id
			));
		} catch(Stripe_Error $e) {
			//print_r($e);
			echo "trouble creating user";
			return;
		}

		// update user meta
		$cust_id = $customer['id'];
		update_user_meta($user_id, 'stripe_cust_id', $cust_id);

	} else {
		// retrieve customer
		try {
			$customer = Stripe_Customer::retrieve($cust_id);
		} catch (Stripe_Error $e) {
			echo "trouble retreiving user";
			return;
		}

		if($customer['id'] != $cust_id) {
			// mismatch... lets create a new user
			try{
				$customer = Stripe_Customer::create( array(
					'email' => $email
					, 'card'  => $token_id
				));
			} catch(Stripe_Error $e) {
				//print_r($e);
				echo "trouble creating user";
				return;
			}

			// update user meta
			$cust_id = $customer['id'];
			update_user_meta($user_id, 'stripe_cust_id', $cust_id);
		}
	}

	if($customer['deleted'] == 1) {
		try{
			$customer = Stripe_Customer::create( array(
				'email' => $email
				, 'card'  => $token_id
			));
		} catch(Stripe_Error $e) {
			//print_r($e);
			echo "trouble creating user";
			return;
		}
		
		// update user meta
		$cust_id = $customer['id'];
		update_user_meta($user_id, 'stripe_cust_id', $cust_id);
	}

	// create a subscription
	try {
		if(!empty($discount)) {
			// with a discount
			$customer->subscriptions->create(
				array (
					'plan' => $plan
					, 'coupon' => $discount
				)
			);
		} else {
			// without a discount
			$customer->subscriptions->create(
				array (
					'plan' => $plan
				)
			);
		}
		echo "success";

	} catch(Stripe_Error $e) {
		//print_r($e);
		echo "trouble creating subscription";
		return;
	}

	// send confirmation emails
	wp_mail($email, 'JoMI Subscription', 'Thanks for subscribing to JoMI! <br>Let us know if you have any questions.');
	$admin_email = get_option('admin_email');
	wp_mail($admin_email, $email . ' Subscribed to JoMI!', 'plan: ' . $plan . '<br>coupon: ' . $discount . '<br>');
	
}
add_action( 'wp_ajax_nopriv_stripe-charge', 'stripe_charge' );
add_action( 'wp_ajax_stripe-charge', 'stripe_charge' );


/**
 * verifies if a user is subscribed on stripe
 * also loads a bunch of useful globals into the system
 * @return [type] [description]
 */
function stripe_verify_user_subscribed() {

	global $access_debug;

	// load customer ID from WP user
	$user_id = get_current_user_id();
	$cust_id = stripe_get_cust_id($user_id);

	// load customer object
	if(!empty($cust_id)) {
		try {
			$customer = Stripe_Customer::retrieve($cust_id);
		} catch(Stripe_Error $e) {
			return;
		}
	} else return false;

	// debug
	if($access_debug && is_single()) {
		echo "Stripe Customer:\n";
		print_r($customer);
	}

	// load stripe user into global space
	global $stripe_user;
	$stripe_user = $customer;

	// get subscription object
	$subscriptions = $customer['subscriptions'];
	$sub_total_count = $subscriptions['total_count'];

	// if no subs exist, they cant be subscribed
	if($sub_total_count < 1) {
		return false;
	}

	// default
	$subscribed = false;

	// flip through sub objects
	$sub_objects = $subscriptions['data'];
	foreach($sub_objects as $sub) {
		$status = $sub['status'];
		if(in_array($status, array("trialing","active","past_due","unpaid"))) {

			$subscribed = true;

			// load subscription into global space
			global $stripe_user_active_sub;
			$stripe_user_active_sub = $sub;
		}
	}
	
	// set global flag
	global $stripe_user_subscribed;
	$stripe_user_subscribed = $subscribed;

	return $stripe_user_subscribed;
}

//add_action('init', 'verify_user_stripe_subscribed');

/**
 * fetch coupon based on its id
 * @param  string $coupon_id [description]
 * @return [type]            [description]
 */
function stripe_get_coupon($coupon_id = "") {

	$id = $coupon_id;

	// prioritize POST over func. argument
	if(!empty($_POST['coupon_id'])) $id = $_POST['coupon_id']; 

	if(empty($id)) return 1;

	// get the coupon
	try {
		$coupon = Stripe_Coupon::retrieve($id);
	} catch (Stripe_Error $e) {
		//print_r($e);
		return null;
	}

	// apply values
	//$discount = $coupon['percent_off'];
	//$discount /= 100;

	//return $discount;
	
	return $coupon;
}

/**
 * get the prices of each subscription
 * used in the generation of the pricing page
 * @return [type] [description]
 */
function stripe_get_subscription_prices() {

	// get the plans
	try {
		$plans = Stripe_Plan::all();
	} catch (Stripe_Error $e) {
		return array();
	}

	$prices = array();

	// load into array
	$plans = $plans['data'];
	foreach($plans as $plan) {
		$id = $plan['id'];
		$amount = $plan['amount'];
		$prices[$id] = $amount;
	}

	return $prices;
}

?>