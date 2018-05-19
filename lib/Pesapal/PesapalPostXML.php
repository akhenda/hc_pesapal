<?php

class Pesapal_POST_XML {
    public static function create($data = array()) {
        $file = __FILE__;
        $logger = new Pesapal_Logger();

        $amount = $data['amount'];
        $currency = $data['currency'];
        $desc = $data['desc'];
        $type = $data['type'];
        $reference = $data['reference'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $email = $data['email'];
        $phonenumber = $data['phonenumber'];
        $line_items = $data['line_items'];

        $post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
          <PesapalDirectOrderInfo
            xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
            xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
            Amount=\"" . $amount . "\"
            Currency=\"" . $currency . "\"
            Description=\"" . $desc . "\"
            Type=\"" . $type . "\"
            Reference=\"" . $reference . "\"
            FirstName=\"" . $first_name . "\"
            LastName=\"" . $last_name . "\"
            Email=\"" . $email . "\"
            PhoneNumber=\"" . $phonenumber."\"
            xmlns=\"http://www.pesapal.com\"";

        if (count($line_items) > 0) {
          $post_xml .= "><LineItems>";
          foreach ($line_items as $item) {
            $post_xml .= "<LineItem
              UniqueId=\"" . $item['product_id'] . "\"
              Particulars=\"" . $item['name'] . "\"
              Quantity=\"" . $item['qty'] . "\"
              UnitCost=\"". $item['line_subtotal'] . "\"
              SubTotal=\"". $item['line_total']."\" />";
          }
          $post_xml .= "</LineItems></PesapalDirectOrderInfo>";
        } else {
          $post_xml .= " />";
        }

        $logger->add('About to finish Pesapal XML generation', $file);

        return htmlentities($post_xml);
    }
}
