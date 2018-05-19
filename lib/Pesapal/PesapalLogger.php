<?php

class Pesapal_Logger extends Pesapal {
	public $file;

	function __construct($logFile = "pesapal.log") {
        $this->file = $logFile;
    }

    function add($message, $file, $level = 'information') {
    	$date = date("Y-m-d h:m:s");
    	$output = "[{$date}] ";
    	$output .= "[{$file}] ";
    	$output .= "[{$level}] ";
    	$output .= "[{$message}]";
        $output .= PHP_EOL;

        if (Pesapal::$debug == true || Pesapal::$debug == 1) {
            return file_put_contents($this->file, $output, FILE_APPEND);
        } else {
            return file_put_contents($this->file, 'Enable logging in Configs', FILE_APPEND);
        }
    }
}
