<?php

/**
 * Return the Pesapal iFrame
 *
 *
 * @since      0.0.1
 * @package    hc_Pesapal
 * @author     Joseph Akhenda <akhenda@gmail.com>
 */
class Pesapal_Create_URL extends Pesapal {
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
     * Create the Pesapal iFrame URL
     *
     * @return string
     */
    function create($data = array(), $callback_url = '/') {
        $file = __FILE__;
        $logger = new Pesapal_Logger();

        $logger->add('Creating iFrame URL', $file);

        $order_xml = Pesapal_POST_XML::create($data);

        // Creation of the iframe url
        $pesapal_post_url_suffix = '/API/PostPesapalDirectOrderV4';
        $iframe_src = OAuthRequest::from_consumer_and_token(
            $this->consumer, $this->token, "GET", $pesapal_post_url_suffix, $this->params
        );
        $iframe_src->set_parameter("oauth_callback", $callback_url);
        $iframe_src->set_parameter("pesapal_request_data", $order_xml);
        $iframe_src->sign_request($this->signature_method, $this->consumer, $this->token);

        return $iframe_src;
    }
}
