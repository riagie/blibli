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
define('CHROMIUM', ROOT . LIB . '\chromium\chromium.exe');
define('CHROME_DRIVER', ROOT . LIB . '\chromium\chromedriver.exe');

/**
 * API CONFIG CONFIGURATION
*/
define('USER',      'gzymlrnnkprn@spacehotline.com');
define('PASSWORD',  '1Gzymlrnnkprn');
define('VOUCHER',   'BCABLI-OCT22');
define('TELEGRAM',  '5643108304:AAGzVbsx7bA4omnYlrPT2yYAWxCHqXpGzDk');
define('T_ID',      '326540542');

/**
 * API GLOBAL CONFIGURATION
 */
// <- XPATH
define('URL',       "https://www.blibli.com");
define('PRODUCT',   "https://www.blibli.com/backend/content/flashsale/products");
define('PRODUCT_GROUP', "");

define('CHECKOUT',              "//button[@class='blu-btn b-primary btn-checkout']");
define('CHECKOUT_OVER',         "Selesaikan pembayarannya dulu");
define('LOGIN_PRE',             "Masuk ke akunmu");
define('LOGIN_USER',            "//input[@class='form__input login__username']");
define('LOGIN_PASSWORD',        "//input[@class='form__input login__password']");
define('LOGIN_SUBMIT',          "//button[@class='blu-btn b-full-width b-secondary']");
define('LOGIN_OTP',             "Apakah ini beneran kamu?");
define('LOGIN_OTP_SUBMIT',      "//button[@class='blu-btn otp-validation__button b-full-width b-secondary']");
define('PHONE_VERIFICATION',    "Kami sudah siapkan voucher gratis ongkir yang baru bisa dipakai kalau kamu sudah verifikasi.");
define('PHONE_NOTIFICATION',    "//button[@class='blu-btn b-outline b-white']");
define('ADDRESS_VERIFICATION',  "Pilih alamat pengiriman");
define('ADDRESS_SUBMIT',        "//div[@class='address blu']");
define('VOUCHERS_NOTIFICATION', "//div[@class='address blu']");
define('VOUCHERS_PRE',          "//div[@class='promo-box']");
define('VOUCHERS',              "//input[@class='form__input promo-code-box__content__form__input']");
define('VOUCHERS_SUBMIT',       "//button[@class='blu-btn promo-code-box__content__button b-secondary']");
define('SHIPMENT_SUBMIT',       "//button[@class='blu-btn checkout-button b-primary next-btn']");
define('PAYMENT_SUBMIT',        "//b[@class='payment-type-name ng-binding']");
define('PAYMENT_OPTION',        "Transfer melalui virtual account");
define('PAYMENT_SELECT',        "//div[@class='payment-method-selection position-relative ng-scope']");
define('PAYMENT_SELECT_SUBMIT', "//span[@class='payment-option__selection__option__name ng-binding']");
define('PAYMENT_NOW',           "//label[@for='gdn-mbl-submit-checkout']");
define('VALIDATION',            "//body");
define('ELEMENT_LOADING',       "Loading");
// -> XPATH

define('MAX_TIMEOUT',   30);
define('MAX_SLEEP',     30);
define('MAX_TESTING',   '');

define('ERROR',     3); // fatal
define('INFO',      3); // result
define('WARNING',   3); // notif
define('STAT',      3); // time excute
define('TRACE',     3); // role progress excute
define('SPECIAL',   1); // notif to email

define('LOGS_ERROR',    'logs/' . date('Ymd') . '-error.log');
define('LOGS_INFO',     'logs/' . date('Ymd') . '-info.log');
define('LOGS_WARNING',  'logs/' . date('Ymd') . '-warning.log');
define('LOGS_STAT',     'logs/' . date('Ymd') . '-stat.log');
define('LOGS_TRACE',    'logs/' . date('Ymd') . '-trace.log');

define('LOG_LEVEL', ERROR | INFO | WARNING | STAT | TRACE);