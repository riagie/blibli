<?php

/**
 * Kumpulan Konstan
 * @package Platform
 * @access public
 * @author 
 * @copyright (c) 2022
 * 
*/

define('VERSION', '1.0.0');

/**
 * API PATH CONFIGURATION
*/
define('ROOT', dirname(dirname(__FILE__)). '/' );
define("LIB", "lib/");
define('CACHE', ROOT . LIB . '\chromium\cache');
define('DRIVER', 'chromium_');
define('CHROMIUM', ROOT . LIB . '\chromium\chromium_##.exe');
define('CHROME_DRIVER', ROOT . LIB . '\chromium\chromedriver.exe');

/**
 * API CONFIG CONFIGURATION
*/
define('ACCOUNT', serialize(
    array (
        // 'wvlrzntcm@spacehotline.com|1Wvlrzntcm'
        'ogie.nurdiana447@gmail.com|0913dian'
    )
));
define('VOUCHER', 'KODEVOUCHER22');
define('TELEGRAM', '5729973270:AAG7F1kS33JDFyoQFY1LGOcmDUhfFybDvr8');
define('T_ID', '326540542');

/**
 * API GLOBAL URL
 */
define('BASE_URL', "https://www.blibli.com");
define('FLASHSALE', "https://www.blibli.com/backend/content/flashsale/products");
define('GROUP', "");
define('PRODUCT', "https://www.blibli.com/p/ultra-voucher-digital-code-rp100-000/ps--VOC-45551-00317?ds=VOC-45551-00317-00001&source=SEARCH&sid=ad58c772385e4cb3&cnc=false&pickupPointCode=PP-3013654&pid=VOC-45551-00317&tag=trending");
define('QTY', 50);
define('KartuKreditDebit', 1);

/**
 * API GLOBAL CONFIGURATION
 */
define('MAX_TIMEOUT',   30);
define('MAX_SLEEP',     180);
define('MIN_SLEEP',     5);
define('MAX_TESTING',   0);
define('MAX_RETRY',     1);

define('ERROR',     3);
define('INFO',      3);
define('WARNING',   3);
define('STAT',      3);
define('TRACE',     3);
define('SPECIAL',   1);

define('LOGS_ERROR',    'logs/' . date('Ymd') . '-error.log');
define('LOGS_INFO',     'logs/' . date('Ymd') . '-info.log');
define('LOGS_WARNING',  'logs/' . date('Ymd') . '-warning.log');
define('LOGS_STAT',     'logs/' . date('Ymd') . '-stat.log');
define('LOGS_TRACE',    'logs/' . date('Ymd') . '-trace.log');

define('LOG_LEVEL', ERROR | INFO | WARNING | STAT | TRACE);