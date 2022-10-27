<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
date_default_timezone_set('Asia/Jakarta');

ini_set('set_time_limit', 300);
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 43200);

define('DEBUG', 1);

use lib\Blibli\Blibli;

require dirname(__FILE__) . '/vendor/autoload.php';
require('inc/config.php');
require('lib/element.php');
require('lib/chromium.php');
require('lib/blibli.php');

$blibli     = new Blibli();
if (PRODUCT != '') {
    $url = urldecode(PRODUCT);
    $url = preg_replace('/\s+/', '+', $url);
    $checkout = $blibli->chromium_init($url, PRODUCT);

    $blibli->taskKill();
    $message = "\ntotal order success 0.\ndata. order null.\n";
    if ($checkout) {
        $message = "\ntotal order success ".$checkout['total'].".\ndata. order ".$checkout['data'].".\n";
    }
    $blibli->telegram($message);
    echo     $message;

    exit;
}

$products   = $blibli->blibli_init();
if (is_array($products)) {
    foreach ($products as $item) {
        if ($item->code == 200) {
            foreach ($item->data as $key => $value) {
                $url = urldecode($value->url);
                $url = preg_replace('/\s+/', '+', $url);
                $checkout = $blibli->chromium_init(BASE_URL.$url, $value->name);
                if (MAX_TESTING != 0 && $key == MAX_TESTING) break 2;
            }
        }
    }

    $blibli->taskKill();
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
