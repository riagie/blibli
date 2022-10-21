<?php

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Jakarta');

ini_set('set_time_limit', 300);
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 43200);

define('DEBUG', 1);

use lib\Blibli\Blibli;

require dirname(__FILE__) . '/vendor/autoload.php';
require('inc/config.php');
require('lib/chromium.php');
require('lib/blibli.php');

$blibli     = new Blibli();
$products   = $blibli->blibli_init();

if (is_array($products)) {
    foreach ($products as $item) {
        if ($item->code == 200) {
            foreach ($item->data as $key => $value) {
                try {
                    if (!$blibli->chromium_init($value->url, $value->name)) {
                        $message = "sorry, automation stoped.\n";
                        $blibli->telegram($message);
                        echo     $message;
                        break 2;
                    }

                    $number[]   = $value->name;
                    $message    = "order success ".$value->name."\n";
                    $blibli->telegram($message);
                    echo    $message;
                } catch (Exception $error) {
                    $message = "order failed ".$value->name." description ".$error->getMessage().".\n";
                    $blibli->chrome_driver_close();
                    $blibli->taskkill();
                    $blibli->telegram($message);
                    echo     $message;
                }
                if (MAX_TESTING != '' && $key == MAX_TESTING) break 2;
            }
        }
    }

    $blibli->chrome_driver_close();
    $blibli->taskkill();

    $message = "\ntotal order success ".@count($number).".\ndata. order ".$blibli->Json('encode', @$number).".\n";
    $blibli->telegram($message);
    echo     $message;

    exit;
}

$message = "data blibli_init error ".$products.".\n";
$blibli->telegram($message);
echo     $message;
