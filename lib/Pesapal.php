<?php

abstract class Pesapal {
    const VERSION = '0.0.1';
    public static $error;
    public static $debug;
    public static $sandbox;
    public static $orderPrefix;
    public static $consumerKey;
    public static $consumerSecret;
    public static $baseUrl = 'https://www.pesapal.com';

    public static function setConsumerSecret($value = null) {
        self::$consumerSecret = $value;
    }

    public static function setConsumerKey($value = null) {
        self::$consumerKey = $value;
    }

    public static function sandbox($value = null) {
        if ($value == 1 || $value == true) {
            self::$sandbox = true;
            self::$baseUrl = 'https://demo.pesapal.com';
        } else {
            self::$sandbox = false;
            self::$baseUrl = 'https://www.pesapal.com';
        }
    }

    public static function setOrderPrefix($value = 'hc_') {
        self::$orderPrefix = $value;
    }

    public static function setDebugging($value = null) {
        if ($value == 1 || $value == true) {
            self::$debug = true;
        } else {
            self::$debug = false;
        }
    }
}

require(dirname(__FILE__) . '/Pesapal/PesapalApi.php');
require(dirname(__FILE__) . '/Pesapal/PesapalCreateURL.php');
require(dirname(__FILE__) . '/Pesapal/PesapalError.php');
require(dirname(__FILE__) . '/Pesapal/PesapalIframe.php');
require(dirname(__FILE__) . '/Pesapal/PesapalLogger.php');
require(dirname(__FILE__) . '/Pesapal/PesapalPostXML.php');
require(dirname(__FILE__) . '/Pesapal/PesapalQueryPayment.php');
require(dirname(__FILE__) . '/Pesapal/PesapalUtil.php');
