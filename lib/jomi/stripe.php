<?php
/**
 * STRIPE HELPER FUNCS
 */

function get_cust_id($user_id) {
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

	if(empty($cust_id)) return false;

	return $cust_id;
}



function stripe_charge() {

	// collect POST vars
	$amount = $_POST['amount'];
	$currency = $_POST['currency'];
	$token_id = $_POST['id'];
	$desc = $_POST['desc'];
	$email = $_POST['email'];
	$plan = $_POST['plan'];

	$user_id = get_current_user_id();

	$cust_id = get_cust_id($user_id);

	// user does not have a stripe customer ID associated with it
	// create a new customer and link it to the wordpress user
	if(empty($cust_id)) {

		// create customer
		try{
			$customer = Stripe_Customer::create( array(
				'email' => $email
				, 'card'  => $token_id
			));

			$cust_id = $customer['id'];

			update_user_meta($user_id, 'stripe_cust_id', $cust_id);

		} catch(Stripe_Error $e) {
			print_r($e);
			return;
		}
	}

	try {
		$customer->subscriptions->create(
			array (
				'plan' => $plan
		));
	} catch(Stripe_Error $e) {
		print_r($e);
		return;
	}

	//print_r($customer);

	// Create the charge on Stripe's servers - this will charge the user's default card
	try {
		$charge = Stripe_Charge::create( array(
				'amount'      => $amount // amount in cents, again
				, 'currency'    => $currency
				//, 'card'        => $token_id
				, 'customer'    => $cust_id
				, 'description' => $description
			)
		);

	} catch(Stripe_CardError $e) {
		print_r($e);
	}
	
}
add_action( 'wp_ajax_nopriv_stripe-charge', 'stripe_charge' );
add_action( 'wp_ajax_stripe-charge', 'stripe_charge' );


function verify_user_stripe_subscribed() {
	$user_id = get_current_user_id();

	$cust_id = get_cust_id($user_id);

	if(!empty($cust_id)) {
		try {
			$customer = Stripe_Customer::retrieve($cust_id);

			if($access_debug) {
				echo "Stripe Customer:\n";
				print_r($customer);
			}

		} catch(Stripe_Error $e) {
			return;
		}
	} else return false;

	// get subscription object
	$subscriptions = $customer['subscriptions'];
	$sub_total_count = $subscriptions['total_count'];
	$sub_objects = $subscriptions['data'];

	$subscribed = false;

	foreach($sub_objects as $sub) {
		$status = $sub['status'];
		if(in_array($status, array("trialing","active","past_due","unpaid"))) {
			$subscribed = true;
		}
	}
	
	// set global flag
	global $user_stripe_subscribed;
	$user_stripe_subscribed = $subscribed;

	return $user_stripe_subscribed;
}

add_action('init', 'verify_user_stripe_subscribed');

?>