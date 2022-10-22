<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Jakarta');

ini_set('set_time_limit', 300);
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 43200);

define('DEBUG', 0);

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
                $checkout = $blibli->chromium_init($value->url, $value->name);
                if (MAX_TESTING != '' && $key == MAX_TESTING) break 2;
            }
        }
    }

    $blibli->taskkill();
    $message = "\ntotal order success 0.\ndata. order null.\n";
    if ($checkout) {
        $message = "\ntotal order success ".$checkout['total'].".\ndata. order ".$checkout['data'].".\n";
    }
    $blibli->telegram($message);
    echo     $message;

    exit;
}

$message = "data blibli_init error ".$products.".\n";
$blibli->telegram($message);
echo     $message;
