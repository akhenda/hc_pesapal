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
class Pesapal_Query_Payment {
    public $queryPaymentStatus;
    public $queryPaymentDetails;
    public $queryPaymentStatusByMerchantRef;

    public function __construct() {
        $this->queryPaymentStatus              = '/API/QueryPaymentStatus';
        $this->queryPaymentDetails             = '/API/QueryPaymentDetails';
        $this->queryPaymentStatusByMerchantRef = '/API/QueryPaymentStatusByMerchantRef';
    }

    /**
     * Get Transaction Details
     *
     * @return array
     */
	public static function getTransactionDetails($merchantReference = '', $trackingId = '') {
        try {
            $request    = new Pesapal_Api_Requester();
            $result     = $request->doCall($this->queryPaymentDetails, $merchantReference, $trackingId, $checkByMerchantRef = false);

            $response      = explode(",", $result);
            $responseArray = array(
                'pesapal_transaction_tracking_id' => $response[0],
                'payment_method'                  => $response[1],
                'status'                          => $response[2],
                'pesapal_merchant_reference'      => $response[3]
            );
        } catch(Exception $ex) {
            $responseArray = array(
                'pesapal_transaction_tracking_id' => '',
                'payment_method'                  => '',
                'status'                          => 'ERROR',
                'pesapal_merchant_reference'      => ''
            );
        }

        return $responseArray;
	}

    /**
     * Get Status by Merchant Reference
     *
     * @return string
     */
    public static function checkStatusByMerchantRef($merchantReference = '') {
        $request    = new Pesapal_Api_Requester();
        $result     = $request->doCall($this->queryPaymentStatusByMerchantRef, $merchantReference, $trackingId, $checkByMerchantRef = true;

        return is_a($status, 'Pesapal_Error') ? 'ERROR' : $status;
    }
}
