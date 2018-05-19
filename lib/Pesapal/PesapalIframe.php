<?php

/**
 * Return the Pesapal Checkout HTML
 *
 *
 * @since      0.0.1
 * @package    hc_Pesapal
 * @author     Joseph Akhenda <akhenda@gmail.com>
 */
class Pesapal_Iframe {
    /**
     * Render the Pesapal iFrame
     *
     * @return HTML
     */
    public static function render($data = array(), $callback_url = '/') {
        $request_url = new Pesapal_Create_URL();
        $pesapal_url = $request_url->create($data, $callback_url);
        // $pesapal_url = Pesapal_Create_URL::create($data, $callback_url);

        $output = '
            <div class="pesapal_container" style="position: relative;">
              <img class="pesapal_loading_preloader" src="<?php echo plugin_dir_url(__FILE__); ?>../../public/images/preloader.svg" alt="loading" style="position: absolute;" />
              <iframe class="pesapal_loading_frame" src="<?php echo $pesapal_url; ?>" width="100%" height="700px"  scrolling="yes" frameBorder="0">
                <p><?php echo "Browser unable to load iFrame"; ?></p>
              </iframe>
            </div>
            <script>
              jQuery(document).ready(function () {
                jQuery(".pesapal_loading_frame").on("load", function () {
                  jQuery(".pesapal_loading_preloader").hide();
                });
              });
            </script>
        ';

        return $output;
    }
}
