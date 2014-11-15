<?php
/**
 * STRIPE HELPER FUNCS
 */


function stripe_charge() {

	// collect POST vars
	$amount = $_POST['amount'];
	$currency = $_POST['currency'];
	$token_id = $_POST['id'];
	$desc = $_POST['desc'];
	$email = $_POST['email'];
	$plan = $_POST['plan'];

	


	// create customer
	try{
		$customer = Stripe_Customer::create( array(
			'email' => $email
			, 'card'  => $token_id
		));
	} catch(Stripe_Error $e) {
		print_r($e);
		return;
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

	print_r($customer);

	//$subscriptions = $customer['subscriptions'];

	

	// Create the charge on Stripe's servers - this will charge the user's default card
	try {
		$charge = Stripe_Charge::create( array(
				'amount'      => $amount // amount in cents, again
				, 'currency'    => $currency
				//, 'card'        => $token_id
				, 'customer'    => $customer['id']
				, 'description' => $description
			)
		);

		//echo "SUCCESS";
		//print_r($charge);

	} catch(Stripe_CardError $e) {
		//echo "NOPE";
		print_r($e);
	}
	
}
add_action( 'wp_ajax_nopriv_stripe-charge', 'stripe_charge' );
add_action( 'wp_ajax_stripe-charge', 'stripe_charge' );

?>