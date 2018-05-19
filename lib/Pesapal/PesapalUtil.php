<?php

class Pesapal_Util extends Pesapal {
    static function removeOrderPrefix($prefixed_order_id, $prefix='') {
        // If the prefix is not set, return the whole $prefixed_order_id
        if (!isset($prefix) || $prefix == '')
            return $prefixed_order_id;

        $prefix_psn = strpos($prefixed_order_id, $prefix);

        //return false if the order_id didnt have the prefix
        if($prefix_psn === false || $prefix_psn != 0) {
            return false;
        } else {
            return substr($prefixed_order_id, strlen($prefix));
        }
    }
}
