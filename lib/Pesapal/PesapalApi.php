<?php

/**
 * Pesapal API Requester
 *
 * This class does Pesapal API calls.
 *
 * @since      0.0.1
 * @package    hc_Pesapal
 * @subpackage hc_Pesapal/api
 * @author     Joseph Akhenda <akhenda@gmail.com>
 */
class Pesapal_Api_Requester {
    public $token;
    public $params;
    public $baseUrl;
    public $consumer;
    public $signature_method;


	function __construct() {
        $this->token            = $this->params = NULL;
        $this->baseUrl          = Pesapal::$baseUrl;
        $this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
        $this->consumer         = new OAuthConsumer(Pesapal::$consumerKey, Pesapal::$consumerSecret);
    }

    /**
     * Make the API Call
     *
     * @return ARRAY
     */
	function doCall($urlSuffix, $merchantReference, $trackingId, $checkByMerchantRef = false) {
        $request_status = OAuthRequest::from_consumer_and_token(
            $this->consumer,
            $this->token,
            "GET",
            $this->baseUrl . $urlSuffix,
            $this->params
        );

        $request_status->set_parameter("pesapal_merchant_reference", $merchantReference);
        if ($checkByMerchantRef == false) {
            $request_status->set_parameter("pesapal_transaction_tracking_id", $trackingId);
        }
        $request_status->sign_request($this->signature_method, $this->consumer, $this->token);

        try {
            if (in_array('curl', get_loaded_extensions())) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $request_status);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                if (defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True') {
                    $proxy_tunnel_flag = (
                        defined('CURL_PROXY_TUNNEL_FLAG')
                        && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE'
                    ) ? false : true;
                    curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
                    curl_setopt ($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    curl_setopt ($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
                }

                $response    = curl_exec($ch);

                if ($response === FALSE) {
                    throw new Pesapal_Error("cURL call failed", "403");
                } else {
                    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                    $raw_header  = substr($response, 0, $header_size - 4);
                    $headerArray = explode("\r\n\r\n", $raw_header);
                    $header      = $headerArray[count($headerArray) - 1];

                    // transaction status
                    $elements = preg_split("/=/", substr($response, $header_size));
                    $pesapal_response_data = $elements[1];
                }
            } else {
                return new Pesapal_Error('Curl appears to be disabled on your server.', "500");
            }
        } catch(Exception $ex) {
            $pesapal_response_data = 'ERROR';
        }

        return $pesapal_response_data;
	}
}
