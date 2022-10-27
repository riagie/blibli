<?php

// URL PRODUCT TRUE
define('ADD_TO_BAG',    "//button[@class='blu-btn b-secondary btn-add-to-bag']");
define('BAG_IN',        "//button[@class='blu-btn b-icon b-secondary add-btn mode-small']");
define('CART',          "https://www.blibli.com/cart");
define('CART_QTY',      "//input[@class='quantity-input--field']");
define('CART_CHECKOUT', "//button[@class='blu-btn cart-footer__summary__button b-primary']");

// FLASHSALE
define('CHECKOUT',      "//button[@class='blu-btn b-primary btn-checkout']");
define('OUT_OF_STOCK',  "Stok habis");
define('OUT_OF_READY',  "Beritahu saya");
define('CHECKOUT_OVER', "Selesaikan pembayarannya dulu");

// XPATH
define('LOGIN',         "Masuk ke akunmu");
define('USER',          "//input[@class='form__input login__username']");
define('PASSWORD',      "//input[@class='form__input login__password']");
define('LOGIN_IN',      "//button[@class='blu-btn b-full-width b-secondary']");
define('OTP',           "Apakah ini beneran kamu?");
define('OTP_IN',        "//button[@class='blu-btn otp-validation__button b-full-width b-secondary']");
define('LOGIN_ERR',     "nomor HP-nya tidak valid");
define('LOGIN_ERR2',    "Yakin itu benar? Coba diingat lagi");
define('LOGIN_ERR3',    "Akunmu terkunci karena mencoba masuk dengan informasi yang salah berulang kali");

define('PHONE_ERR',     "Verifikasi nomor HP dulu, ya, biar dapat voucher gratis ongkir!");
define('PHONE_ERR2',    "Oke");
define('PHONE_IN',      "//button[@class='blu-btn b-outline b-white']");
define('PHONE_PASS',    "Nanti saja");
define('PHONE_PASS_IN', "//button[@class='blu-btn footer__btn b-ghost b-secondary']");

define('ADDRESS_ERR',   "Pilih alamat pengiriman");
define('ADDRESS_IN',    "//div[@class='address blu']");

define('VOUCHERS_ERR',  "Pakai voucher/kode promo");
define('VOUCHERS_ERR2', "voucher/kode promo terpakai");
define('VOUCHERS_IN',   "//div[@class='promo-box']");
define('VOUCHERS',      "//input[@class='form__input promo-code-box__content__form__input']");
define('PAKAI',         "//button[@class='blu-btn promo-code-box__content__button b-secondary']");

define('SHIPMENT_ERR',  "Lanjut");
define('SHIPMENT_IN',   "//button[@class='blu-btn checkout-button b-primary next-btn']");

define('PAYMENT_METHOD_ERR',    "Transfer melalui virtual account");
define('PAYMENT_METHOD_IN',     "//b[@class='payment-type-name ng-binding']");
define('PAYMENT_METHOD_SELECT', "//div[@class='payment-method-selection position-relative ng-scope']");
define('PAYMENT_SELECT_IN',     "//span[@class='payment-option__selection__option__name ng-binding']");

define('PAYMENT_ERR',   "Bayar");
define('PAYMENT_IN',    "//label[@for='gdn-mbl-submit-checkout']");

define('THANKYOU',  "Cek status pembayaran");

define('VALIDATION',    "//body");
define('LOADING',       "Loading");