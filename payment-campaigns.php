<?php
/* function for ajax payment through pesapal for campaigns */

add_action('wp_ajax_listingpro_pesapal_pay_campaigns', 'listingpro_pesapal_pay_campaigns');
add_action('wp_ajax_nopriv_listingpro_pesapal_pay_campaigns', 'listingpro_pesapal_pay_campaigns');

if (!function_exists('listingpro_pesapal_pay_campaigns')) {
	function listingpro_pesapal_pay_campaigns() {
		require_once("lib/Pesapal.php");
		global $listingpro_options;
		$file = __FILE__;
        $logger = new Pesapal_Logger();
        $logger->add('Starting PesaPal Checkout', $file);
        $logger->add('GET Variables: ' . $_GET, $file);
        $logger->add('POST Variables: ' . $_POST, $file);
        $logger->add('SESSION Variables: ' . $_SESSION, $file);

		echo('<br />GET: ');
		print_r($_GET);
		echo('<br /><br />GET: ');
		var_dump($_GET);
		echo('<br /><br />POST: ');
		print_r($_POST);
		echo('<br /><br />POST: ');
		var_dump($_POST);
		echo('<br /><br />SESSION: ');
		print_r($_SESSION);
		echo('<br /><br />SESSION: ');
		var_dump($_SESSION);

        $secid = '';
		$selid = '';
		$token = $_POST['token'];
		$tprice = $_POST['tprice'];
		$listing_id = $_POST['listing_id'];
		$price_packages = $_POST['packages'];

		$userid = $_POST['userid'];
		$uname = $_POST['uname'];
		$umail = $_POST['umail'];
		$uphone = $_POST['uphone'];
		$uaddress = $_POST['uaddress'];
		$ucity = $_POST['ucity'];
		$ustate = $_POST['ustate'];
		$ucountry = $_POST['ucountry'];
		$uzip = $_POST['uzip'];
		$taxPrice = 0;
		$pkgPrice = 0;
		$current_user = wp_get_current_user();
		$useremail = $current_user->user_email;
		$userDisplayName = $current_user->display_name;
		$userFirstName = $current_user->user_firstname;
		$userLastName = $current_user->user_lastname;

		if(!empty($price_packages)){
			foreach($price_packages as $package){
				$pkgPrice = $pkgPrice + $listingpro_options["$package"];
			}
		}
		if(isset($_POST['taxprice'])){
			if(!empty($_POST['taxprice'])){
				$taxPrice = $_POST['taxprice'];
			}
		}
		$pkgPrice = $pkgPrice + $taxPrice;

		$pesapal_success = $listingpro_options['payment_success'];
		if(!empty($pesapal_success)){
			$pesapal_success = get_permalink($pesapal_success);
		}

		if (isset($listingpro_options['pesapal_consumer_key'])) {
			if (!empty($listingpro_options['pesapal_consumer_key'])) {
				$secid = $listingpro_options['pesapal_consumer_key'];
			}
		}

		if ( isset($listingpro_options['pesapal_consumer_secret']) ) {
			if ( !empty($listingpro_options['pesapal_consumer_secret']) ) {
				$selid = $listingpro_options['pesapal_consumer_secret'];
			}
		}

		$currency = $listingpro_options['currency_paid_submission'];

		Pesapal::setConsumerKey($secid); // Pesapal Consumer Key
		Pesapal::setConsumerSecret($selid); // Pesapal Consumer Secret

		if ( isset($listingpro_options['pesapal_env']) ) {
			if ( !empty($listingpro_options['pesapal_env']) ) {
				if ( $listingpro_options['pesapal_env'] == 'sandbox' ) {
					Pesapal::sandbox(true);
				}
				else{
					Pesapal::sandbox(false);
				}
			}
			else{
				Pesapal::sandbox(true);
			}
		}
		else{
			Pesapal::sandbox(true);
		}

		if ( isset($listingpro_options['pesapal_debug_mode']) ) {
			if ( !empty($listingpro_options['pesapal_debug_mode']) ) {
				if ( $listingpro_options['pesapal_debug_mode'] == 'no' ) {
					Pesapal::setDebugging(false);
				}
				else{
					Pesapal::setDebugging(true);
				}
			}
			else{
				Pesapal::setDebugging(false);
			}
		}
		else{
			Pesapal::setDebugging(false);
		}

		if (isset($listingpro_options['pesapal_order_prefix']) && !empty($listingpro_options['pesapal_order_prefix'])) {
			Pesapal::setOrderPrefix($listingpro_options['pesapal_order_prefix']);
		}

		if (isset($_REQUEST['pesapal_merchant_reference'])) {
			try {
				$logger->add('Veryfying the payment made.', $file);

				//After transaction is done, this redirects back to order received page with the transaction id and the order number
				if (isset($_GET['pesapal_transaction_tracking_id'])) {
					$pesapal_tracking_id = stripslashes($_GET['pesapal_transaction_tracking_id']);
					$pre_order_id        = stripslashes($_GET['pesapal_merchant_reference']);
					$order_id            = Pesapal_Util::removeOrderPrefix($pre_order_id, Pesapal::$orderPrefix);

					//return if $order_id didn't have the appropriate prefix
					if ($order_id == false)
						return;

					// Check status of the order here to get those that fail, or pass immediately
					$status = Pesapal_Query_Payment::getTransactionDetails($pre_order_id, $pesapal_tracking_id)['status'];

					$logger->add('Txn Details at "update_pesapal_transaction": '. json_encode($check_status->getTransactionDetails($pre_order_id, $pesapal_tracking_id)), $file);
					$logger->add('Status at "update_pesapal_transaction": '. $status, $file);

					switch ($status) {
						case 'COMPLETED':
							$transactionId = $pesapal_tracking_id;
							$ads_durations = $listingpro_options['listings_ads_durations'];
							$currentdate = date("d-m-Y");
							$exprityDate = date('Y-m-d', strtotime($currentdate. ' + '.$ads_durations.' days'));
							$exprityDate = date('d-m-Y', strtotime( $exprityDate ));
							$my_post = array(
								'post_title'    => $listing_id,
								'post_status'   => 'publish',
								'post_type' => 'lp-ads',
							);
							$adID = wp_insert_post( $my_post );

							listing_set_metabox('ads_listing', $listing_id, $adID);
							listing_set_metabox('ad_status', 'Active', $adID);
							listing_set_metabox('ad_date', $currentdate, $adID);
							listing_set_metabox('ad_expiryDate', $exprityDate, $adID);
							listing_set_metabox('campaign_id', $adID, $listing_id);
							update_post_meta( $listing_id, 'campaign_status', 'active' );

							$priceKeyArray;
							if( !empty($price_packages) ){
								foreach( $price_packages as $val ){
									$priceKeyArray[] = $val;
									update_post_meta( $listing_id, $val, 'active' );
								}
							}

							if(!empty($priceKeyArray)) {
								listing_set_metabox('ad_type', $priceKeyArray, $adID);
							}

							$tID = $transactionId;
							$token = $token;
							$payment_method = 'pesapal';
							$status = "success";

							$responsed = lp_save_2cheeckout_campaign_data($adID, $tID, $payment_method, $token, $status, $price_packages, $pkgPrice, $listing_id);
							$response = json_encode(array('status'=>'success', 'token'=>$token, 'redirect'=>$pesapal_success));
							$logger->add('Payment Received. Thank you.', $file);
							die($response);
							break;
						case 'PENDING':
							// Update status to 'in progress' as the transaction cannot be complete until PesaPal confirms
							$response = json_encode(array('status'=>'in progress', 'token'=>$token, 'msg' => esc_html('Payment is being processed by Pesapal. We will let you know via email as soon as we are done', 'listingpro')));
							$logger->add('Payment is being processed by Pesapal. We will let you know via email as soon as we are done.', $file);
							die($response);
							break;
						default:
							// Assuming there is an error or the transaction fails, create a fail order note and inform the user on the checkout page
							$response = json_encode(array('status'=>'error', 'token'=>$token, 'msg' => esc_html('Sorry! payment failed.', 'listingpro')));
							$logger->add('Sorry! payment failed.', $file);
							die($response);
							break;
					}
				} else { //If the pesapal_transaction_tracking_id hasn't been set, throw an exception
					$logger->add('PesaPal Transaction Failed: invalid parameters', $file);
					throw new Pesapal_Error("PesaPal Transaction Failed: invalid parameters", "400");
				}
			} catch(Exception $ex) {
				$logger->add('An unexpected Error occurred.', $file);
				throw new Pesapal_Error("An unexpected Error occurred", "500");
			}
			$logger->add('End veryfying the payment made.', $file);
		} else {
			$logger->add('Loading Pesapal checkout page...', $file);

			try {
				$merchandorder = rand();
				$data = [
				    'amount' => $pkgPrice,
				    'currency' => $currency,
			        'desc' => 'WAPI? Kenya listing subscription',
			        'type' => 'MERCHANT',
			        'reference' => $listing_id,
			        'first_name' => $userFirstName,
			        'last_name' => $userLastName,
			        'email' => $umail,
			        'phonenumber' => $uphone,
			        'line_items' => [],
				];
				$logger->add('Pesapal payload: ' . $data, $file);
				$checkout = $listingpro_options['payment-checkout'];
				$checkout_url = get_permalink($checkout);
				$perma = '';
				$methodQuery = 'method=pesapal';
				global $wp_rewrite;
				if ($wp_rewrite->permalink_structure == ''){
					$perma = "&";
				} else {
					$perma = "?";
				}
				$callback_url = $checkout_url . $perma . $methodQuery;
				$logger->add('Callback URL: ' . $callback_url, $file);
				$output = Pesapal_Iframe::render($data, $callback_url);

				return $output;
			} catch (Pesapal_Error $e) {
				$response = json_encode(array('status'=>'error', 'token'=>$token, 'msg'=>$e->getMessage()));
				$logger->add('Error Loading Pesapal checkout page!!!', $file);
				die($response);
			}
			$logger->add('Finished loading Pesapal checkout page...', $file);
		}
	}
}
