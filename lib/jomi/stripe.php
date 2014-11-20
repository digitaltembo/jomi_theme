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
	$discount = (empty($_POST['discount'])) ? '' : $_POST['discount'];

	$user_id = get_current_user_id();

	if(empty($user_id)) {
		echo "user not logged in";
		return;
	}

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
			//print_r($e);
			echo "trouble creating user";
			return;
		}
	} else {
		try {
			$customer = Stripe_Customer::retrieve($cust_id);
		} catch (Stripe_Error $e) {
			echo "trouble retreiving user";
			return;
		}
	}

	try {
		$customer->subscriptions->create(
			array (
				'plan' => $plan
				, 'coupon' => $discount
			)
		);

		wp_mail($email, 'JoMI Subscription', 'Thanks for subscribing to JoMI! <br>Let us know if you have any questions.');

		$admin_email = get_option('admin_email');

		wp_mail($admin_email, $email . ' Subscribed to JoMI!', 'plan: ' . $plan . '<br>coupon: ' . $discount . '<br>');

		echo "success";
		return;

	} catch(Stripe_Error $e) {
		//print_r($e);
		echo "trouble creating subscription";
		return;
	}

	//print_r($customer);

	// Create the charge on Stripe's servers - this will charge the user's default card
	/*try {
		$charge = Stripe_Charge::create( array(
				'amount'      => $amount // amount in cents, again
				, 'currency'    => $currency
				//, 'card'        => $token_id
				, 'customer'    => $cust_id
				, 'description' => $description
			)
		);

		//wp_redirect(site_url('/pricing/?action=orderplaced'));
		echo "success";

	} catch(Stripe_CardError $e) {
		//print_r($e);
		//wp_redirect(site_url('/pricing/?action=ordererror'));
		echo "error";
	}*/
	
}
add_action( 'wp_ajax_nopriv_stripe-charge', 'stripe_charge' );
add_action( 'wp_ajax_stripe-charge', 'stripe_charge' );


function verify_user_stripe_subscribed() {

	global $access_debug;

	$user_id = get_current_user_id();

	$cust_id = get_cust_id($user_id);

	if(!empty($cust_id)) {
		try {
			$customer = Stripe_Customer::retrieve($cust_id);

			if($access_debug && is_single()) {
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

	if($sub_total_count < 1) {
		return false;
	}

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

//add_action('init', 'verify_user_stripe_subscribed');

/**
 * get the percentage discount based on the coupon id
 * @param  string $coupon_id [description]
 * @return [type]            [description]
 */
function stripe_get_coupon_discount($coupon_id = "") {

	$id = $coupon_id;

	// prioritize POST over func. argument
	if(!empty($_POST['coupon_id'])) $id = $_POST['coupon_id']; 

	if(empty($id)) return 1;

	try {
		$coupon = Stripe_Coupon::retrieve($id);
	} catch (Stripe_Error $e) {
		//print_r($e);
		return 1;
	}

	$discount = $coupon['percent_off'];
	$discount /= 100;

	return $discount;
}

function stripe_get_subscription_prices() {

	try {
		$plans = Stripe_Plan::all();
	} catch (Stripe_Error $e) {
		return array();
	}

	$prices = array();

	$plans = $plans['data'];

	foreach($plans as $plan) {
		$id = $plan['id'];
		$amount = $plan['amount'];
		$prices[$id] = $amount;
	}

	return $prices;
}

?>